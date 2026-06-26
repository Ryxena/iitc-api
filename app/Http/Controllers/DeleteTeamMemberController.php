<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteTeamMemberController extends Controller
{
    public function __invoke(string $teamId, string $memberId): JsonResponse
    {
        $this->authorize('delete', Team::query()->find($teamId));
        $team = Team::query()->findOrFail($teamId);
        $member = User::query()->findOrFail($memberId);

        Member::query()->where('team_id', $team->id)
            ->where('user_id', $member->id)
            ->firstOrFail();

        $member->asMembers()->detach($team->id);

        $responseData = [
            'status' => 1,
            'message' => 'Succeed delete user from team',
            'data' => [
                'teamId' => $teamId,
                'memberId' => $memberId,
            ],
        ];

        return response()->json($responseData, 201);
    }
}
