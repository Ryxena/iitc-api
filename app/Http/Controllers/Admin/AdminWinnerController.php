<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Winner;
use Illuminate\Http\Request;

class AdminWinnerController extends Controller
{
    public function index()
    {
        $activeEvent = Event::where('is_active', true)->first();
        $competitions = collect();
        if ($activeEvent) {
            $competitions = Competition::where('event_id', $activeEvent->id)
                ->with(['teams' => function ($q) {
                    $q->with(['winner', 'leader.participant'])->whereHas('paymentStatus', function ($sq) {
                        $sq->where('status', 'VALID');
                    });
                }])
                ->get();
        }

        return view('admin.winners.index', compact('activeEvent', 'competitions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'rank' => 'required|integer',
            'award_title' => 'required|string',
        ]);

        Winner::updateOrCreate(
            ['team_id' => $request->team_id],
            ['rank' => $request->rank, 'award_title' => $request->award_title]
        );

        return redirect()->back()->with('success', 'Berhasil menyimpan data juara.');
    }

    public function destroy(string $teamId)
    {
        Winner::where('team_id', $teamId)->delete();

        return redirect()->back()->with('success', 'Berhasil menghapus data juara.');
    }
}
