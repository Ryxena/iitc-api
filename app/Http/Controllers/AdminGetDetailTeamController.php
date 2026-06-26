<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentStatus;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminGetDetailTeamController extends Controller
{
    public function show(string $teamId): JsonResponse
    {
        $this->authorize('detailPayment', Team::class);
        $team = Team::query()->with([
            'paymentStatus',
            'payment',
            'leader',
            'leader.participant:avatar',
            'members:id,name,email',
            'members.participant:user_id,avatar',
            'competition:id,name',
        ])->findOrFail($teamId);
        $paymentStatus = isset($team->payment) ? PaymentStatus::PENDING : null;
        $paymentStatus = $team->paymentStatus->status ?? $paymentStatus;
        $teamResponse = [
            'name' => $team->name,
            'code' => $team->code,
            'title' => $team->title,
            'isActive' => $paymentStatus,
            'isSubmit' => isset($team->submission),
            'avatar' => $team->avatar,
            'transferReceipt' => $team->payment->transfer_receipt ?? null,
            'competition' => $team->competition,
            'leaderName' => $team->leader->name,
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
}
