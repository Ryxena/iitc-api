<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSeminarStatus;
use App\Helpers\PaymentSeminarStatus as PaymentSeminarStatusHelper;
use App\Http\Requests\StorePaymentSeminarRequest;
use App\Models\PaymentSeminar;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PaymentSeminarController extends Controller
{
    public function store(StorePaymentSeminarRequest $request, string $userId): JsonResponse
    {
        $this->authorize('create', [PaymentSeminar::class, new PaymentSeminar(), User::query()->findOrFail($userId)]);
        // $this->authorize('create', [PaymentSeminar::class, new PaymentSeminar(), $user]);
        $user = User::query()->findOrFail($userId);
        $proofs = [];
        foreach ($request->file('proveOfPayment') as $proof) {
            $proofs[] = Storage::disk('public')->url($proof->store('receipt', ['disk' => 'public']));
        }
        $paymentSeminarData = [
            'user_id' => $user->id,
            'transfer_receipt' => $proofs,
        ];

        $paymentSeminar = PaymentSeminar::query()->updateOrCreate(
            ['user_id' => $user->id],
            $paymentSeminarData,
        );
        $prevPaymentSeminarStatus = PaymentSeminarStatus::query()->where('user_id', $user->id)->first();
        if ($prevPaymentSeminarStatus != null) {
            $prevPaymentSeminarStatus->status = PaymentSeminarStatusHelper::PENDING;
            $prevPaymentSeminarStatus->save();
        }

        $responseData = [
            'status' => 1,
            'message' => 'success post proof of payment',
            'data' => [
                'user' => [
                    'userId' => $userId,
                ],
                'payment' => $paymentSeminar,
            ]
        ];

        return response()->json($responseData);
    }
}
