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
        Schema::create('module_information_field', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->text('placeholder')->nullable();
            $table->unsignedInteger('maxlength')->default(2000);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('module_information_answer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('module_information_field_id')
                ->constrained('module_information_field')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->mediumText('answer')->nullable();
            $table->timestamps();

            $table->unique(
                ['user_id', 'module_information_field_id'],
                'module_info_answer_user_field_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_information_answer');
        Schema::dropIfExists('module_information_field');
    }
};
