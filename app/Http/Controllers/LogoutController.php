<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Success Logout');
    }
}
