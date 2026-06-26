<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentStatus as PaymentStatusHelper;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Models\PaymentStatus;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class PaymentStatusController extends Controller
{

    public function update(UpdatePaymentStatusRequest $request, string $teamId): JsonResponse
    {
        $this->authorize('update', [PaymentStatus::class, new PaymentStatus()]);
        $team = Team::query()->findOrFail($teamId);

        $paymentStatusData = [
            'team_id' => $team->id,
            'status' => $request->input('isApprove') ? PaymentStatusHelper::VALID : PaymentStatusHelper::INVALID,
            'reason' => $request->input('reason'),
        ];

        $paymentStatus = PaymentStatus::query()->updateOrCreate(
            ['team_id' => $team->id],
            $paymentStatusData
        );

        $responseData = [
            'status' => 1,
            'message' => 'success update payment status',
            'data' => [
                'payment' => [
                    'team_id' => $teamId,
                    'status' => $paymentStatus->status,
                    'reason' => $paymentStatus->reason,
                ]
            ]
        ];

        return response()->json($responseData);
    }
}
