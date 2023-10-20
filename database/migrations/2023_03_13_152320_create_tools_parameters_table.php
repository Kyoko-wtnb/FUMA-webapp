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
        Schema::create('tools_parameters', function (Blueprint $table) {
            // db related
            $table->id();
            $table->timestamps();
            $table->foreignId('tool_id')
            ->constrained('tools')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            // TODO: add uniqie key in between id and param_name

            // General settings of the parameter
            $table->string('param_name');
            $table->string('param_full_name');
            $table->string('level');
            $table->string('group');
            $table->unsignedTinyInteger('group_position')->default(0); 

            // Command line settings
            $table->string('flag')->nullable();
            $table->string('delimiter')->nullable();
            $table->unsignedTinyInteger('command_position')->default(2);
            $table->boolean('required')->default(false);
            $table->boolean('auxiliary')->default(false);

            // arguments related settings
            $table->string('type'); //
            $table->text('default_arguments')->nullable();
            $table->string('default_value')->nullable();
            $table->boolean('multiple')->default(false);
            $table->boolean('batchable')->default(false);

            // help related settings
            $table->text('help_test')->nullable();
            $table->string('placeholder')->nullable();
            
            // other settings
            $table->boolean('visible')->default(true);

            $table->unique(['tool_id', 'param_name']);
            $table->unique(['tool_id', 'flag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools_parameters');

        Schema::table('tools_parameters', function (Blueprint $table) {
            $table->dropConstrainedForeignId(['tool_id']);
            $table->dropUnique(['tool_id', 'param_name']);
            $table->dropUnique(['tool_id', 'flag']);
        });
    }
};
