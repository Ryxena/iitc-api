<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentStatus;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Team::class);
        
        $eventActive = Event::query()->where('is_active', true)->first();
        if (! $eventActive) {
            return $this->error('No active event found.', 404);
        }
        
        $competitionIds = Competition::query()->where('event_id', $eventActive->id)->pluck('id');
        $teams = Team::query()
            ->whereIn('competition_id', $competitionIds)
            ->with([
                'paymentStatus',
                'payment',
                'leader',
                'competition',
            ])->get();
            
        $teamsResponse = [];
        foreach ($teams as $team) {
            $paymentStatus = isset($team->payment) ? PaymentStatus::PENDING : null;
            $paymentStatus = $team->paymentStatus->status ?? $paymentStatus;
            
            $teamsResponse[] = [
                'id'              => $team->id,
                'name'            => $team->name,
                'code'            => $team->code,
                'title'           => $team->title,
                'isActive'        => $paymentStatus,
                'isSubmit'        => isset($team->submission),
                'avatar'          => $team->avatar,
                'leaderName'      => $team->leader->name,
                'competitionName' => $team->competition->name,
            ];
        }

        return $this->success('Succeed get all team.', ['teams' => $teamsResponse]);
    }

    public function store(StoreTeamRequest $request, string $competitionSlug): JsonResponse
    {
        $this->authorize('create', Team::class);
        $competition = Competition::query()->where('slug', $competitionSlug)->firstOrFail();
        
        // Business logic validation: check if user already has a team in this event (as leader or member)
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
            return $this->error('You are already a member or leader of a team in this event.', 400);
        }

        $code = fake()->bothify('##??##??');
        $team = Team::query()->create([
            'leader_id'      => $userId,
            'competition_id' => $competition->id,
            'code'           => $code,
            'name'           => $request->name,
        ]);

        return $this->success('Succeed create new team.', [
            'team' => [
                'id'   => $team->id,
                'code' => $team->code,
                'name' => $team->name,
            ],
        ], 201);
    }

    public static function findUserTeam($user): ?Team
    {
        $eventActive = Event::query()->where('is_active', true)->first();

        $teamQuery = Team::query()
            ->where(function ($query) use ($user) {
                $query->where('leader_id', $user->id)
                    ->orWhereHas('members', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            });

        $team = null;
        if ($eventActive) {
            $team = (clone $teamQuery)
                ->whereHas('competition', function ($q) use ($eventActive) {
                    $q->where('event_id', $eventActive->id);
                })
                ->first();
        }

        if (! $team) {
            $team = $teamQuery->latest()->first();
        }

        return $team;
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $team = static::findUserTeam($user);

        if (! $team) {
            return $this->error('Team not found.', 404);
        }

        $team->load([
            'paymentStatus',
            'payment',
            'leader',
            'leader.participant:user_id,avatar',
            'members:id,name,email',
            'members.participant:user_id,avatar',
            'competition',
        ]);

        $this->authorize('view', $team);

        $paymentStatus = isset($team->payment) ? PaymentStatus::PENDING : null;
        $paymentStatus = $team->paymentStatus->status ?? $paymentStatus;

        $members = $team->members->map(function ($member) {
            return [
                'id'     => $member->id,
                'name'   => $member->name,
                'email'  => $member->email,
                'avatar' => $member->participant->avatar ?? null,
            ];
        });

        $teamResponse = [
            'id'             => $team->id,
            'name'           => $team->name,
            'code'           => $team->code,
            'title'          => $team->title,
            'isActive'       => $paymentStatus,
            'isSubmit'       => isset($team->submission),
            'submissionLink' => $team->submission,
            'avatar'         => $team->avatar,
            'leader'         => [
                'name'   => $team->leader->name,
                'email'  => $team->leader->email,
                'avatar' => $team->leader->participant->avatar ?? null,
            ],
            'members'        => $members,
            'competition'    => $team->competition,
        ];

        return $this->success('Succeed get detail team.', ['team' => $teamResponse]);
    }

    public function update(UpdateTeamRequest $request): JsonResponse
    {
        $team = static::findUserTeam(auth()->user());
        if (! $team) {
            return $this->error('Team not found.', 404);
        }

        $this->authorize('update', $team);

        $teamData = [
            'name'  => $request->name,
            'title' => $request->title,
        ];

        if ($request->file('avatar') !== null) {
            $oldAvatar = $team->avatar;
            $avatar = $request->file('avatar')->store('team/avatar', ['disk' => 'public']);
            $teamData['avatar'] = Storage::disk('public')->url($avatar);
            if ($oldAvatar !== null && Storage::exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        if ($request->input('submission') !== null) {
            $teamData['submission'] = $request->input('submission');
        }

        $team->update($teamData);

        return $this->success('Succeed updated team.', [
            'team' => [
                'id'     => $team->id,
                'name'   => $team->name,
                'title'  => $team->title,
                'avatar' => $team->avatar,
                'submission' => $team->submission,
            ],
        ]);
    }

    public function destroy(): JsonResponse
    {
        $team = static::findUserTeam(auth()->user());
        if (! $team) {
            return $this->error('Team not found.', 404);
        }

        $this->authorize('delete', $team);
        $deletedTeamId = $team->id;
        $team->delete();

        return $this->success('Succeed delete team.', ['teamId' => $deletedTeamId]);
    }
}
