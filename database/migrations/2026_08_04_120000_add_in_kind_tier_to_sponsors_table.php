<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE sponsors MODIFY COLUMN tier ENUM('platinum','gold','silver','bronze','in-kind') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sponsors MODIFY COLUMN tier ENUM('platinum','gold','silver','bronze') NOT NULL");
    }
};
