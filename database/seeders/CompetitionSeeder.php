<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Event;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pakai event aktif; jika belum ada, ambil yang pertama
        $event = Event::query()->where('is_active', true)->first()
            ?? Event::query()->first();

        Competition::factory(10)->create([
            'event_id' => $event?->id ?? 1,
        ]);
    }
}
