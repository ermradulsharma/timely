<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateColumnForeignToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

  
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['job_id']);

        $table->foreign('job_id')
            ->references('id')
            ->on('services_requests')
            ->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('update_column_foreign_to_payments');
    }
}
