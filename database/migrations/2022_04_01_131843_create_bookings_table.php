<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('provider_send_to')->nullable();
            $table->bigInteger('user_id_send_by')->nullable();
            $table->string('price')->nullable();
            $table->string('lat', 50)->nullable()->default('');
            $table->string('lng', 50)->nullable()->default('');
            $table->string('pickup_address')->nullable();
            $table->string('destionation_address')->nullable();
            $table->enum('status', [0, 1])->default(1);
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
        Schema::dropIfExists('bookings');
    }
}
