<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePasswordResetLinkRequest;
use App\Mail\SendsPasswordResetEmails;
use App\Models\User;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordResetLinkController extends Controller
{
    public function store(StorePasswordResetLinkRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user == null) {
            throw new NotFoundHttpException("email tidak ada");
        }
        $token = Password::broker()->createToken($user);
        Mail::to($user)->queue(new SendsPasswordResetEmails($token, $user->name, $user->email));

        $responseData = [
            "status" => 1,
            "message" => "Success request link reset password",
            "data" => [
                "token_reset_password" => $token,
            ],
        ];

        return response()->json($responseData);
    }
}
