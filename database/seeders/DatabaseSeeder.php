<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Participant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            RoleAndPermissionSeeder::class,
        );

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => 'myPassword'
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'myPassword'
        ]);
        $admin->assignRole('Admin');

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => 'myPassword'
        ]);
        $user->assignRole('User');

        $member = User::factory()->create([
            'name' => 'User',
            'email' => 'member@gmail.com',
            'password' => 'myPassword'
        ]);
        $member->assignRole('User');

        $notMember = User::factory()->create([
            'name' => 'User',
            'email' => 'notmember@gmail.com',
            'password' => 'myPassword'
        ]);
        $notMember->assignRole('User');

        $this->call([
            CategorySeeder::class,
            CompetitionSeeder::class,
            CategoryCompetitionSeeder::class
        ]);

        $users = User::factory(100)->create();
        foreach ($users as $user) {
            $user->assignRole('User');
            Team::factory()->create([
                'leader_id' => $user->id,
                'competition_id' => fake()->numberBetween(1, 10),
            ]);
        }

        $members = User::factory(300)->create();
        $memberIndex = 0;
        for ($i=1; $i <= 100; $i++) {
            for ($j=0; $j < 3; $j++) {
                Member::factory()->create([
                    'team_id' => $i,
                    'user_id' => $members[$memberIndex]->id,
                ]);
                $members[$memberIndex]->assignRole('User');
                $memberIndex++;
            }
        }

        foreach ($members as $member) {
            Participant::factory()->create([
                'user_id' => $member->id,
            ]);
        }
    }
}
