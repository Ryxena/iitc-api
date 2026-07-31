<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Member;
use App\Models\SeminarRegistration;
use App\Models\Team;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function teams(): StreamedResponse
    {

        $activeEvent = Event::query()->where('is_active', true)->first();

        $competitionIds = $activeEvent
            ? Competition::query()->where('event_id', $activeEvent->id)->pluck('id')
            : collect();

        $teams = Team::query()
            ->whereIn('competition_id', $competitionIds)
            ->with(['competition', 'leader', 'leader.participant', 'paymentStatus'])
            ->withCount('members')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pendaftar-lomba-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($teams) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No',
                'Nama Tim',
                'Kompetisi',
                'Kode Tim',
                'Nama Ketua',
                'Email Ketua',
                'No. HP Ketua',
                'Institusi',
                'Jumlah Anggota',
                'Status Payment',
                'Tanggal Daftar',
            ]);

            foreach ($teams as $i => $team) {
                fputcsv($handle, [
                    $i + 1,
                    $team->name ?? '-',
                    $team->competition->name ?? '-',
                    $team->code ?? '-',
                    $team->leader->name ?? '-',
                    $team->leader->email ?? '-',
                    $team->leader->phone ?? '-',
                    $team->leader->participant->institution ?? '-',
                    $team->members_count + 1,
                    $team->paymentStatus->status ?? 'BELUM UPLOAD',
                    $team->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function seminars(): StreamedResponse
    {
        $registrations = SeminarRegistration::query()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="peserta-seminar-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($registrations) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['No', 'Nama', 'TTD']);

            foreach ($registrations as $i => $reg) {
                fputcsv($handle, [
                    $i + 1,
                    $reg->user->name ?? '-',
                    '', // blank TTD column for physical signature
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
