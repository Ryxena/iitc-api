<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::create([
            'name'        => 'IITC 2026',
            'description' => 'Indonesian IT Competition 2026.',
            'is_active'   => true,
        ]);
    }
}
