<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGene2funcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gene2func', function (Blueprint $table) {
            $table->bigIncrements('jobID');
            $table->string('title');
            $table->integer('snp2gene')->nullable();
            $table->string('snp2geneTitle')->nullable();
            $table->string('email')->default('Not set');
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
        Schema::drop('gene2func');
    }
}
