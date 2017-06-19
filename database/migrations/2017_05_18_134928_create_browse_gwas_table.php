<?php

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
        Schema::table('BrowseGwas', function (Blueprint $table) {
            $table->bigIncrements('gwasID');
			$table->strong('title');
			$table->string('PMID');
			$table->string('year');
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
        Schema::table('BrowseGwas', function (Blueprint $table) {
            Schema::drop('BrowseGwas');
        });
    }
}
