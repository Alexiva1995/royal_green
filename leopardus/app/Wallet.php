<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = "walletlog";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'iduser', 'usuario', 'descripcion', 'debito', 'credito', 'balance', 'descuento', 'tipotransacion', 'puntos',
         'puntosI', 'puntosD', 'tantechcoin', 'creditocoin'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
}