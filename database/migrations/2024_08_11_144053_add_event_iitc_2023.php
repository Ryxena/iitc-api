<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert into events table
        DB::table('events')->insert([
            'name' => 'IITC 2023',
            'description' => 'iitc 2023',
            'is_active' => 0,
            'created_at' => '2024-08-05 13:37:59',
            'updated_at' => '2024-08-05 13:37:59',
        ]);

        // Update competitions table
        DB::table('competitions')
            ->whereNull('event_id')
            ->update(['event_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('events')
        ->where('name', 'IITC 2023')
        ->where('description', 'iitc 2023')
        ->delete();

    // Set event_id back to NULL in competitions table
    DB::table('competitions')
        ->where('event_id', 1)
        ->update(['event_id' => null]);
    }
};
