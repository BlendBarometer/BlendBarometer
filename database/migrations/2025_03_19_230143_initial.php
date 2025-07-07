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
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('section_name');
            $table->mediumText('info');
        });

        Schema::create('form_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')
                  ->constrained('content')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->mediumText('description')->nullable();
        });

        Schema::create('question_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_section_id')
                  ->constrained('form_section')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('name');
            $table->mediumText('description')->nullable();
        });

        Schema::create('sub_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_category_id')
                  ->constrained('question_category')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('name');
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_category_id')
                  ->constrained('question_category')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->foreignId('sub_category_id')
                  ->nullable()
                  ->constrained('sub_category')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('text');
            $table->mediumText('description')->nullable();
        });

        Schema::create('academy', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->string('abbreviation');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question');
        Schema::dropIfExists('sub_category');
        Schema::dropIfExists('question_category');
        Schema::dropIfExists('form_section');
        Schema::dropIfExists('content');
        Schema::dropIfExists('academy');
    }
};
