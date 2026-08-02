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

        $competitions = [
            [
                'name'        => 'UI UX',
                'slug'        => 'ui-ux',
                'description' => 'Kompetisi merancang UI/UX inovatif dan fungsional berupa prototype (Figma/tools sejenis), mengutamakan usability, estetika visual, dan solusi sesuai tema.',
                'guide_book'  => 'https://example.com/guidebook/ui-ux.pdf',
                'price'       => 45000,
                'max_members' => 3,
                'deadline'    => '2026-08-15 23:59:59',
            ],
            [
                'name'        => 'Gen AI',
                'slug'        => 'gen-ai',
                'description' => 'Kompetisi kreativitas dan storytelling menggunakan Generative AI untuk menciptakan video orisinal, inovatif, dan sesuai tema mulai dari ide, alur cerita, hingga prompt engineering.',
                'guide_book'  => 'https://example.com/guidebook/gen-ai.pdf',
                'price'       => 45000,
                'max_members' => 3,
                'deadline'    => '2026-08-15 23:59:59',
            ],
            [
                'name'        => 'Web Design',
                'slug'        => 'web-design',
                'description' => 'Kompetisi merancang website interaktif dan visual menarik, fokus front-end, bebas framework/library, dengan desain orisinal tanpa template.',
                'guide_book'  => 'https://drive.google.com/file/d/1yEVONs2ohXLQEPAM4QrBdd6WT_ZBbse6/view?usp=sharing',
                'price'       => 45000,
                'max_members' => 3,
                'deadline'    => '2026-08-15 23:59:59',
            ],
        ];

        foreach ($competitions as $data) {
            Competition::create(array_merge($data, [
                'event_id' => $event?->id ?? 1,
            ]));
        }
    }
}
