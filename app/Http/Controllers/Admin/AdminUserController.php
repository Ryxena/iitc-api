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

    public function edit(string $userId): View
    {
        $user = User::with('participant')->findOrFail($userId);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, string $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'       => 'nullable|string|max:20',
            'password'    => 'nullable|string|min:8',
            'institution' => 'nullable|string|max:255',
            'grade'       => 'nullable|string|max:50',
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        if ($user->participant) {
            $user->participant->update([
                'institution' => $validated['institution'],
                'grade'       => $validated['grade'],
            ]);
        }

        return redirect()->back()->with('success', "Data user \"{$user->name}\" berhasil diperbarui.");
    }

    public function destroy(string $userId): RedirectResponse
    {
        $user = User::query()->findOrFail($userId);
        $user->delete();

        return redirect()->back()->with('success', "User \"{$user->name}\" berhasil dihapus.");
    }
}
