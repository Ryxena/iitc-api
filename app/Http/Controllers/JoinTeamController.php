<?php

namespace App\Http\Controllers;

use App\Exceptions\LeaderJoinOwnTeamException;
use App\Http\Requests\StoreJoinTeamRequest;
use App\Models\Member;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class JoinTeamController extends Controller
{
    public function store(StoreJoinTeamRequest $request): JsonResponse
    {
        try {
            $code = $request->input('code');
            $team = Team::query()->where('code', $code)->firstOrFail();
            $user = auth()->user();

            // code not found
            if ($code != $team->code) {
                throw new NotFound('team code not found');
            }

            // leader joined his own team
            if ($team->leader_id == $user->id) {
                throw new LeaderJoinOwnTeamException('you are the leader!');
            }

            $user->asMembers()->syncWithoutDetaching($team->id);

            $responseData = [
                'status' => 1,
                'message' => 'Succeed joined a team',
            ];

            return response()->json($responseData, 200);
        } catch (Exception $exception) {
            $responseData = [
                'status' => 0,
                'message' => $exception->getMessage(),
            ];

            return response()->json($responseData, 400);
        }
    }
}
