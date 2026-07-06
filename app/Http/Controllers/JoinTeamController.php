<?php

namespace App\Http\Controllers;

use App\Exceptions\LeaderJoinOwnTeamException;
use App\Http\Requests\StoreJoinTeamRequest;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;

class JoinTeamController extends Controller
{
    public function store(StoreJoinTeamRequest $request): JsonResponse
    {
        try {
            $code = $request->input('code');
            $team = Team::query()->where('code', $code)->firstOrFail();
            $user = auth()->user();

            // leader joined his own team
            if ($team->leader_id === $user->id) {
                throw new LeaderJoinOwnTeamException('you are the leader!');
            }

            // Business validation: Check if user is already in a team for this competition
            $hasTeamInCompetition = Team::query()->where('competition_id', $team->competition_id)
                ->where(function ($query) use ($user) {
                    $query->where('leader_id', $user->id)
                        ->orWhereHas('members', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                })->exists();
                
            if ($hasTeamInCompetition) {
                return $this->error('You are already a member or leader of a team in this competition.', 400);
            }

            // Business validation: Check team capacity (leader is +1, so total members is members_count + 1)
            $team->loadCount('members');
            $maxMembers = $team->competition->max_members;
            if (($team->members_count + 1) >= $maxMembers) {
                return $this->error('The team has already reached its maximum capacity.', 400);
            }

            $user->asMembers()->syncWithoutDetaching($team->id);

            return $this->success('Succeed joined a team');
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }
}
