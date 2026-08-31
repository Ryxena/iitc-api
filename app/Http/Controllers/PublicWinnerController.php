<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Event;

class PublicWinnerController extends Controller
{
    public function index()
    {
        $activeEvent = Event::query()->where('is_active', true)->first();
        if (! $activeEvent) {
            return $this->error('No active event found.', 404);
        }

        $year = $activeEvent->created_at->format('Y');

        $competitions = Competition::query()
            ->where('event_id', $activeEvent->id)
            ->with([
                'teams' => function ($query) {
                    $query->has('winner')
                        ->with(['winner', 'leader.participant', 'members.participant']);
                },
            ])
            ->get();

        $categories = [];
        foreach ($competitions as $competition) {
            $winnersData = [];

            $teams = $competition->teams->sortBy(function ($team) {
                return $team->winner->rank;
            });

            foreach ($teams as $team) {
                $leader = $team->leader;
                $members = [];

                foreach ($team->members as $member) {
                    $members[] = [
                        'userId' => $member->id,
                        'name' => $member->name,
                        'role' => 'MEMBER',
                        'avatar' => $member->participant->avatar ?? null,
                    ];
                }

                $winnersData[] = [
                    'rank' => $team->winner->rank,
                    'awardTitle' => $team->winner->award_title,
                    'team' => [
                        'code' => $team->code,
                        'name' => $team->name,
                        'institution' => $leader->participant->institution ?? null,
                        'teamPhoto' => $team->avatar,
                        'leader' => [
                            'userId' => $leader->id,
                            'name' => $leader->name,
                            'role' => 'LEADER',
                            'avatar' => $leader->participant->avatar ?? null,
                        ],
                        'members' => $members,
                    ],
                ];
            }

            if (count($winnersData) > 0) {
                $categories[] = [
                    'categoryName' => $competition->name,
                    'slug' => $competition->slug,
                    'winners' => array_values($winnersData),
                ];
            }
        }

        return $this->success('Data juara berhasil diambil', [
            'year' => (int) $year,
            'categories' => $categories,
        ]);
    }
}
