<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrowseGwasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('BrowseGwas', function (Blueprint $table) {
            $table->increments('gwasID');
			$table->string('title');
			$table->string('PMID',8);
			$table->integer('ear')->nullable();
            $table->date('created_at')->nullable();
			$table->date('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('BrowseGwas');
    }
}
