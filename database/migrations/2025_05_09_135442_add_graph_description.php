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
        Schema::create('graph_description', function (Blueprint $table) {
            $table->id();
            $table->string('graph_type');
            $table->foreignId('sub_category_id')
                ->nullable()
                ->constrained('sub_category')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->mediumText('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graph_description');
    }
};
