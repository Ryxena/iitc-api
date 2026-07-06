<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $users = User::query()->role('User')->with('participant')->get();

        return $this->success('success get all users', [
            'users' => $users,
        ]);
    }

    public function destroy(Request $request, string $userId): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($userId);
            $this->authorize('delete', $user);
            $user->delete();

            return $this->success('User berhasil dihapus');
        } catch (ModelNotFoundException $exception) {
            return $this->error('User tidak ada', 404);
        }
    }
}
