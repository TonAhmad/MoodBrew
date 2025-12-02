<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Master data menu cafe dengan flavor_profile untuk AI recommendation
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->enum('category', ['coffee', 'non-coffee', 'pastry', 'main_course']);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_available')->default(true);

            // JSON field untuk AI flavor matching
            // Structure: {"acidity": 1-5, "body": 1-5, "mood_tags": ["relaxing", "energizing", ...]}
            $table->json('flavor_profile')->nullable();

            $table->timestamps();

            // Index untuk pencarian menu
            $table->index(['category', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
