<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    public function store(StoreRegisterRequest $request): JsonResponse
    {
        $data = [
            "name" => $request->fullName,
            "email" => $request->email,
            "password" => $request->password,
            "phone" => $request->phone,
        ];
        $user = User::query()->create($data);
        $user->assignRole('User');

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $pattern = '/\bsignature=([^&]+)/';
        preg_match($pattern, $url, $matches);
        $signature = $matches[1];

        $expire = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->timestamp;
        $hash = sha1($user->email);

        $responseData = [
            "status" => 1,
            "message" => "Success register & check email for verify",
            "data" => [
                "user" => [
                    "id" => $user->id,
                    "fullName" => $user->name,
                    "email" => $user->email,
                ],
                'verifyEmail' => [
                    'id' => $user->id,
                    'hash' => $hash,
                    'expires' => $expire,
                    'signature' => $signature,
                ],
            ],
        ];

        return response()->json($responseData, 201);
    }
}
