<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customer Interactions table untuk Empathy Radar feature
 * Menyimpan interaksi pelanggan (keluhan, feedback, pujian) beserta sentimen analysis
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            
            // Interaction details
            $table->enum('interaction_type', ['complaint', 'feedback', 'praise', 'question'])
                  ->default('feedback');
            $table->enum('channel', ['in-store', 'online', 'phone'])->default('in-store');
            $table->text('customer_message');
            
            // Sentiment analysis
            $table->string('sentiment_type', 50)->default('neutral'); // positive, neutral, negative, happy, angry, etc.
            $table->decimal('sentiment_score', 3, 2)->default(0.50); // 0.00 to 1.00
            $table->text('ai_analysis')->nullable(); // Full AI analysis
            $table->text('suggested_response')->nullable(); // AI suggested response
            
            // Staff handling
            $table->text('staff_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['sentiment_type', 'handled_by']);
            $table->index('interaction_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_interactions');
    }
};
