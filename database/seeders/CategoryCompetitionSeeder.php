<?php

namespace Database\Seeders;

use App\Models\CategoryCompetition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryCompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryCompetition::factory(10)->create();
    }
}
