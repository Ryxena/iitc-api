<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaymentStatus as PaymentStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Member;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTeamRecapController extends Controller
{
    public function index(Request $request): View
    {
        $allEvents = Event::query()->orderByDesc('created_at')->get();
        $activeEvent = Event::query()->where('is_active', true)->first();

        $selectedEventId = $request->query('event_id');
        if (! $selectedEventId) {
            $selectedEventId = $activeEvent?->id;
        }

        // Get competitions for the selected event
        $competitions = $selectedEventId
            ? Competition::query()->where('event_id', $selectedEventId)->orderBy('name')->get()
            : Competition::query()->orderBy('name')->get();

        $competitionIds = $competitions->pluck('id');

        $status = $request->query('status', 'ALL');
        $competitionId = $request->query('competition_id', 'ALL');
        $search = trim($request->query('search', ''));

        // Stats calculation
        $baseTeamsQuery = Team::query()->whereIn('competition_id', $competitionIds);

        $totalTeamsCount = (clone $baseTeamsQuery)->count();

        $validatedCount = (clone $baseTeamsQuery)
            ->whereHas('paymentStatus', fn ($q) => $q->where('status', PaymentStatusHelper::VALID))
            ->count();

        $pendingCount = (clone $baseTeamsQuery)
            ->where(function ($q) {
                $q->whereHas('paymentStatus', fn ($q2) => $q2->where('status', PaymentStatusHelper::PENDING))
                    ->orWhereDoesntHave('paymentStatus');
            })->count();

        $invalidCount = (clone $baseTeamsQuery)
            ->whereHas('paymentStatus', fn ($q) => $q->where('status', PaymentStatusHelper::INVALID))
            ->count();

        $teamIdsForEvent = (clone $baseTeamsQuery)->pluck('id');
        $totalMembersCount = Member::query()->whereIn('team_id', $teamIdsForEvent)->count();
        $totalParticipants = $totalTeamsCount + $totalMembersCount;

        // Competition breakdown
        $competitionBreakdown = Competition::query()
            ->whereIn('id', $competitionIds)
            ->withCount([
                'teams',
                'teams as validated_teams_count' => fn ($q) => $q->whereHas('paymentStatus', fn ($q2) => $q2->where('status', PaymentStatusHelper::VALID)),
            ])
            ->get();

        // Datatable query
        $query = Team::query()
            ->whereIn('competition_id', $competitionIds)
            ->with([
                'competition',
                'leader',
                'leader.participant',
                'members',
                'members.participant',
                'payment',
                'paymentStatus',
            ])
            ->withCount('members');

        if ($competitionId !== 'ALL') {
            $query->where('competition_id', $competitionId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('competition', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('leader', fn ($q2) => $q2->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status !== 'ALL') {
            if ($status === PaymentStatusHelper::PENDING) {
                $query->where(function ($q) {
                    $q->whereHas('paymentStatus', fn ($q2) => $q2->where('status', PaymentStatusHelper::PENDING))
                        ->orWhereDoesntHave('paymentStatus');
                });
            } else {
                $query->whereHas('paymentStatus', fn ($q) => $q->where('status', $status));
            }
        }

        $teams = $query->latest()->paginate(20)->withQueryString();

        return view('admin.teams.recap', compact(
            'allEvents',
            'activeEvent',
            'selectedEventId',
            'competitions',
            'totalTeamsCount',
            'validatedCount',
            'pendingCount',
            'invalidCount',
            'totalParticipants',
            'competitionBreakdown',
            'teams',
            'status',
            'competitionId',
            'search'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $exportType = $request->query('export_type', 'teams'); // 'teams' | 'participants'
        $selectedEventId = $request->query('event_id');

        if (! $selectedEventId) {
            $activeEvent = Event::query()->where('is_active', true)->first();
            $selectedEventId = $activeEvent?->id;
        }

        $competitions = $selectedEventId
            ? Competition::query()->where('event_id', $selectedEventId)->pluck('id')
            : Competition::query()->pluck('id');

        $status = $request->query('status', 'ALL');
        $competitionId = $request->query('competition_id', 'ALL');
        $search = trim($request->query('search', ''));

        $query = Team::query()
            ->whereIn('competition_id', $competitions)
            ->with([
                'competition',
                'leader',
                'leader.participant',
                'members',
                'members.participant',
                'payment',
                'paymentStatus',
            ])
            ->withCount('members');

        if ($competitionId !== 'ALL') {
            $query->where('competition_id', $competitionId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('competition', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('leader', fn ($q2) => $q2->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status !== 'ALL') {
            if ($status === PaymentStatusHelper::PENDING) {
                $query->where(function ($q) {
                    $q->whereHas('paymentStatus', fn ($q2) => $q2->where('status', PaymentStatusHelper::PENDING))
                        ->orWhereDoesntHave('paymentStatus');
                });
            } else {
                $query->whereHas('paymentStatus', fn ($q) => $q->where('status', $status));
            }
        }

        $teams = $query->oldest()->get();

        $filenameSuffix = match ($exportType) {
            'participants_only' => 'peserta-lomba-individu',
            'participants' => 'peserta-lomba-roster',
            default => 'recap-tim-lomba',
        };
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filenameSuffix.'-'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function () use ($teams, $exportType) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

            if ($exportType === 'participants_only') {
                // Individual-only mode: export each person without team columns (original format)
                fputcsv($handle, [
                    'No',
                    'Nama Lengkap',
                    'Email',
                    'Nomor Telepon',
                    'Asal Sekolah',
                ]);

                $index = 1;
                foreach ($teams as $team) {
                    $leader = $team->leader;
                    fputcsv($handle, [
                        $index++,
                        $leader->name ?? '-',
                        $leader->email ?? '-',
                        $leader->phone ?? '-',
                        $leader->participant->institution ?? '-',
                    ]);

                    foreach ($team->members as $member) {
                        fputcsv($handle, [
                            $index++,
                            $member->name ?? '-',
                            $member->email ?? '-',
                            $member->phone ?? '-',
                            $member->participant->institution ?? '-',
                        ]);
                    }
                }
            } elseif ($exportType === 'participants') {
                // Roster mode: export each individual participant (leader & member), grouped by team
                fputcsv($handle, [
                    'No',
                    'Nama Tim',
                    'Tanggal Daftar Tim',
                    'Nama Lengkap',
                    'Peran',
                    'Nomor Telepon',
                    'Asal Sekolah',
                ]);

                $index = 1;
                foreach ($teams as $team) {
                    $teamName = $team->name ?? '-';
                    $teamRegisteredAt = $team->created_at ? $team->created_at->format('d/m/Y H:i') : '-';

                    // 1. Leader
                    $leader = $team->leader;
                    fputcsv($handle, [
                        $index++,
                        $teamName,
                        $teamRegisteredAt,
                        $leader->name ?? '-',
                        'Ketua Tim',
                        $leader->phone ?? '-',
                        $leader->participant->institution ?? '-',
                    ]);

                    // 2. Members
                    foreach ($team->members as $member) {
                        fputcsv($handle, [
                            $index++,
                            $teamName,
                            $teamRegisteredAt,
                            $member->name ?? '-',
                            'Anggota Tim',
                            $member->phone ?? '-',
                            $member->participant->institution ?? '-',
                        ]);
                    }
                }
            } else {
                // Team summary mode
                fputcsv($handle, [
                    'No',
                    'Nama Tim',
                    'Kode Tim',
                    'Kompetisi',
                    'Nama Ketua',
                    'Email Ketua',
                    'No. HP Ketua',
                    'Institusi',
                    'Jumlah Anggota',
                    'Total Peserta',
                    'Status Payment',
                    'Alasan Status',
                    'Submission / Karya',
                    'Tanggal Daftar',
                ]);

                foreach ($teams as $i => $team) {
                    fputcsv($handle, [
                        $i + 1,
                        $team->name ?? '-',
                        $team->code ?? '-',
                        $team->competition->name ?? '-',
                        $team->leader->name ?? '-',
                        $team->leader->email ?? '-',
                        $team->leader->phone ?? '-',
                        $team->leader->participant->institution ?? '-',
                        $team->members_count,
                        $team->members_count + 1,
                        $team->paymentStatus->status ?? 'BELUM UPLOAD',
                        $team->paymentStatus->reason ?? '-',
                        $team->submission ?? $team->submission_file_name ?? '-',
                        $team->created_at ? $team->created_at->format('d/m/Y H:i') : '-',
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id): View
    {
        $team = Team::with([
            'competition',
            'leader.participant',
            'members.participant',
            'payment',
            'paymentStatus',
        ])->withCount('members')->findOrFail($id);

        return view('admin.teams.show', compact('team'));
    }
}
