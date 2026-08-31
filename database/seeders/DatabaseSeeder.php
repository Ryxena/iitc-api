<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Roles & Permissions ────────────────────────────────
        $this->call(RoleAndPermissionSeeder::class);

        // ── 2. Admin users ────────────────────────────────────────
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@intermediaiitc.com',
            'password' => 'PokoknyaINIPW123',
            'phone' => 6281234567890,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@intermediaiitc.com',
            'password' => '#WeAreFamily123',
            'phone' => 6281234567891,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // ── 3. Event ──────────────────────────────────────────────
        $event = Event::create([
            'name' => 'IITC 2026',
            'description' => 'Indonesian IT Competition 2026',
            'is_active' => true,
        ]);

        // ── 4. Categories ─────────────────────────────────────────
        $pelajar = Category::create(['name' => 'Pelajar']);
        $mahasiswa = Category::create(['name' => 'Mahasiswa']);

        // ── 5. Competitions ───────────────────────────────────────
        $this->call(CompetitionSeeder::class);

        $this->command->info('✅ Production Seeder complete.');
        $this->command->info('   Super Admin  : superadmin@intermediaiitc.com');
        $this->command->info('   Admin        : admin@intermediaiitc.com');
        $this->command->info('   Competitions : UI UX, Gen AI, Web Design (Seeded)');
    }
}
