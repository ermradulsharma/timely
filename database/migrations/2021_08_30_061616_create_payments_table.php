<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('booking_id')->unsigned()->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->bigInteger('payment_by')->unsigned()->nullable();
            $table->foreign('payment_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('card_id')->unsigned()->nullable();
            $table->foreign('card_id')->references('id')->on('cards');
            $table->string('charge_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->double('amount', 10, 2)->default(0.00);
            $table->string('currency', 20)->default('usd')->nullable();
            $table->string('payment_message')->nullable();
            $table->string('payment_status', 20)->nullable();
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
        Schema::dropIfExists('payments');
    }
}
