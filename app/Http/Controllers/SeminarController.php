<?php

namespace App\Http\Controllers;

use App\Mail\SendSeminarParticipantTicket;
use App\Mail\SendSeminarParticipantTicketFail;
use App\Http\Requests\UpdatePaymentSeminarRequest;
use App\Helpers\PaymentSeminarStatus as PaymentSeminarStatusHelper;
use App\Models\User;
use App\Models\PaymentSeminarStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SeminarController extends Controller
{
    public function index(): JsonResponse
    {
        // $this->authorize('viewAny', User::class);
        $users = User::query()->role('User')->whereHas('payment')->with([
            'paymentStatus',
            'payment'
        ])->get();

        $usersResponse = [];
        foreach ($users as $user) {
            $paymentStatus = isset($user->payment) ? PaymentSeminarStatusHelper::PENDING : null;
            $paymentStatus = $user->paymentStatus->status ?? $paymentStatus;
            $userResponse = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'isActive' => $paymentStatus,
                'transferReceipt' => $user->payment->transfer_receipt ?? null,
            ];
            $usersResponse[] = $userResponse;
        }

        $responseData = [
            'status' => 1,
            'message' => 'success get all users',
            'data' => [
                'users' => $usersResponse,
            ],
        ];

        return response()->json($responseData);
    }

    public function show(string $userId): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $user = User::query()->role('User')->whereHas('payment')->with([
            'paymentStatus',
            'payment'
        ])->findOrFail($userId);

        $paymentStatus = isset($user->payment) ? PaymentSeminarStatusHelper::PENDING : null;
        $paymentStatus = $user->paymentStatus->status ?? $paymentStatus;
        $userResponse = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'isActive' => $paymentStatus,
            'transferReceipt' => $user->payment->transfer_receipt ?? null,
        ];

        $responseData = [
            'status' => 1,
            'message' => 'success get all user',
            'data' => $userResponse,
        ];

        return response()->json($responseData);
    }
    
    public function tampil(string $userId): JsonResponse
    {
        // $this->authorize('viewAny', User::class);
        $user = User::query()->role('User')->whereHas('payment')->with([
            'paymentStatus',
            'payment'
        ])->findOrFail($userId);

        $paymentStatus = isset($user->payment) ? PaymentSeminarStatusHelper::PENDING : null;
        $paymentStatus = $user->paymentStatus->status ?? $paymentStatus;
        $userResponse = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'isActive' => $paymentStatus,
            'transferReceipt' => $user->payment->transfer_receipt ?? null,
        ];

        $responseData = [
            'status' => 1,
            'message' => 'success get all user',
            'data' => $userResponse,
        ];

        return response()->json($responseData);
    }

    public function update(UpdatePaymentSeminarRequest $request, string $userId): JsonResponse
    {
        $this->authorize('update', [PaymentSeminarStatus::class, new PaymentSeminarStatus()]);
        $user = User::query()->findOrFail($userId);

        $paymentStatusData = [
            'user_id' => $user->id,
            'status' => $request->input('isApprove') ? PaymentSeminarStatusHelper::VALID : PaymentSeminarStatusHelper::INVALID,
            'reason' => $request->input('reason'),
        ];

        if($request->input('isApprove')) {
            Mail::to($user)->queue(new SendSeminarParticipantTicket($user->name, $user->email));
        } else {
            Mail::to($user)->queue(new SendSeminarParticipantTicketFail($user->name, $user->email, $paymentStatusData['reason']));

        }

        $paymentStatus = PaymentSeminarStatus::query()->updateOrCreate(
            ['user_id' => $user->id],
            $paymentStatusData
        );

        $responseData = [
            'status' => 1,
            'message' => 'success update payment status',
            'data' => [
                'payment' => [
                    'user_id' => $userId,
                    'status' => $paymentStatus->status,
                    'reason' => $paymentStatus->reason,
                ]
            ]
        ];

        return response()->json($responseData);
    }
}
