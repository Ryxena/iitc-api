<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\SeminarRegistration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $superAdmin = User::factory()->create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@intermediaiitc.com',
            'password' => 'PokoknyaINIPW123',
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@intermediaiitc.com',
            'password' => '#WeAreFamily123',
        ]);
        $admin->assignRole('Admin');

        $event = Event::create([
            'name'        => 'IITC 2026',
            'description' => 'Indonesian IT Competition 2026',
            'is_active'   => true,
        ]);

        $pelajar   = Category::create(['name' => 'Pelajar']);
        $mahasiswa = Category::create(['name' => 'Mahasiswa']);
        
        $this->call(CompetitionSeeder::class);

        $competitionModels = Competition::all();

        $statuses = [
            \App\Helpers\PaymentStatus::VALID,
            \App\Helpers\PaymentStatus::PENDING,
            \App\Helpers\PaymentStatus::INVALID,
        ];

        $receiptDir = storage_path('app/public/receipt');
        if (! file_exists($receiptDir)) {
            mkdir($receiptDir, 0755, true);
        }
        $dummyReceiptUrl = \Illuminate\Support\Facades\Storage::disk('public')->url('receipt/dummy.png');

        $userData = [
            ['name' => 'Andi Pratama',    'email' => 'andi@example.com'],
            ['name' => 'Budi Santoso',    'email' => 'budi@example.com'],
            ['name' => 'Citra Dewi',      'email' => 'citra@example.com'],
            ['name' => 'Dian Rahayu',     'email' => 'dian@example.com'],
            ['name' => 'Eka Wulandari',   'email' => 'eka@example.com'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar@example.com'],
            ['name' => 'Gita Puspita',    'email' => 'gita@example.com'],
            ['name' => 'Hendra Kusuma',   'email' => 'hendra@example.com'],
            ['name' => 'Indah Permata',   'email' => 'indah@example.com'],
            ['name' => 'Joko Widodo',     'email' => 'joko@example.com'],
        ];

        foreach ($userData as $i => $data) {
            $user = User::factory()->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => 'myPassword',
            ]);
            $user->assignRole('User');

            Participant::factory()->create(['user_id' => $user->id]);

            $comp = $competitionModels[$i % count($competitionModels)];

            $team = Team::create([
                'leader_id'      => $user->id,
                'competition_id' => $comp->id,
                'name'           => 'Tim ' . $data['name'],
                'code'           => strtoupper(Str::random(6)),
            ]);

            Payment::create([
                'team_id'          => $team->id,
                'transfer_receipt' => $dummyReceiptUrl,
            ]);

            PaymentStatus::create([
                'team_id' => $team->id,
                'status'  => $statuses[$i % 3],
                'reason'  => ($statuses[$i % 3] === \App\Helpers\PaymentStatus::INVALID)
                    ? 'Bukti bayar tidak jelas.'
                    : '',
            ]);
        }

        $seminarUsers = [
            ['name' => 'Rini Anggraeni',  'email' => 'rini@example.com',    'attended' => true],
            ['name' => 'Sandi Maulana',   'email' => 'sandi@example.com',   'attended' => true],
            ['name' => 'Tari Kusumawati', 'email' => 'tari@example.com',    'attended' => false],
            ['name' => 'Umar Hakim',      'email' => 'umar@example.com',    'attended' => false],
            ['name' => 'Vera Lestari',    'email' => 'vera@example.com',    'attended' => false],
        ];

        foreach ($seminarUsers as $data) {
            $user = User::factory()->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => 'myPassword',
            ]);
            $user->assignRole('User');

            SeminarRegistration::create([
                'user_id'  => $user->id,
                'attended' => $data['attended'],
            ]);
        }

        $this->command->info('✅ Seeder complete.');
        $this->command->info('   Super Admin  : superadmin@intermediaiitc.com / PokoknyaINIPW123');
        $this->command->info('   Admin        : admin@intermediaiitc.com / #WeAreFamily123');
        $this->command->info('   Competitions : UI UX, Gen AI, Web Design');
        $this->command->info('   Users        : 10 dengan team & payment, 5 pendaftar seminar');
    }
}
