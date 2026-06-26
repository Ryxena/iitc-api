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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->uuid('leader_id');
            $table->unsignedBigInteger('competition_id');
            $table->string('name')->nullable();
            $table->string('code')->unique()->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('avatar')->nullable();
            $table->string('submission')->nullable();
            $table->string('submission_file_name')->nullable();
            $table->timestamps();

            $table->foreign('leader_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('competition_id')
                ->references('id')
                ->on('competitions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
