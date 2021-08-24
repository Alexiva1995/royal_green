<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    protected $table = 'packages';

    protected $fillable = [
        'name', 'group_id', 'price', 'description', 'status', 'minimum_deposit', 'expired'
    ];

      /**
     * Permite obtener el grupo al que pertenece
     *
     * @return void
     */
    public function getGroup()
    {
        return $this->belongsTo('App\Models\Groups', 'group_id', 'id');
    }

    /**
     * Permite obtener todos los paquetes de un grupo
     *
     * @return void
     */
    public function E()
    {
        return $this->hasMany('App\Models\OrdenPurchases', 'package_id');
    }

    public function img()
    {
        $imagen = '';
        if($this->price == 50){
            $imagen = 'rg50.png';
        }elseif($this->price == 100){
            $imagen = 'rg100.png';
        }elseif($this->price == 250){
            $imagen = 'rg250.png';
        }elseif($this->price == 500){
            $imagen = 'rg500.png';
        }elseif($this->price == 1000){
            $imagen = 'rg1000.png';
        }elseif($this->price == 2000){
            $imagen = 'rg2000.png';
        }elseif($this->price == 5000){
            $imagen = 'rg5000.png';
        }elseif($this->price == 10000){
            $imagen = 'rg10000.png';
        }elseif($this->price == 25000){
            $imagen = 'rg25000.png';
        }elseif($this->price == 50000){
            $imagen = 'rg50000.png';
        }

        return asset('assets/img/royal_green/paquetes/'.$imagen);
    }
}
