<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('provider_id')->nullable();
            $table->string('service_cost')->nullable();
            $table->string('total_cost')->nullable();
            $table->string('reason')->nullable();
            $table->string('request_time')->nullable();
            $table->string('accept_time')->nullable();
            $table->enum('payment_status', [0, 1, 2, 3, 4, 5])->default(0);
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
        Schema::dropIfExists('payment_requests');
    }
}
