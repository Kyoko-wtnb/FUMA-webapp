<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGwasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gwasDB', function (Blueprint $table) {
            $table->increments('PMID');
            $table->integer('Year');
            $table->string('File');
            $table->string('website');
            $table->string('Domain');
            $table->string('ChapterLevel');
            $table->string('SubchapterLevel');
            $table->string('Trait');
            $table->string('Population');
            $table->string('Ncase');
            $table->string('Ncontrol');
            $table->string('N INTEGER');
            $table->string('Note TEXT');
            $table->string('Fileformat');
            $table->string('SNPh2');
            $table->string('hg19');
            $table->string('DateAdded');
            $table->string('DateLastModified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gwasDB');
    }
}

