<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Participant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LeaderTeamSeeder extends Seeder
{
    /**
     * Seed a user@gmail.com who is the leader of a team with 2 members.
     *
     * Credentials:
     *   Leader  → user@gmail.com  / myPassword
     *   Member1 → member1@gmail.com / myPassword
     *   Member2 → member2@gmail.com / myPassword
     */
    public function run(): void
    {
        // ── 1. Leader ──────────────────────────────────────────────────────────
        $leader = User::firstOrCreate(
            ['email' => 'useryeat@gmail.com'],
            [
                'name'              => 'Team Leader',
                'password'          => 'myPassword',
                'email_verified_at' => now(),
                'phone'             => 6281234567890,
            ]
        );

        if (! $leader->hasRole('User')) {
            $leader->assignRole('User');
        }

        Participant::firstOrCreate(
            ['user_id' => $leader->id],
            [
                'grade'             => 'pelajar',
                'gender'            => 'male',
                'student_id_number' => '123456789',
                'institution'       => 'SMAN 1 Jakarta',
            ]
        );

        // ── 2. Members ─────────────────────────────────────────────────────────
        $memberData = [
            ['name' => 'Member One', 'email' => 'member1@gmail.com'],
            ['name' => 'Member Two', 'email' => 'member2@gmail.com'],
        ];

        $memberUsers = collect();
        foreach ($memberData as $data) {
            $memberUser = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => 'myPassword',
                    'email_verified_at' => now(),
                    'phone'             => fake()->numerify('628##########'),
                ]
            );

            if (! $memberUser->hasRole('User')) {
                $memberUser->assignRole('User');
            }

            Participant::firstOrCreate(
                ['user_id' => $memberUser->id],
                [
                    'grade'             => 'pelajar',
                    'gender'            => 'female',
                    'student_id_number' => Str::random(8),
                    'institution'       => 'SMAN 2 Jakarta',
                ]
            );

            $memberUsers->push($memberUser);
        }

        // ── 3. Team ────────────────────────────────────────────────────────────
        $competition = \App\Models\Competition::first();
        if (! $competition) {
            $this->call(CompetitionSeeder::class);
            $competition = \App\Models\Competition::first();
        }

        $team = Team::firstOrCreate(
            [
                'leader_id'      => $leader->id,
                'competition_id' => $competition->id,
            ],
            [
                'name'      => 'Team Alpha',
                'code'      => strtoupper(Str::random(6)),
                'title'     => 'Our Awesome Project',
                'is_active' => true,
            ]
        );

        // ── 4. Attach members to the team ──────────────────────────────────────
        foreach ($memberUsers as $memberUser) {
            Member::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => $memberUser->id,
            ]);
        }

        // ── 5. Payment Proof & Status ──────────────────────────────────────────
        \App\Models\Payment::firstOrCreate(
            ['team_id' => $team->id],
            ['transfer_receipt' => 'https://via.placeholder.com/600x400.png?text=Fake+Receipt']
        );

        \App\Models\PaymentStatus::firstOrCreate(
            ['team_id' => $team->id],
            [
                'status' => \App\Helpers\PaymentStatus::VALID, 
                'reason' => 'Automatically validated by seeder'
            ]
        );

        $this->command->info('LeaderTeamSeeder done.');
        $this->command->info("  Leader  → user@gmail.com / myPassword  (team_id: {$team->id})");
        $this->command->info("  Member1 → member1@gmail.com / myPassword");
        $this->command->info("  Member2 → member2@gmail.com / myPassword");
    }
}
