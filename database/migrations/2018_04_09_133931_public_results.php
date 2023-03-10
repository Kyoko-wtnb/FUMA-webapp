<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PublicResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('PublicResults', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
			$table->integer('jobID');
			$table->integer('g2f_jobID');
			$table->string('title');
			$table->string('author');
			$table->string('email');
			$table->string('phenotype');
			$table->string('publication');
			$table->string('sumstats_link');
			$table->string('sumstats_ref');
			$table->string('notes');
			$table->date('created_at');
			$table->date('update_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('PublicResults', function (Blueprint $table) {
            Schema::drop('PublicResults');
        });
    }
}
