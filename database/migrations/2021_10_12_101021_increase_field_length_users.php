<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseFieldLengthUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image', 1000)->change();
            $table->string('device_type', 100)->change();
            $table->string('gender', 100)->change();
            $table->string('account_status', 50)->change();
            $table->string('is_profile_updated', 50)->change();
            $table->string('status', 50)->change();
            $table->string('user_type', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
