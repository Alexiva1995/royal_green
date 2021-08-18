<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRanks extends Model
{
    use HasFactory;

    protected $fillable = [
        'iduser', 'rank_id'
    ];


    /**
     * Permite la informacion del rango asociado
     *
     * @return void
     */
    public function getRank()
    {
        return $this->belongsTo('App\Models\Ranks', 'rank_id', 'id');
    }

    public function getUserRank()
    {
        return $this->belongsTo('App\Models\User', 'iduser', 'id');
    }
}
