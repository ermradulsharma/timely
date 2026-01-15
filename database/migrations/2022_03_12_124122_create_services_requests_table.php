<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesRequestsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('services_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('services_provider_id')->nullable();
            $table->bigInteger('service_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('note')->nullable();
            $table->string('address')->nullable();
            $table->string('lati')->nullable();
            $table->string('long')->nullable();
            $table->string('request_time')->nullable();
            $table->string('accept_time')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('request_status')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('services_requests');
    }
}
