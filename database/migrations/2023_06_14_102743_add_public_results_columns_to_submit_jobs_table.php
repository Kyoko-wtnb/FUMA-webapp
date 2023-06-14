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
            $table->boolean('is_public')->default(false);
			$table->string('author')->nullable();
            $table->string('publication_email')->nullable();
			$table->string('phenotype')->nullable();
			$table->string('publication')->nullable();
			$table->string('sumstats_link')->nullable();
			$table->string('sumstats_ref')->nullable();
			$table->longText('notes')->nullable();
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('SubmitJobs', function (Blueprint $table) {
            $table->dropColumn([
                'is_public',
                'author',
                'publication_email',
                'phenotype',
                'publication',
                'sumstats_link',
                'sumstats_ref',
                'notes',
                'published_at'
            ]);
        });
    }
};
