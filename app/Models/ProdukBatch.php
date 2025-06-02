<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukBatch extends Model
{
    public function setKodeBatchAttribute($value)
    {
        $this->attributes['kode_batch'] = $value ?? 'BATCH-' . now()->format('YmdHis');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function mutasis()
    {
        return $this->hasMany(ProdukMutasi::class);
    }
}
