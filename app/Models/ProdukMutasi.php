<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukMutasi extends Model
{
    public function produkBatch()
    {
        return $this->belongsTo(ProdukBatch::class);
    }
}
