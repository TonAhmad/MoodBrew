<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Promo dadakan yang bisa di-trigger kasir saat dead-hour
     * AI akan generate copywriting untuk promo ini
     */
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Nama promo
            $table->string('promo_code')->unique();
            $table->integer('discount_percentage')->default(0);
            $table->string('trigger_reason')->nullable(); // Alasan trigger
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ai_generated_copy')->nullable(); // AI copywriting result
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            // Index untuk query promo aktif
            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sales');
    }
};
