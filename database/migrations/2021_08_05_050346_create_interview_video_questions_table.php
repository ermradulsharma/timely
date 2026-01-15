<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewVideoQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_video_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('interview_video_id')->unsigned();
            $table->foreign('interview_video_id')->references('id')->on('interview_videos')->onDelete('cascade');
            $table->string('question');
            $table->text('answer');
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
        Schema::dropIfExists('interview_video_questions');
    }
}
