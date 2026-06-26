<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $users = User::query()->role('User')->with('participant')->get();

        $responseData = [
            'status' => 1,
            'message' => 'success get all users',
            'data' => [
                'users' => $users,
            ],
        ];

        return response()->json($responseData);
    }
    
    public function show(): JsonResponse
    {
        // $this->authorize('viewAny', User::class);
        $users = User::query()->role('User')->with('participant')->get();

        $responseData = [
            'status' => 1,
            'message' => 'success get all users',
            'data' => [
                'users' => $users,
            ],
        ];

        return response()->json($responseData);
    }

    public function destroy(Request $request, string $userId): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($userId);
            $this->authorize('delete', $user);
            $user->delete();

            $responseData = [
                "status" => 1,
                "message" => "User berhasil dihapus",
            ];

            return response()->json($responseData);
        } catch (ModelNotFoundException $exception) {
            $responseData = [
                "status" => 0,
                "message" => "User tidak ada",
            ];

            return response()->json($responseData, 404);
        }
    }
}
