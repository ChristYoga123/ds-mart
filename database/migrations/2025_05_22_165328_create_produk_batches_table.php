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
        Schema::create('produk_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->string('kode_batch')->nullable()->unique();
            $table->unsignedBigInteger('harga_beli_per_pcs')->default(0);
            $table->unsignedBigInteger('stok_pcs_tersedia')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_batches');
    }
};
