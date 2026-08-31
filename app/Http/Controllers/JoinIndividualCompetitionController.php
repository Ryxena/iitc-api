<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinIndividualCompetitionRequest;
use App\Models\Competition;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;

class JoinIndividualCompetitionController extends Controller
{
    public function __invoke(JoinIndividualCompetitionRequest $request, string $competitionSlug): JsonResponse
    {
        try {
            $competition = Competition::query()->where('slug', $competitionSlug)->firstOrFail();

            // Business validation: verify if competition allows individual entry
            if ($competition->max_members !== 1) {
                return $this->error('This competition is team-based. You must register or join a team.', 400);
            }

            // Business validation: check if user already registered or joined in this event
            $userId = auth()->id();
            $hasTeamInEvent = Team::query()
                ->whereHas('competition', function ($q) use ($competition) {
                    $q->where('event_id', $competition->event_id);
                })
                ->where(function ($query) use ($userId) {
                    $query->where('leader_id', $userId)
                        ->orWhereHas('members', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        });
                })->exists();

            if ($hasTeamInEvent) {
                return $this->error('You have already joined a competition in this event.', 400);
            }

            $team = Team::query()->create([
                'leader_id' => $userId,
                'competition_id' => $competition->id,
            ]);

            return $this->success('Succeed joined competition', [
                'team' => [
                    'id' => $team->id,
                ],
            ]);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }
}
