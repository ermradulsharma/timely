<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employer_id')->unsigned();
            $table->foreign('employer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('company_name')->default('')->nullable();
            $table->string('position', 50)->default('')->nullable();
            $table->string('title')->default('')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('job_experience_id')->unsigned();
            $table->foreign('job_experience_id')->references('id')->on('job_experiences');
            $table->tinyInteger('min_experience')->default(0);
            $table->tinyInteger('max_experience')->default(0);
            $table->string('city', 50)->default('')->nullable();
            $table->string('state', 50)->default('')->nullable();
            $table->string('address_line_1')->default('')->nullable();
            $table->string('address_line_2')->default('')->nullable();
            $table->string('address_line_3')->default('')->nullable();
            $table->string('skills')->default('')->nullable();
            $table->tinyInteger('number_of_position')->default(1);
            $table->string('contract_type')->default('')->nullable();
            $table->string('type_of_employment')->default('')->nullable();
            $table->string('currency')->default('')->nullable();
            $table->double('hourly_rate', 10, 2)->default(0.00);
            $table->string('qualification')->default('')->nullable();
            $table->text('benefits')->nullable();
            $table->enum('status', ['0', '1'])->default('0')->comment('0 => open, 1 => closed');
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
        Schema::dropIfExists('jobs');
    }
}
