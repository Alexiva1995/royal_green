<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    protected $table = 'inversions';

    protected $fillable = [
        'package_id', 'invertido',
        'ganacia', 'retiro', 'capital', 'progreso',
        'fecha_vencimiento', 'iduser', 'ganancia_acumulada',
        'restante', 'max_ganancia', 'rentabilidad'
    ];

    
     /**
     * Permite obtener al usuario de una Inversion
     *
     * @return void
     */
    public function getInversionesUser()
    {
        return $this->belongsTo('App\Models\User', 'iduser', 'id');
    }

    /**
     * Permite obtener el paquete de una inversion
     *
     * @return void
     */
    public function getPackageOrden()
    {
        return $this->belongsTo('App\Models\Packages', 'package_id');
    }

    /**
     * Permite obtener la orden asociada
     *
     * @return void
     */
    public function getOrdenInversion()
    {
        return $this->hasMany('App\Models\OrdenPurchases', 'inversion_id');
    }

    public function progreso()
    {
        if(isset($this->max_ganancia) && isset($this->restante)){
            $total = $this->max_ganancia - $this->restante;

            $operacion = ($total * 100) / $this->max_ganancia;
        }else{
            $operacion = 0;
        }

        return $operacion;
    }
}
