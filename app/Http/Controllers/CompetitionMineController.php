<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentStatus;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompetitionMineController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $user = User::query()
                ->with([
                    'teams' => function ($query) {
                        $query->withCount('members');
                    },
                    'teams' => [
                        'competition',
                        'paymentStatus',
                        'payment',
                    ],
                    'asMembers' => function ($query) {
                        $query->withCount('members');
                    },
                    'asMembers.competition'])
                ->findOrFail(auth()->id());
            $teams = [];
            foreach ($user->teams as $team) {
                $teams[] = $this->transformDBToResponseTeam($team);
            }
            foreach ($user->asMembers as $team) {
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
        } catch (Exception $exception) {
            $responseData = [
                'status' => 0,
                'message' => $exception->getMessage(),
            ];

            return response()->json($responseData, 400);
        }
    }

    private function transformDBToResponseTeam(Team $team): array
    {
        $paymentStatus = isset($team->payment) ? PaymentStatus::PENDING : null;
        $paymentStatus = $team->paymentStatus->status ?? $paymentStatus;

        return [
            'teamId' => $team->id,
            'competitionName' => $team->competition->name,
            'cSlug' => $team->competition->slug,
            'teamName' => $team->name,
            'avatar' => $team->avatar,
            'isSubmit' => isset($team->submission),
            'maxMembers' => $team->competition->max_members,
            'currentMembers' => $team->members_count,
            'isActive' => $paymentStatus,
        ];
    }
}
