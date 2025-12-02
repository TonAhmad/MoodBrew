<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Detail item dalam setiap pesanan
     * price_at_moment menyimpan harga saat transaksi (snapshot)
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->restrictOnDelete();

            $table->integer('quantity')->default(1);

            // Harga saat transaksi (tidak berubah meski harga menu berubah)
            $table->decimal('price_at_moment', 10, 2);

            // Catatan khusus (misal: "less sugar", "no ice")
            $table->text('note')->nullable();

            $table->timestamps();

            // Index untuk reporting
            $table->index('menu_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
