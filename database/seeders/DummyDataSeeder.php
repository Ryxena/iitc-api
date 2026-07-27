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
        $this->command->info('Creating 10 Users and their Teams (this may take a minute due to password hashing)...');
        $users = User::factory(10)->create();
        $teams = collect();

        foreach ($users as $user) {
            $user->assignRole('User');
            $team = Team::factory()->create([
                'leader_id' => $user->id,
                'competition_id' => fake()->numberBetween(1, 10),
            ]);
            $teams->push($team);
        }

        $this->command->info('Creating 30 Members for the Teams (this may take a few minutes)...');
        $members = User::factory(30)->create();
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

        $this->command->info('Creating dummy payments and payment statuses for testing dashboard...');
        
        // Buat folder jika belum ada
        $receiptDir = storage_path('app/public/receipt');
        if (!file_exists($receiptDir)) {
            mkdir($receiptDir, 0755, true);
        }

        // Buat satu file dummy image menggunakan GD library bawaan PHP
        $dummyImageName = 'dummy_receipt_seeder.png';
        $dummyImagePath = $receiptDir . '/' . $dummyImageName;
        
        if (!file_exists($dummyImagePath)) {
            $image = imagecreate(600, 800);
            imagecolorallocate($image, 30, 33, 48); // background color
            $textColor = imagecolorallocate($image, 200, 200, 255);
            imagestring($image, 5, 180, 350, "BUKTI PEMBAYARAN DUMMY", $textColor);
            imagestring($image, 5, 230, 380, "IITC SEEDER", $textColor);
            imagepng($image, $dummyImagePath);
            imagedestroy($image);
        }

        $statuses = [\App\Helpers\PaymentStatus::VALID, \App\Helpers\PaymentStatus::PENDING, \App\Helpers\PaymentStatus::INVALID];
        
        foreach ($teams as $index => $team) {
            // Gunakan format URL yang sama dengan logic Controller: Storage::disk('public')->url()
            $receiptUrl = \Illuminate\Support\Facades\Storage::disk('public')->url('receipt/' . $dummyImageName);

            \App\Models\Payment::query()->create([
                'team_id'          => $team->id,
                'transfer_receipt' => $receiptUrl,
            ]);

            $status = $statuses[$index % 3];
            \App\Models\PaymentStatus::query()->create([
                'team_id' => $team->id,
                'status'  => $status,
                'reason'  => ($status === \App\Helpers\PaymentStatus::INVALID) ? 'Bukti bayar blur atau nominal tidak sesuai (Contoh penolakan)' : '',
            ]);
        }
    }
}

