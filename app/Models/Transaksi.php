<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
