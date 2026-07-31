<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class LeaveTeamController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth()->user();
        $team = TeamController::findUserTeam($user);

        if (! $team) {
            return $this->error('Team not found.', 404);
        }

        if ($team->leader_id === $user->id) {
            return $this->error('Leader cannot leave the team.', 400);
        }

        $isMember = Member::query()
            ->where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isMember) {
            return $this->error('You are not a member of this team.', 400);
        }

        $user->asMembers()->detach($team->id);

        return $this->success('Succeed left the team', [
            'teamId' => $team->id,
        ]);
    }
}
