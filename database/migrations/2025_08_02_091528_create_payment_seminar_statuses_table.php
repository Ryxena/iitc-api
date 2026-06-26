<?php

use App\Helpers\PaymentSeminarStatus;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_seminar_statuses', function (Blueprint $table) {
            $table->uuid('user_id')->unique();
            $table->enum(
                'status',
                [
                    PaymentSeminarStatus::INVALID,
                    PaymentSeminarStatus::PENDING,
                    PaymentSeminarStatus::VALID
                ]
            )
                ->default(PaymentSeminarStatus::PENDING);
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_seminar_statuses');
    }
};
