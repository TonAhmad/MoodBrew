<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Tabel transaksi utama dengan hybrid ordering support
     * - Web App: status awal 'pending_payment'
     * - Direct Kasir: bisa langsung 'paid_preparing'
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('table_number')->nullable();
            $table->string('customer_name', 100)->nullable(); // Nama customer untuk walk-in
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->enum('status', [
                'pending_payment',  // Pesanan dari web/kasir, belum bayar
                'preparing',        // Sudah bayar, sedang disiapkan
                'ready',            // Siap diambil/disajikan
                'completed',        // Transaksi selesai
                'cancelled'         // Dibatalkan
            ])->default('pending_payment');

            $table->text('notes')->nullable(); // Catatan pesanan

            // Snapshot mood customer untuk Empathy Radar kasir
            $table->string('customer_mood_summary')->nullable();

            // Metode pembayaran (diisi saat status berubah ke paid)
            $table->enum('payment_method', ['cash', 'qris', 'debit', 'credit', 'other'])->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable(); // Jumlah dibayar
            $table->decimal('change_amount', 12, 2)->nullable(); // Kembalian

            // Nullable karena customer bisa guest (tidak login)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Kasir yang memproses pembayaran
            $table->foreignId('cashier_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Flash sale yang diaplikasikan (jika ada)
            $table->foreignId('applied_flash_sale_id')
                ->nullable()
                ->constrained('flash_sales')
                ->nullOnDelete();

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index untuk dashboard kasir
            $table->index(['status', 'created_at']);
            $table->index('table_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
