<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Member;
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
        // ── 1. Roles & Permissions ────────────────────────────────
        $this->call(RoleAndPermissionSeeder::class);

        // ── 2. Admin users ────────────────────────────────────────
        $superAdmin = User::factory()->create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@gmail.com',
            'password' => 'myPassword',
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => 'myPassword',
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
        $competitions = [
            [
                'name'        => 'Web Design',
                'slug'        => 'web-design',
                'description' => 'Kompetisi desain website yang kreatif dan fungsional.',
                'deadline'    => now()->addDays(30),
                'max_members' => 3,
                'price'       => 75000,
                'guide_book'  => 'https://example.com/guidebook/web-design',
                'event_id'    => $event->id,
                'categories'  => [$pelajar->id, $mahasiswa->id],
            ],
            [
                'name'        => 'UI/UX',
                'slug'        => 'ui-ux',
                'description' => 'Kompetisi perancangan antarmuka dan pengalaman pengguna terbaik.',
                'deadline'    => now()->addDays(30),
                'max_members' => 2,
                'price'       => 75000,
                'guide_book'  => 'https://example.com/guidebook/ui-ux',
                'event_id'    => $event->id,
                'categories'  => [$pelajar->id, $mahasiswa->id],
            ],
            [
                'name'        => 'Generative AI',
                'slug'        => 'generative-ai',
                'description' => 'Kompetisi inovasi berbasis kecerdasan buatan generatif.',
                'deadline'    => now()->addDays(30),
                'max_members' => 3,
                'price'       => 100000,
                'guide_book'  => 'https://example.com/guidebook/gen-ai',
                'event_id'    => $event->id,
                'categories'  => [$mahasiswa->id],
            ],
        ];

        $competitionModels = [];
        foreach ($competitions as $data) {
            $categories = $data['categories'];
            unset($data['categories']);
            $comp = Competition::create($data);
            $comp->categories()->attach($categories);
            $competitionModels[] = $comp;
        }

        // ── 6. Regular users with teams & payments ────────────────
        $statuses = [
            \App\Helpers\PaymentStatus::VALID,
            \App\Helpers\PaymentStatus::PENDING,
            \App\Helpers\PaymentStatus::INVALID,
        ];

        // Ensure dummy receipt folder exists
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

        // ── 7. Seminar registrations ──────────────────────────────
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
        $this->command->info('   Super Admin : superadmin@gmail.com / myPassword');
        $this->command->info('   Admin       : admin@gmail.com / myPassword');
        $this->command->info('   Competitions: Web Design, UI/UX, Generative AI');
        $this->command->info('   Users       : 10 dengan team & payment, 5 pendaftar seminar');
    }
}
