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
            $table->timestamp('removed_at')->nullable();

            $table->foreignId('removed_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('SubmitJobs', function (Blueprint $table) {
            $table->dropColumn('removed_at');
            
            $table->dropConstrainedForeignId('removed_by');
        });
    }
};
