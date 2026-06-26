<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinIndividualCompetitionRequest;
use App\Models\Competition;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JoinIndividualCompetitionController extends Controller
{
    public function __invoke(JoinIndividualCompetitionRequest $request, string $competitionSlug): JsonResponse
    {
        try {
            $competition = Competition::query()->where('slug', $competitionSlug)->firstOrFail();
            $teamData = [
                'leader_id' => auth()->id(),
                'competition_id' => $competition->id,
            ];

            $team = Team::query()->create($teamData);

            $responseData = [
                'status' => 1,
                'message' => 'Succeed joined competition',
                'data' => [
                    'team' => [
                        'id' => $team->id
                    ]
                ]
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
