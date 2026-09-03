<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WinnerController extends Controller
{
    public function store(Request $request)
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'rank' => 'required|integer',
            'award_title' => 'required|string',
            'project_name' => 'nullable|string|max:255',
            'project_description' => 'nullable|string',
        ]);

        $winner = Winner::updateOrCreate(
            ['team_id' => $request->team_id],
            [
                'rank' => $request->rank,
                'award_title' => $request->award_title,
                'project_name' => $request->project_name,
                'project_description' => $request->project_description,
            ]
        );

        return $this->success('Berhasil menyimpan data juara', $winner);
    }

    public function destroy(string $teamId)
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        Winner::where('team_id', $teamId)->delete();

        return $this->success('Berhasil menghapus data juara', null);
    }
}
