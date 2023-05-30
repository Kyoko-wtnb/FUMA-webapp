<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('celltype', function (Blueprint $table) {
            $table->integer('snp2gene')->nullable()->change();
            $table->string('snp2geneTitle')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('celltype', function (Blueprint $table) {
            //
        });
    }
};
