<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukBatch extends Model
{
    public function setKodeBatchAttribute($value)
    {
        $this->attributes['kode_batch'] = $this->attributes['kode_batch'] ?? 'BATCH-' . now()->format('YmdHis');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
