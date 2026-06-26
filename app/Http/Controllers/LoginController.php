<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthenticationException;
use App\Http\Requests\StoreLoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(StoreLoginRequest $request): JsonResponse
    {
        try {
            $user = User::query()->where("email", $request->email)->firstOrFail();
            if (!Hash::check($request->password, $user->password)) {
                throw new AuthenticationException("Invalid Password");
            }

            $token = $user->createToken('authToken')->plainTextToken;

            $data = [
                "status" => 1,
                "message" => "berhasil login",
                "data" => [
                    "access_token" => $token,
                    'email_verified_at' => $user->email_verified_at,
                ]
            ];
            return response()->json($data, 200);
        } catch (ModelNotFoundException $exception) {
            $responseData = [
                "status" => 0,
                "message" => "User tidak ditemukan",
            ];

            return response()->json($responseData, 404);
        }
    }
}
