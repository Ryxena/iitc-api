<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DeleteTeamMemberController extends Controller
{
    public function __invoke(string $memberId): JsonResponse
    {
        $user = auth()->user();
        $team = TeamController::findUserTeam($user);

        if (! $team) {
            return $this->error('Team not found.', 404);
        }

        // If member is removing themselves from the team
        if ($user && $user->id === $memberId) {
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
                'memberName' => $user->name,
                'memberId' => $user->id,
            ]);
        }

        // Leader kicking another member
        $this->authorize('delete', $team);

        $member = User::query()->findOrFail($memberId);

        Member::query()->where('team_id', $team->id)
            ->where('user_id', $member->id)
            ->firstOrFail();

        $member->asMembers()->detach($team->id);

        return $this->success('Succeed delete user from team', [
            'teamId' => $team->id,
            'memberName' => $member->name,
            'memberId' => $memberId,
        ]);
    }
}
