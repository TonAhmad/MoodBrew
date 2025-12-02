<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Silent Social Wall - tempat customer share vibes tanpa tekanan sosmed
     * sentiment_score dari AI untuk filter konten negatif
     */
    public function up(): void
    {
        Schema::create('vibe_entries', function (Blueprint $table) {
            $table->id();

            // Konten vibe yang dishare
            $table->text('content');

            // Skor sentimen dari AI (-1 to 1, atau 0-100)
            // Digunakan untuk auto-moderate konten negatif
            $table->decimal('sentiment_score', 5, 2)->default(0);

            // Apakah di-feature di wall utama (curated by admin/AI)
            $table->boolean('is_featured')->default(false);

            // Status moderasi
            $table->boolean('is_approved')->default(true);

            // Optional: track user jika login
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Anonymous display name
            $table->string('display_name')->nullable();

            $table->timestamps();

            // Index untuk wall display
            $table->index(['is_approved', 'is_featured', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vibe_entries');
    }
};
