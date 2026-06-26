<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryCompetition;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Seeder;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleAndPermissionSeeder::class);

        Category::factory()->create([
            'name' => 'Pelajar',
        ]);

        Category::factory()->create([
            'name' => 'Mahasiswa',
        ]);

        Competition::factory(7)->create();

        $this->seedingCompetitionFactory();

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
            'name' => 'Member',
            'email' => 'member@gmail.com',
            'password' => 'myPassword'
        ]);
        $member->assignRole('User');

        $notMember = User::factory()->create([
            'name' => 'Not Member',
            'email' => 'notmember@gmail.com',
            'password' => 'myPassword'
        ]);
        $notMember->assignRole('User');

    }

    private function seedingCompetitionFactory(): void
    {
        for ($i = 0; $i < 7; $i++) {
            CategoryCompetition::factory()->create([
                'competition_id' => fake()->numberBetween(1, 7),
                'category_id' => fake()->numberBetween(1, 2),
            ]);
        }
    }
}
