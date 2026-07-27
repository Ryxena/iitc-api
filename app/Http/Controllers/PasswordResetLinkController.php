<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePasswordResetLinkRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function store(StorePasswordResetLinkRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if ($user === null) {
            return $this->error('email tidak ada', 404);
        }
        $token = Password::broker()->createToken($user);

        return $this->success('Success request link reset password', [
            'token_reset_password' => $token,
        ]);
    }
}
