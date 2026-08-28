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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi')->unique();
            $table->enum('jenis', ['masuk', 'jual', 'transfer']);
            
            // Relasi gudang asal & tujuan
            $table->foreignId('gudang_asal_id')->nullable()->constrained('gudang')->nullOnDelete();
            $table->foreignId('gudang_tujuan_id')->nullable()->constrained('gudang')->nullOnDelete();
            
            // Relasi pelanggan (khusus penjualan)
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan')->nullOnDelete();
            
            $table->date('tanggal');
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->enum('status', ['selesai', 'batal'])->default('selesai');
            $table->text('catatan')->nullable();
            
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
