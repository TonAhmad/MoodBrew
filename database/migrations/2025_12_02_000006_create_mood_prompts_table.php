<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * History percakapan AI untuk mood-based recommendation
     * Menyimpan input user, response AI, dan menu yang direkomendasikan
     */
    public function up(): void
    {
        Schema::create('mood_prompts', function (Blueprint $table) {
            $table->id();

            // Input mood dari customer
            $table->text('user_input');

            // Response dari AI
            $table->text('ai_response');

            // Menu yang direkomendasikan AI (nullable jika AI tidak recommend menu spesifik)
            $table->foreignId('recommended_menu_id')
                ->nullable()
                ->constrained('menu_items')
                ->nullOnDelete();

            // Optional: track user jika login
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Session ID untuk track guest users
            $table->string('session_id')->nullable();

            $table->timestamps();

            // Index untuk analytics
            $table->index('created_at');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_prompts');
    }
};
