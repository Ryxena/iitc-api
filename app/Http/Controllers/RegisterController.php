<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function store(StoreRegisterRequest $request): JsonResponse
    {
        $data = [
            'name' => $request->fullName,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
        ];
        $user = User::query()->create($data);
        $user->assignRole('User');

        return $this->success('Success register', [
            'user' => [
                'id' => $user->id,
                'fullName' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }
}
