<?php

use App\Helpers\PaymentStatus;
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
        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->unique();
            $table->enum(
                'status',
                [
                    PaymentStatus::INVALID,
                    PaymentStatus::PENDING,
                    PaymentStatus::VALID
                ]
            )
                ->default(PaymentStatus::PENDING);
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_statuses');
    }
};
