<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateNumber;
use App\Models\Event;
use App\Services\CertificateNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCertificateController extends Controller
{
    public function __construct(private readonly CertificateNumberService $numbers) {}

    /**
     * List every finalist (D) and participant (F) certificate number of the
     * selected event, with the per-event numbering settings.
     */
    public function index(Request $request): View
    {
        $events = Event::query()->orderByDesc('created_at')->get();
        $activeEvent = Event::query()->where('is_active', true)->first();

        $event = $events->firstWhere('id', (int) $request->query('event_id')) ?? $activeEvent ?? $events->first();

        $rows = collect();
        $stats = ['total' => 0, 'assigned' => 0, 'finalist' => 0, 'participant' => 0];

        if ($event) {
            $rows = CertificateNumber::query()
                ->with(['user.participant', 'team.competition', 'team.winner'])
                ->whereHas('team.competition', fn ($q) => $q->where('event_id', $event->id))
                ->get()
                // Urut: nama kompetisi → finalis (rank 1,2,3,…) → partisipan → nomor.
                ->sortBy(fn (CertificateNumber $r) => [
                    Str::lower($r->team?->competition?->name ?? ''),
                    $r->type === CertificateNumber::TYPE_FINALIST ? 0 : 1,
                    (int) ($r->team?->winner?->rank ?? PHP_INT_MAX),
                    $r->team_id,
                    $r->sequence ?? PHP_INT_MAX,
                    $r->id,
                ])
                ->values();

            $stats = [
                'total' => $rows->count(),
                'assigned' => $rows->whereNotNull('sequence')->count(),
                'finalist' => $rows->where('type', CertificateNumber::TYPE_FINALIST)->count(),
                'participant' => $rows->where('type', CertificateNumber::TYPE_PARTICIPANT)->count(),
            ];
        }

        return view('admin.certificates.index', [
            'event' => $event,
            'events' => $events,
            'rows' => $rows,
            'stats' => $stats,
            'suffix' => $event ? $this->numbers->suffix($event) : CertificateNumberService::DEFAULT_SUFFIX,
            'startD' => $event ? $this->numbers->start($event, CertificateNumber::TYPE_FINALIST) : CertificateNumberService::DEFAULT_START[CertificateNumber::TYPE_FINALIST],
            'startF' => $event ? $this->numbers->start($event, CertificateNumber::TYPE_PARTICIPANT) : CertificateNumberService::DEFAULT_START[CertificateNumber::TYPE_PARTICIPANT],
            'maxD' => $event ? $this->numbers->currentMax($event, CertificateNumber::TYPE_FINALIST) : 0,
            'maxF' => $event ? $this->numbers->currentMax($event, CertificateNumber::TYPE_PARTICIPANT) : 0,
        ]);
    }

    /**
     * Save the suffix segment and the starting numbers for an event.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'suffix' => ['required', 'string', 'max:255', 'regex:~^[A-Za-z0-9._\-/]+$~'],
            'start_finalist' => 'required|integer|min:1|max:999999',
            'start_participant' => 'required|integer|min:1|max:999999',
        ]);

        $event = Event::findOrFail($data['event_id']);

        $this->numbers->setSuffix($event, $data['suffix']);
        $this->numbers->setStart($event, CertificateNumber::TYPE_FINALIST, (int) $data['start_finalist']);
        $this->numbers->setStart($event, CertificateNumber::TYPE_PARTICIPANT, (int) $data['start_participant']);

        return redirect()->back()->with('success', 'Pengaturan nomor sertifikat tersimpan.');
    }

    /**
     * Build the D/F roster for the event and fill in missing sequence numbers.
     */
    public function generate(Request $request): RedirectResponse
    {
        $data = $request->validate(['event_id' => 'required|exists:events,id']);

        $event = Event::findOrFail($data['event_id']);

        $created = $this->numbers->sync($event);
        $assigned = $this->numbers->assign($event);

        return redirect()->back()->with('success', "Roster diperbarui: {$created} orang ditambahkan, {$assigned} nomor ditetapkan.");
    }

    /**
     * Renumber every row of one type from its configured start.
     */
    public function renumber(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'type' => 'required|in:D,F',
        ]);

        $event = Event::findOrFail($data['event_id']);
        $count = $this->numbers->renumber($event, $data['type']);

        $label = $data['type'] === CertificateNumber::TYPE_FINALIST ? 'finalis (D)' : 'partisipan (F)';

        return redirect()->back()->with('success', "Nomor {$label} diurutkan ulang: {$count} orang.");
    }

    /**
     * Override the number of a single person.
     */
    public function updateNumber(Request $request, CertificateNumber $certificateNumber): RedirectResponse
    {
        $data = $request->validate([
            'sequence' => ['required', 'integer', 'min:1', 'max:999999',
                'unique:certificate_numbers,sequence,'.$certificateNumber->id.',id,type,'.$certificateNumber->type,
            ],
        ]);

        $certificateNumber->update(['sequence' => (int) $data['sequence']]);

        return redirect()->back()->with('success', 'Nomor diperbarui.');
    }

    /**
     * CSV export: name + official certificate number only.
     */
    public function export(Request $request): StreamedResponse
    {
        $event = Event::query()->where('is_active', true)->first()
            ?? Event::query()->orderByDesc('created_at')->first();

        $rows = $event
            ? CertificateNumber::query()
                ->with(['user', 'team.competition.event'])
                ->whereHas('team.competition', fn ($q) => $q->where('event_id', $event->id))
                ->whereNotNull('sequence')
                ->orderBy('type')
                ->orderBy('sequence')
                ->get()
            : collect();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="nomor-sertifikat-'.now()->format('Y-m-d').'.csv"',
        ];

        $numbers = $this->numbers;

        $callback = function () use ($rows, $numbers) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Nama', 'Nomor Surat']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->user->name ?? '-',
                    $numbers->format($row),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
