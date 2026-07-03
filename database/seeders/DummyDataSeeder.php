<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Participant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating 100 Users and their Teams (this may take a minute due to password hashing)...');
        $users = User::factory(100)->create();
        $teams = collect();

        foreach ($users as $user) {
            $user->assignRole('User');
            $team = Team::factory()->create([
                'leader_id' => $user->id,
                'competition_id' => fake()->numberBetween(1, 10),
            ]);
            $teams->push($team);
        }

        $this->command->info('Creating 300 Members for the Teams (this may take a few minutes)...');
        $members = User::factory(300)->create();
        $memberIndex = 0;
        
        foreach ($teams as $team) {
            for ($j = 0; $j < 3; $j++) {
                Member::factory()->create([
                    'team_id' => $team->id,
                    'user_id' => $members[$memberIndex]->id,
                ]);
                $members[$memberIndex]->assignRole('User');
                $memberIndex++;
            }
        }

        $this->command->info('Creating Participants for each member...');
        foreach ($members as $member) {
            Participant::factory()->create([
                'user_id' => $member->id,
            ]);
        }
    }
}
