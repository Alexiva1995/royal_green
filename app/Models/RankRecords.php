<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankRecords extends Model
{
    use HasFactory;

    protected $table = 'rank_records';

    protected $fillable = [
        'iduser', 'rank_actual_id', 'rank_previou_id',
        'fecha_inicio', 'fecha_fin'
    ];

    /**
     * Permite obtener el rango al que pertenece
     *
     * @return void
     */
    public function getRank()
    {
        return $this->belongsTo('App\Models\Ranks', 'rank_actual_id', 'id');
    }

    /**
     * Permite obtener el usuario al que pertenece
     *
     * @return void
     */
    public function getUserRank()
    {
        return $this->belongsTo('App\Models\User', 'iduser', 'id');
    }
}
