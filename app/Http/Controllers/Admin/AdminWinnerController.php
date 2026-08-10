<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminWinnerController extends Controller
{
    public function index()
    {
        $activeEvent = \App\Models\Event::where('is_active', true)->first();
        $competitions = collect();
        if ($activeEvent) {
            $competitions = \App\Models\Competition::where('event_id', $activeEvent->id)
                ->with(['teams' => function ($q) {
                    $q->with(['winner', 'leader.participant'])->whereHas('paymentStatus', function ($sq) {
                        $sq->where('status', 'VALID');
                    });
                }])
                ->get();
        }

        return view('admin.winners.index', compact('activeEvent', 'competitions'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'rank' => 'required|integer',
            'award_title' => 'required|string',
        ]);

        \App\Models\Winner::updateOrCreate(
            ['team_id' => $request->team_id],
            ['rank' => $request->rank, 'award_title' => $request->award_title]
        );

        return redirect()->back()->with('success', 'Berhasil menyimpan data juara.');
    }

    public function destroy(string $teamId)
    {
        \App\Models\Winner::where('team_id', $teamId)->delete();
        return redirect()->back()->with('success', 'Berhasil menghapus data juara.');
    }
}
