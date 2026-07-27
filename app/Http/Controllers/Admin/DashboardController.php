<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaymentStatus as PaymentStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Member;
use App\Models\PaymentStatus;
use App\Models\SeminarRegistration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activeEvent = Event::query()->where('is_active', true)->first();

        $competitionIds = collect();
        $competitions   = collect();

        if ($activeEvent) {
            $competitions   = Competition::query()
                ->where('event_id', $activeEvent->id)
                ->withCount(['teams', 'teams as teams_with_payment_count' => function ($q) {
                    $q->whereHas('payment');
                }])
                ->get();
            $competitionIds = $competitions->pluck('id');
        }

        // Payment status counts (team/lomba)
        $paymentCounts = PaymentStatus::query()
            ->whereIn('team_id', function ($q) use ($competitionIds) {
                $q->select('id')
                    ->from('teams')
                    ->whereIn('competition_id', $competitionIds->toArray());
            })
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $pendingCount = $paymentCounts[PaymentStatusHelper::PENDING] ?? 0;
        $validCount   = $paymentCounts[PaymentStatusHelper::VALID] ?? 0;
        $invalidCount = $paymentCounts[PaymentStatusHelper::INVALID] ?? 0;

        // Teams without any payment status (uploaded proof but no status yet)
        $teamsWithPaymentNoStatus = Team::query()
            ->whereIn('competition_id', $competitionIds->toArray())
            ->whereHas('payment')
            ->whereDoesntHave('paymentStatus')
            ->count();

        $pendingCount += $teamsWithPaymentNoStatus;

        // Total unique members (leaders + members across all teams in active event)
        $totalTeams = Team::query()
            ->whereIn('competition_id', $competitionIds->toArray())
            ->count();

        $totalMembers = Member::query()
            ->whereIn('team_id', function ($q) use ($competitionIds) {
                $q->select('id')
                    ->from('teams')
                    ->whereIn('competition_id', $competitionIds->toArray());
            })
            ->count();

        // Seminar participants (users registered for the seminar)
        $seminarParticipants = SeminarRegistration::count();
        // Timeline: teams registered per day last 30 days
        $timeline = Team::query()
            ->whereIn('competition_id', $competitionIds->toArray())
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn ($r) => $r->count);

        // Fill missing days with 0
        $labels    = [];
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date        = now()->subDays($i)->format('Y-m-d');
            $labels[]    = now()->subDays($i)->format('d M');
            $chartData[] = $timeline[$date] ?? 0;
        }


        return view('admin.dashboard', compact(
            'activeEvent',
            'competitions',
            'totalTeams',
            'totalMembers',
            'seminarParticipants',
            'pendingCount',
            'validCount',
            'invalidCount',
            'labels',
            'chartData',
        ));
    }
}
