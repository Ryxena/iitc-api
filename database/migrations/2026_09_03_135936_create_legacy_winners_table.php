<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legacy_winners', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('project_name');
            $table->text('project_description')->nullable();
            $table->string('institution')->nullable();
            $table->string('competition_name');
            $table->integer('rank');
            $table->string('award_title');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_winners');
    }
};
