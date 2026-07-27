<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('payment_seminar_statuses');
        Schema::dropIfExists('payment_seminars');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These tables are no longer needed; down() is a no-op
    }
};
