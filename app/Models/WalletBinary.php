<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletBinary extends Model
{
    use HasFactory;

    protected $table = 'wallet_binaries';

    protected $fillable = [
        'iduser', 'referred_id', 'orden_purchase_id',
        'puntos_d', 'puntos_i', 'puntos_reales_d', 'puntos_reales_i', 'side', 'descripcion',
        'status'
    ];

    public function getUserBinary()
    {
        return $this->belongsTo('App\Models\User', 'iduser', 'id');
    }

    public function getReferredBinary()
    {
        return $this->belongsTo('App\Models\User', 'iduser', 'id');
    }
}
