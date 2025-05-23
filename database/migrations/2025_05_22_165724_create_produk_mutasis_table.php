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
        Schema::create('produk_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_batch_id')->constrained('produk_batches')->cascadeOnDelete();
            $table->date('tanggal_mutasi');
            $table->enum('jenis_mutasi', ['masuk', 'keluar']);
            $table->bigInteger('jumlah_mutasi'); // bisa negatif atau positif
            $table->longText('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_mutasis');
    }
};
