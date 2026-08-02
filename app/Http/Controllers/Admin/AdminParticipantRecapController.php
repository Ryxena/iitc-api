<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminParticipantRecapController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search', ''));

        $query = Participant::query()
            ->with([
                'user',
                'user.teams.competition',
                'user.asMembers.competition'
            ]);

        if ($search !== '') {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('institution', 'like', "%{$search}%");
        }

        $participants = $query->latest()->paginate(20)->withQueryString();
        $totalParticipants = Participant::count();

        return view('admin.participants.recap', compact('participants', 'totalParticipants', 'search'));
    }

    public function show($id): View
    {
        // Participant table uses user_id as its primary identifier in relationships
        $participant = Participant::with(['user', 'user.teams.competition', 'user.asMembers.competition'])
            ->where('user_id', $id)
            ->firstOrFail();

        return view('admin.participants.show', compact('participant'));
    }
}
