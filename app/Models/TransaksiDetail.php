<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function produkBatch()
    {
        return $this->belongsTo(ProdukBatch::class);
    }
}
