<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractTypeIdToJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->bigInteger('contract_type_id')->unsigned()->nullable()->after('contract_type');
            $table->foreign('contract_type_id')->references('id')->on('contract_types')->onDelete('cascade');
            $table->bigInteger('employment_type_id')->unsigned()->nullable()->after('type_of_employment');
            $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            //
        });
    }
}
