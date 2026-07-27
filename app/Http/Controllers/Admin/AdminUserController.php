<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search        = $request->query('search', '');
        $competitionId = $request->query('competition', '');

        $activeEvent = Event::query()->where('is_active', true)->first();

        $competitions = $activeEvent
            ? Competition::query()->where('event_id', $activeEvent->id)->orderBy('name')->get()
            : collect();

        $query = User::query()
            ->role('User')
            ->with('participant')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($competitionId) {
            $query->whereHas('members.team', fn ($q) =>
                $q->where('competition_id', $competitionId)
            );
        }

        $users = $query->paginate(25)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'competitions', 'competitionId', 'activeEvent'));
    }

    public function destroy(string $userId): RedirectResponse
    {
        $user = User::query()->findOrFail($userId);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User \"{$user->name}\" berhasil dihapus.");
    }
}
