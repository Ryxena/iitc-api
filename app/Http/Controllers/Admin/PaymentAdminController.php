<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaymentStatus as PaymentStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentAdminController extends Controller
{
    public function index(Request $request): View
    {
        $activeEvent = Event::query()->where('is_active', true)->first();

        $competitionIds = $activeEvent
            ? Competition::query()->where('event_id', $activeEvent->id)->pluck('id')
            : collect();

        $status = $request->query('status', 'ALL');
        $search = $request->query('search', '');

        $query = Team::query()
            ->whereIn('competition_id', $competitionIds)
            ->with(['competition', 'leader', 'payment', 'paymentStatus'])
            ->withCount('members');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('competition', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('leader', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status !== 'ALL') {
            if ($status === PaymentStatusHelper::PENDING) {
                // Teams with PENDING status OR payment uploaded but no status yet
                $query->where(function ($q) {
                    $q->whereHas('paymentStatus', fn ($q2) => $q2->where('status', PaymentStatusHelper::PENDING))
                        ->orWhereDoesntHave('paymentStatus');
                });
            } else {
                $query->whereHas('paymentStatus', fn ($q) => $q->where('status', $status));
            }
        }

        $teams = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('teams', 'status', 'search', 'activeEvent'));
    }

    public function show(string $teamId): View
    {
        $team = Team::query()->with([
            'competition',
            'leader',
            'leader.participant',
            'members',
            'members.participant',
            'payment',
            'paymentStatus',
        ])->findOrFail($teamId);

        return view('admin.payments.show', compact('team'));
    }

    public function update(Request $request, string $teamId): RedirectResponse
    {
        $request->validate([
            'is_approve' => ['required', 'in:1,0'],
            'reason'     => ['nullable', 'string', 'max:500'],
        ]);

        $team = Team::query()->findOrFail($teamId);

        // Ensure payment proof exists
        $paymentExists = Payment::query()->where('team_id', $team->id)->exists();
        if (! $paymentExists) {
            return redirect()->back()->with('error', 'Tidak ada bukti pembayaran untuk tim ini.');
        }

        $isApprove = (bool) $request->input('is_approve');
        $status    = $isApprove ? PaymentStatusHelper::VALID : PaymentStatusHelper::INVALID;

        PaymentStatus::query()->updateOrCreate(
            ['team_id' => $team->id],
            [
                'team_id' => $team->id,
                'status'  => $status,
                'reason'  => $request->input('reason') ?? '',
            ]
        );

        $message = $isApprove
            ? "Payment tim \"{$team->name}\" berhasil diverifikasi."
            : "Payment tim \"{$team->name}\" ditolak.";

        return redirect()->route('admin.payments.index')->with('success', $message);
    }
}
