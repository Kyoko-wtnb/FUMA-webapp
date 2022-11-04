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
            $table->bigIncrements('jobID');
	    $table->string('title');
            $table->string('email')->default('Not set');
            $table->integer('snp2gene');
            $table->string('snp2geneTitle');
            $table->string('status');
            $table->date('created_at');

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
        Schema::dropIfExists('celltype');
    }
}
