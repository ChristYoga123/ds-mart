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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_golongan_id')->constrained('produk_golongans')->cascadeOnDelete();
            $table->string('barcode')->nullable()->unique();
            $table->string('nama')->unique();
            $table->unsignedBigInteger('harga_jual_per_pcs')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
