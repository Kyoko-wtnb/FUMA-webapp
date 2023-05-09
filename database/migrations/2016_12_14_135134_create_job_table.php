<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SubmitJobs', function (Blueprint $table) {
            $table->bigIncrements('jobID');
            $table->string('email')->default('Not set');
            $table->string('title')->default('Not set');
            $table->date('created_at');
            $table->date('updated_at');
            $table->string('status')->default('NEW');

            // Add indexes
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('SubmitJobs');
    }
}
