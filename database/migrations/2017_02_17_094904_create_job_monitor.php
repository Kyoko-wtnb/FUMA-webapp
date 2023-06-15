<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobMonitor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('JobMonitor', function (Blueprint $table) {
            $table->bigIncrements('jobID');
            $table->date('created_at');
            $table->date('started_at');
            $table->date('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('JobMonitor');
    }
}
