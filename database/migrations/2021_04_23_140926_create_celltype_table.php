<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCelltypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('celltype', function (Blueprint $table) {
            $table->increments('jobID');
			$table->string('title')->nullable();
			$table->string('email');
			$table->integer('snp2gene');
			$table->string('snp2geneTitle')->nullable();
			$table->string('status')->nullable();
			$table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('celltype');
    }
}
