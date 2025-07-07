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
        Schema::create('graph_legenda', function (Blueprint $table) {
            $table->id();
            $table->string('color');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('module_level_answer_id')->nullable();
            $table->foreign('module_level_answer_id')
                ->references('id')
                ->on('module_level_answer')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graph_legenda');
    }
};
