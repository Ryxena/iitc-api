<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    public function store(StoreNewPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $responseData = [
                "status" => 1,
                "message" => "Successs reset password",
            ];

            return response()->json($responseData);
        }


        $responseData = [
            "status" => 0,
            "message" => "Fail reset password",
        ];

        return response()->json($responseData);
    }
}
