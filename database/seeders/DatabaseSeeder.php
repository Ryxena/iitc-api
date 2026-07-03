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
            'password' => 'myPassword',
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'myPassword',
        ]);
        $admin->assignRole('Admin');

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => 'myPassword',
        ]);
        $user->assignRole('User');

        $member = User::factory()->create([
            'name' => 'User',
            'email' => 'member@gmail.com',
            'password' => 'myPassword',
        ]);
        $member->assignRole('User');

        $notMember = User::factory()->create([
            'name' => 'User',
            'email' => 'notmember@gmail.com',
            'password' => 'myPassword',
        ]);
        $notMember->assignRole('User');

        $this->call([
            CategorySeeder::class,
            CompetitionSeeder::class,
            CategoryCompetitionSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}
