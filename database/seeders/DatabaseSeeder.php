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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Roles & Permissions ────────────────────────────────
        $this->call(RoleAndPermissionSeeder::class);

        // ── 2. Admin users ────────────────────────────────────────
        $superAdmin = User::create([
            'name'              => 'Super Admin',
            'email'             => 'superadmin@intermediaiitc.com',
            'password'          => 'PokoknyaINIPW123',
            'phone'             => 6281234567890,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@intermediaiitc.com',
            'password'          => '#WeAreFamily123',
            'phone'             => 6281234567891,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // ── 3. Event ──────────────────────────────────────────────
        $event = Event::create([
            'name'        => 'IITC 2026',
            'description' => 'Indonesian IT Competition 2026',
            'is_active'   => true,
        ]);

        // ── 4. Categories ─────────────────────────────────────────
        $pelajar   = Category::create(['name' => 'Pelajar']);
        $mahasiswa = Category::create(['name' => 'Mahasiswa']);

        // ── 5. Competitions ───────────────────────────────────────
        $this->call(CompetitionSeeder::class);
        $competitionModels = Competition::all();

        // ── 6. Regular users with participants, teams & payments ──
        $statuses = [
            \App\Helpers\PaymentStatus::VALID,
            \App\Helpers\PaymentStatus::PENDING,
            \App\Helpers\PaymentStatus::INVALID,
        ];

        $receiptDir = storage_path('app/public/receipt');
        if (! file_exists($receiptDir)) {
            mkdir($receiptDir, 0755, true);
        }
        $dummyReceiptUrl = Storage::disk('public')->url('receipt/dummy.png');

        $userData = [
            ['name' => 'Andi Pratama',  'email' => 'andi-demo@example.com',   'phone' => 6281100000001, 'grade' => '11', 'gender' => 'Laki-laki', 'nisn' => '1234567890', 'institution' => 'SMAN 1 Jakarta'],
            ['name' => 'Budi Santoso',  'email' => 'budi-demo@example.com',   'phone' => 6281100000002, 'grade' => '12', 'gender' => 'Laki-laki', 'nisn' => '1234567891', 'institution' => 'SMAN 2 Bandung'],
            ['name' => 'Citra Dewi',    'email' => 'citra-demo@example.com',  'phone' => 6281100000003, 'grade' => '10', 'gender' => 'Perempuan', 'nisn' => '1234567892', 'institution' => 'SMAN 3 Surabaya'],
            ['name' => 'Dian Rahayu',   'email' => 'dian-demo@example.com',   'phone' => 6281100000004, 'grade' => '11', 'gender' => 'Perempuan', 'nisn' => '1234567893', 'institution' => 'SMAN 4 Yogyakarta'],
            ['name' => 'Eka Wulandari', 'email' => 'eka-demo@example.com',    'phone' => 6281100000005, 'grade' => '12', 'gender' => 'Perempuan', 'nisn' => '1234567894', 'institution' => 'SMAN 5 Semarang'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar-demo@example.com',  'phone' => 6281100000006, 'grade' => '1',  'gender' => 'Laki-laki', 'nisn' => '2234567890', 'institution' => 'Universitas Indonesia'],
            ['name' => 'Gita Puspita',  'email' => 'gita-demo@example.com',   'phone' => 6281100000007, 'grade' => '2',  'gender' => 'Perempuan', 'nisn' => '2234567891', 'institution' => 'ITB'],
            ['name' => 'Hendra Kusuma', 'email' => 'hendra-demo@example.com', 'phone' => 6281100000008, 'grade' => '3',  'gender' => 'Laki-laki', 'nisn' => '2234567892', 'institution' => 'ITS'],
            ['name' => 'Indah Permata', 'email' => 'indah-demo@example.com',  'phone' => 6281100000009, 'grade' => '1',  'gender' => 'Perempuan', 'nisn' => '2234567893', 'institution' => 'UGM'],
            ['name' => 'Joko Widodo',   'email' => 'joko-demo@example.com',   'phone' => 6281100000010, 'grade' => '2',  'gender' => 'Laki-laki', 'nisn' => '2234567894', 'institution' => 'UNPAD'],
        ];

        foreach ($userData as $i => $data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => 'myPassword',
                'phone'             => $data['phone'],
                'email_verified_at' => now(),
            ]);
            $user->assignRole('User');

            Participant::create([
                'user_id'           => $user->id,
                'grade'             => $data['grade'],
                'gender'            => $data['gender'],
                'student_id_number' => $data['nisn'],
                'institution'       => $data['institution'],
            ]);

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

        // ── 7. Seminar registrations ──────────────────────────────
        $seminarUsers = [
            ['name' => 'Rini Anggraeni',  'email' => 'rini-demo@example.com',  'phone' => 6281200000001, 'attended' => true],
            ['name' => 'Sandi Maulana',   'email' => 'sandi-demo@example.com', 'phone' => 6281200000002, 'attended' => true],
            ['name' => 'Tari Kusumawati', 'email' => 'tari-demo@example.com',  'phone' => 6281200000003, 'attended' => false],
            ['name' => 'Umar Hakim',      'email' => 'umar-demo@example.com',  'phone' => 6281200000004, 'attended' => false],
            ['name' => 'Vera Lestari',    'email' => 'vera-demo@example.com',  'phone' => 6281200000005, 'attended' => false],
        ];

        foreach ($seminarUsers as $data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => 'myPassword',
                'phone'             => $data['phone'],
                'email_verified_at' => now(),
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
