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
                'description' => 'Lomba UI/UX Design merupakan sebuah kompetisi yang bertujuan untuk menguji
kreativitas, kemampuan analisis, serta keterampilan peserta dalam merancang
antarmuka (User Interface) dan pengalaman pengguna (User Experience) yang inovatif,
fungsional, dan berorientasi pada kebutuhan pengguna. Dalam perlombaan ini, peserta
diharapkan mampu menghasilkan rancangan solusi digital berupa prototype
menggunakan perangkat desain seperti Figma atau tools sejenis, dengan
mengedepankan kemudahan penggunaan, estetika visual, serta penyelesaian masalah
sesuai dengan tema yang telah ditentukan.',
                'guide_book'  => 'https://example.com/guidebook/ui-ux.pdf',
                'price'       => 45000,
                'max_members' => 3,
                'deadline'    => '2026-08-15 23:59:59',
            ],
            [
                'name'        => 'Gen AI',
                'slug'        => 'gen-ai',
                'description' => 'Lomba Gen AI (Generative AI Video) merupakan sebuah kompetisi yang
bertujuan untuk menguji kreativitas, kemampuan bercerita (storytelling), serta
keterampilan peserta dalam memanfaatkan teknologi Generative Artificial Intelligence
(GenAI) untuk menghasilkan sebuah video yang inovatif, informatif, dan menarik.
Dalam perlombaan ini, peserta diharapkan mampu mengembangkan ide, menyusun
alur cerita, membuat prompt yang efektif, serta mengintegrasikan berbagai teknologi
AI generatif untuk menghasilkan video yang orisinal sesuai dengan tema.',
                'guide_book'  => 'https://example.com/guidebook/gen-ai.pdf',
                'price'       => 45000,
                'max_members' => 3,
                'deadline'    => '2026-08-15 23:59:59',
            ],
            [
                'name'        => 'Web Design',
                'slug'        => 'web-design',
                'description' => 'Lomba Web Design merupakan sebuah kompetisi yang bertujuan untuk menguji
kreativitas, keterampilan, dan pengetahuan peserta dalam merancang serta membangun
sebuah website yang interaktif dan menarik secara visual. Dalam perlombaan ini,
peserta diharapkan mampu merancang antarmuka (front-end) suatu website dan
diperbolehkan menggunakan framework/library front-end apapun dengan catatan
murni hasil rancangan sendiri dan bukan menggunakan template jadi.',
                'guide_book'  => 'https://example.com/guidebook/web-design.pdf',
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
