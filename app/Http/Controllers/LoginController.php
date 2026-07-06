<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoginRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(StoreLoginRequest $request): JsonResponse
    {
        try {
            $user = User::query()->where('email', $request->email)->firstOrFail();
            if (! Hash::check($request->password, $user->password)) {
                return $this->error('Invalid Password', 401);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return $this->success('berhasil login', [
                'access_token'       => $token,
                'email_verified_at'  => $user->email_verified_at,
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->error('User tidak ditemukan', 404);
        }
    }
}
