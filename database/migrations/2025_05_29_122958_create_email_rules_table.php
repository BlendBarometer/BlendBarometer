<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_rules', function (Blueprint $table) {
            $table->id();
            $table->string('academy_name')->nullable(); // null = default rule
            $table->string('email');
            $table->timestamps();

            $table->foreign('academy_name')
                  ->references('name')->on('academy')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->unique(['academy_name', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_rules');
    }
};