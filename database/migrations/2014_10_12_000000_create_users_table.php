<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 255)->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('account_status', [0, 1])->default(1)->comment('0 => private, 1 => open');
            $table->string('country_code', 10)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('city', 50)->nullable()->default('');
            $table->string('state', 50)->nullable()->default('');
            $table->string('country', 50)->nullable()->default('');
            $table->string('address')->nullable()->default('');
            $table->string('pincode', 50)->nullable()->default('');
            $table->string('age', 10)->nullable()->default('');
            $table->string('gender', 10)->nullable()->default('');
            $table->string('image')->nullable()->default('');
            $table->string('lat', 50)->nullable()->default('');
            $table->string('lng', 50)->nullable()->default('');
            $table->string('device_type', 10)->nullable()->default('');
            $table->text('device_token')->nullable();
            $table->text('summary')->nullable();
            $table->string('company_name')->nullable();
            $table->string('position', 50)->nullable();
            $table->enum('status', [0, 1])->default(1);
            $table->enum('user_type', ['user', 'admin', 'provider'])->default('user');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            // Added columns from add_col migrations
            $table->string('geolocation')->nullable();
            $table->string('stripe_connected_account_id')->nullable();
            $table->string('print_name')->nullable();
            $table->string('card_payments', 50)->nullable();
            $table->string('transfers', 50)->nullable();
            $table->date('background_check_date')->nullable();
            $table->string('background_check_details')->nullable();
            $table->boolean('background_check_status')->default(false);
            $table->text('accepted_terms_conditions')->nullable();
            $table->boolean('address_verified')->default(false);
            $table->boolean('bank_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
