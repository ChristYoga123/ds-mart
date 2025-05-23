<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    public function getNamaAttribute($value)
    {
        return ucwords(str_replace('_', ' ', $value));
    }

    public function produkGolongan()
    {
        return $this->belongsTo(ProdukGolongan::class);
    }

    public function produkBatches()
    {
        return $this->hasMany(ProdukBatch::class);
    }
}
