<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Pagos extends Model
{
    protected $table = "pagos";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'iduser', 'username', 'email', 'monto', 'fechasoli', 'fechapago', 'metodo',
         'estado', 'tipopago', 'descuento', 'tipowallet', 'tipo_retiro', 'idrentabilidad',
         'codigo_confirmacion', 'fecha_codigo', 'bkp'
    ];
    
     public function scopeSearch($query, $iduser){
         $query->where(\DB::raw("CONCAT(iduser,'', email)"),"LIKE" ,"%$iduser%");
   

    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }
}