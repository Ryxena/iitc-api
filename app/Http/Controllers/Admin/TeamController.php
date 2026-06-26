<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Team;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\Eloquent\Builder;

class TeamController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $eventActive = Event::query()->where('is_active', true)->first();
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

        $responseData = [
            'status' => 1,
            'message' => 'Succeed get all competition',
            'data' => [
                'teams' => $teams,
            ],
        ];

        return response()->json($responseData, 200);
    }

    public function show(string $teamId)
    {
        if (!auth()->user()->hasRole('Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $team = Team::query()->with([
            'paymentStatus',
            'payment',
            'leader',
            'leader.participant:avatar',
            'members:id,name,email',
            'members.participant:user_id,avatar',
            'competition'
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

        $responseData = [
            'status' => 1,
            'message' => 'Succeed get detail team',
            'data' => [
                'team' => $teamResponse,
            ],
        ];

        return response()->json($responseData, 200);
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
            'currentMembers' => $team->members_count,
            'leader' => [
                'name' => $team->leader->name,
                'phone' => $team->leader->phone,
                'email' => $team->leader->email,
                'address' => $team->leader->participant->institution ?? null,
            ],
            'submission' => $team->submission,
        ];
    }
}

