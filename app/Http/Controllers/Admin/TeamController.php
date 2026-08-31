<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TeamController extends Controller
{
    public function index()
    {
        if (! auth()->user()->hasRole('Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $eventActive = Event::query()->where('is_active', true)->first();
        if (! $eventActive) {
            return $this->error('No active event found.', 404);
        }

        $competitionIds = Competition::query()->where('event_id', $eventActive->id)->pluck('id');
        $queryTeams = Team::query()
            ->whereIn('competition_id', $competitionIds)
            ->whereHas('paymentStatus', function (Builder $query) {
                $query->where('status', '=', 'VALID');
            })
            ->withCount('members')
            ->with([
                'competition',
                'leader',
            ])->get();

        $teams = [];
        foreach ($queryTeams as $team) {
            $teams[] = $this->transformDBToResponseTeam($team);
        }

        return $this->success('Succeed get all competition.', ['teams' => $teams]);
    }

    public function show(string $teamId)
    {
        if (! auth()->user()->hasRole('Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $team = Team::query()->with([
            'paymentStatus',
            'payment',
            'leader',
            'leader.participant:avatar',
            'members:id,name,email',
            'members.participant:user_id,avatar',
            'competition',
        ])->findOrFail($teamId);

        $paymentStatus = isset($team->payment) ? PaymentStatus::PENDING : null;
        $paymentStatus = $team->paymentStatus->status ?? $paymentStatus;

        $teamResponse = [
            'id' => $team->id,
            'name' => $team->name,
            'code' => $team->code,
            'title' => $team->title,
            'isActive' => $paymentStatus,
            'isSubmit' => isset($team->submission),
            'avatar' => $team->avatar,
            'leader' => [
                'name' => $team->leader->name,
                'email' => $team->leader->email,
                'avatar' => $team->leader->participant->avatar ?? null,
            ],
            'members' => $team->members,
        ];

        return $this->success('Succeed get detail team.', ['team' => $teamResponse]);
    }

    private function transformDBToResponseTeam(Team $team): array
    {
        return [
            'teamId' => $team->id,
            'competitionName' => $team->competition->name,
            'cSlug' => $team->competition->slug,
            'teamName' => $team->name,
            'avatar' => $team->avatar,
            'isSubmit' => isset($team->submission),
            'maxMembers' => $team->competition->max_members,
            'currentMembers' => $team->members_count + 1,
            'leader' => [
                'name' => $team->leader->name,
                'phone' => $team->leader->phone,
                'email' => $team->leader->email,
                'address' => $team->leader->participant->institution ?? null,
            ],
            'submission' => $team->submission,
        ];
    }

    public function uploadAvatar(Request $request, string $teamId): JsonResponse
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048']);

        $team = Team::query()->findOrFail($teamId);

        if ($team->avatar) {
            $oldPath = str_replace(Storage::disk('public')->url(''), '', $team->avatar);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('avatar')->store('team/avatar', 'public');
        $team->update(['avatar' => Storage::disk('public')->url($path)]);

        return $this->success('Team avatar uploaded successfully.', ['avatar' => $team->avatar]);
    }

    public function updateName(Request $request, string $teamId): JsonResponse
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $request->validate(['name' => 'required|string|max:255']);

        $team = Team::query()->findOrFail($teamId);
        $team->update(['name' => $request->name]);

        return $this->success('Team name updated successfully.', [
            'team' => ['id' => $team->id, 'name' => $team->name],
        ]);
    }
}
