<?php

namespace App\Http\Controllers;

use App\Models\Seminar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicSeminarController extends Controller
{
    /**
     * Get list of active seminars (or all if specified).
     */
    public function index(Request $request): JsonResponse
    {
        $showAll = $request->boolean('all', false);

        $query = Seminar::query()->latest();

        if (! $showAll) {
            $query->where('is_active', true);
        }

        $seminars = $query->get()->map(fn (Seminar $seminar) => [
            'id'                => $seminar->id,
            'title'             => $seminar->title,
            'description'       => $seminar->description,
            'speaker'           => $seminar->speaker,
            'dateTime'          => $seminar->date_time ? $seminar->date_time->toIso8601String() : null,
            'startDate'         => $seminar->start_date ? $seminar->start_date->toDateString() : null,
            'endDate'           => $seminar->end_date ? $seminar->end_date->toDateString() : null,
            'location'          => $seminar->location,
            'registrationLink'  => $seminar->registration_link,
            'posterUrl'         => $seminar->poster ? \Illuminate\Support\Facades\Storage::disk('public')->url($seminar->poster) : null,
            'isActive'          => $seminar->is_active,
            'createdAt'         => $seminar->created_at->toIso8601String(),
        ]);

        return $this->success('success get seminar data', [
            'seminars' => $seminars,
        ]);
    }

    /**
     * Get single seminar detail.
     */
    public function show(string $id): JsonResponse
    {
        $seminar = Seminar::query()->find($id);

        if (! $seminar) {
            return $this->error('Seminar not found', 404);
        }

        return $this->success('success get detail seminar', [
            'seminar' => [
                'id'                => $seminar->id,
                'title'             => $seminar->title,
                'description'       => $seminar->description,
                'speaker'           => $seminar->speaker,
                'dateTime'          => $seminar->date_time ? $seminar->date_time->toIso8601String() : null,
                'startDate'         => $seminar->start_date ? $seminar->start_date->toDateString() : null,
                'endDate'           => $seminar->end_date ? $seminar->end_date->toDateString() : null,
                'location'          => $seminar->location,
                'registrationLink'  => $seminar->registration_link,
                'posterUrl'         => $seminar->poster ? \Illuminate\Support\Facades\Storage::disk('public')->url($seminar->poster) : null,
                'isActive'          => $seminar->is_active,
                'createdAt'         => $seminar->created_at->toIso8601String(),
            ],
        ]);
    }
}
