<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTeamManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $competitionId = $request->query('competition_id', '');

        $activeEvent = Event::query()->where('is_active', true)->first();
        $competitions = $activeEvent
            ? Competition::query()->where('event_id', $activeEvent->id)->orderBy('name')->get()
            : collect();

        $query = Team::query()->with(['competition', 'leader.participant'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($competitionId) {
            $query->where('competition_id', $competitionId);
        }

        $teams = $query->paginate(25)->withQueryString();

        return view('admin.teams-management.index', compact('teams', 'search', 'competitions', 'competitionId', 'activeEvent'));
    }


    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'leader_email'   => 'required|email|exists:users,email',
            'competition_id' => 'required|exists:competitions,id',
            'name'           => 'nullable|string|max:255',
            'code'           => 'nullable|string|max:255|unique:teams,code,' . $team->id,
            'title'          => 'nullable|string|max:255',
            'is_active'      => 'boolean',
        ]);

        $leader = User::query()->where('email', $validated['leader_email'])->firstOrFail();

        $team->update([
            'leader_id'      => $leader->id,
            'competition_id' => $validated['competition_id'],
            'name'           => $validated['name'],
            'code'           => $validated['code'],
            'title'          => $validated['title'],
            'is_active'      => $validated['is_active'] ?? false,
        ]);

        return redirect()->back()->with('success', "Tim \"{$team->name}\" berhasil diperbarui.");
    }

    public function destroy(Team $team): RedirectResponse
    {
        $name = $team->name;
        $team->delete();

        return redirect()->back()->with('success', "Tim \"{$name}\" berhasil dihapus.");
    }
}
