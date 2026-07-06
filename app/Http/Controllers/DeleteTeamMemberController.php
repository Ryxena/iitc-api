<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DeleteTeamMemberController extends Controller
{
    public function __invoke(string $teamId, string $memberId): JsonResponse
    {
        $team = Team::query()->findOrFail($teamId);
        $this->authorize('delete', $team);

        $member = User::query()->findOrFail($memberId);

        Member::query()->where('team_id', $team->id)
            ->where('user_id', $member->id)
            ->firstOrFail();

        $member->asMembers()->detach($team->id);

        return $this->success('Succeed delete user from team', [
            'teamId'   => $teamId,
            'memberId' => $memberId,
        ]);
    }
}
