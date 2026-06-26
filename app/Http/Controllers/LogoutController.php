<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function store(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $responseData = [
            "status" => 1,
            "message" => "Success Logout"
        ];

        return response()->json($responseData, 200);
    }
}
