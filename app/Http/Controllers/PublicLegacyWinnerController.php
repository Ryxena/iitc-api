<?php

namespace App\Http\Controllers;

use App\Models\LegacyWinner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicLegacyWinnerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $year = $request->integer('year');

        $winners = LegacyWinner::query()
            ->when($year, function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->orderBy('year', 'desc')
            ->orderBy('rank')
            ->get()
            ->map(function ($winner) {
                return [
                    'id' => $winner->id,
                    'year' => $winner->year,
                    'projectName' => $winner->project_name,
                    'projectDescription' => $winner->project_description,
                    'institution' => $winner->institution,
                    'competitionName' => $winner->competition_name,
                    'rank' => $winner->rank,
                    'awardTitle' => $winner->award_title,
                    'image' => $winner->image,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Data juara lama berhasil diambil',
            'data' => $winners->toArray(),
        ]);
    }
}