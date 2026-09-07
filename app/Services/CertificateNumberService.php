<?php

namespace App\Services;

use App\Helpers\PaymentStatus;
use App\Models\CertificateNumber;
use App\Models\Event;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class CertificateNumberService
{
    public const DEFAULT_SUFFIX = 'SRT-IITC/INTERMEDIA/IX/2026';

    public const DEFAULT_START = [
        CertificateNumber::TYPE_FINALIST => 55,
        CertificateNumber::TYPE_PARTICIPANT => 84,
    ];

    /**
     * The static suffix segment shared by all numbers of an event,
     * e.g. "SRT-IITC/INTERMEDIA/IX/2026".
     */
    public function suffix(Event $event): string
    {
        return Setting::get($this->key('suffix', $event), self::DEFAULT_SUFFIX);
    }

    /**
     * Starting number configured for an event + type (D or F).
     */
    public function start(Event $event, string $type): int
    {
        return (int) Setting::get($this->key('start', $event, $type), self::DEFAULT_START[$type] ?? 1);
    }

    /**
     * Persist the suffix segment for an event.
     */
    public function setSuffix(Event $event, string $suffix): void
    {
        Setting::set($this->key('suffix', $event), trim($suffix, '/'));
    }

    /**
     * Persist the starting number for an event + type.
     */
    public function setStart(Event $event, string $type, int $start): void
    {
        Setting::set($this->key('start', $event, $type), max(1, $start));
    }

    /**
     * Create rows for every eligible person of the event's VALID-paid teams
     * (leader + members). Type D for teams with a winner, F otherwise.
     * Existing rows keep their sequence; new rows are added unassigned.
     *
     * @return int number of rows created
     */
    public function sync(Event $event): int
    {
        $teams = Team::query()
            ->whereIn('competition_id', $event->competitions()->pluck('id'))
            ->whereHas('paymentStatus', fn ($q) => $q->where('status', PaymentStatus::VALID))
            ->with(['winner', 'members', 'leader'])
            ->get();

        $created = 0;

        foreach ($teams as $team) {
            $type = $team->winner ? CertificateNumber::TYPE_FINALIST : CertificateNumber::TYPE_PARTICIPANT;

            foreach ($this->teamPeople($team) as $user) {
                $row = CertificateNumber::query()->firstOrNew([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                ]);

                if ($row->exists) {
                    // Tipe pindah (misal juara dihapus → jadi partisipan):
                    // nomor lama dilepas agar dapat nomor tipe baru saat assign.
                    $row->update($row->type === $type
                        ? ['type' => $type]
                        : ['type' => $type, 'sequence' => null]);

                    continue;
                }

                $row->type = $type;
                $row->save();
                $created++;
            }
        }

        return $created;
    }

    /**
     * Assign sequences to every unassigned row of the event, continuing
     * from the highest number already in use (or the configured start).
     *
     * @return int number of rows assigned
     */
    public function assign(Event $event): int
    {
        $assigned = 0;

        foreach ([CertificateNumber::TYPE_FINALIST, CertificateNumber::TYPE_PARTICIPANT] as $type) {
            $rows = $this->rowsQuery($event, $type)
                ->whereNull('sequence')
                ->orderBy('id')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $next = $this->nextSequence($event, $type);

            foreach ($rows as $row) {
                $row->update(['sequence' => $next++]);
                $assigned++;
            }
        }

        return $assigned;
    }

    /**
     * Rebuild the roster of a team and fill in any missing numbers.
     * Called when a winner is set/removed so certificate numbers track
     * the juara data without the admin pressing Generate again.
     */
    public function syncTeam(Team $team): void
    {
        $event = $team->competition->event;

        if (! $event) {
            return;
        }

        $this->sync($event);
        $this->assign($event);
    }

    /**
     * Full certificate number for a row, e.g. "055/D/SRT-IITC/INTERMEDIA/IX/2026".
     * Returns null while the row has no sequence yet.
     */
    public function format(CertificateNumber $row): ?string
    {
        if ($row->sequence === null) {
            return null;
        }

        $event = $row->team->competition->event;

        return sprintf('%03d/%s/%s', $row->sequence, $row->type, $this->suffix($event));
    }

    /**
     * Official number of a person inside a team, if assigned. Used by the
     * competition certificate PDF generator.
     */
    public function numberFor(User $user, Team $team): ?string
    {
        $row = CertificateNumber::query()
            ->with(['team.competition.event'])
            ->where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->first();

        return $row ? $this->format($row) : null;
    }

    /**
     * Official number of a person, preferring the finalist (D) row when
     * they belong to multiple teams.
     */
    public function numberForUser(User $user): ?string
    {
        $row = CertificateNumber::query()
            ->with(['team.competition.event'])
            ->where('user_id', $user->id)
            ->whereNotNull('sequence')
            ->orderBy('type')
            ->orderByDesc('id')
            ->first();

        return $row ? $this->format($row) : null;
    }

    /**
     * Map of "userId|teamId" => formatted number for every assigned row of
     * the given teams, used to avoid N+1 queries on listing pages.
     *
     * @param  array<int, string|int>|Collection  $teamIds
     * @return array<string, string>
     */
    public function numbersForTeams(array|Collection $teamIds): array
    {
        $rows = CertificateNumber::query()
            ->with(['team.competition.event'])
            ->whereIn('team_id', $teamIds)
            ->whereNotNull('sequence')
            ->get();

        $suffixes = [];
        $map = [];

        foreach ($rows as $row) {
            $event = $row->team?->competition?->event;

            if (! $event) {
                continue;
            }

            $suffixes[$event->id] ??= $this->suffix($event);
            $map[$row->user_id.'|'.$row->team_id] = sprintf('%03d/%s/%s', $row->sequence, $row->type, $suffixes[$event->id]);
        }

        return $map;
    }

    /**
     * Highest sequence currently used for an event + type, or the start
     * value when nothing has been assigned yet.
     */
    public function currentMax(Event $event, string $type): int
    {
        $max = (int) $this->rowsQuery($event, $type)->whereNotNull('sequence')->max('sequence');

        return max($max, $this->start($event, $type));
    }

    /**
     * People of a team: leader plus pivot members, deduplicated.
     *
     * @return Collection<int, User>
     */
    private function teamPeople(Team $team): Collection
    {
        return $team->members->prepend($team->leader)->filter()->unique('id')->values();
    }

    /**
     * Rows of an event + type.
     */
    private function rowsQuery(Event $event, string $type)
    {
        return CertificateNumber::query()
            ->where('type', $type)
            ->whereHas('team.competition', fn ($q) => $q->where('event_id', $event->id));
    }

    /**
     * First free sequence for an event + type.
     */
    private function nextSequence(Event $event, string $type): int
    {
        $max = (int) $this->rowsQuery($event, $type)->whereNotNull('sequence')->max('sequence');

        return $max > 0 ? $max + 1 : $this->start($event, $type);
    }

    /**
     * Setting keys, scoped per event so a new edition gets fresh numbers.
     */
    private function key(string $what, Event $event, ?string $type = null): string
    {
        return $type
            ? "certificate_{$what}_{$event->id}_{$type}"
            : "certificate_{$what}_{$event->id}";
    }
}
