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
        Schema::table('SubmitJobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId(['parent_id']);

            $table->foreign('parent_id')
                ->nullable()
                ->references('jobID')
                ->on('SubmitJobs')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('SubmitJobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
