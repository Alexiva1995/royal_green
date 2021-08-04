<?php

namespace App\Http\Controllers;

use App\Models\RankRecords;
use App\Models\Ranks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class RankController extends Controller
{
    //

    /**
     * Permite verificar el rango de un usuario
     *
     * @param integer $iduser
     * @return void
     */
    public function checkRank(int $iduser)
    {
        $totalRanks = Ranks::all()->count();
        $user = User::find($iduser);
        $rol_actual = $user->rank_id;
        $rol_new = ($rol_actual + 1);
        if ($rol_new <= $totalRanks) {
            $rolCheck = Ranks::find($rol_new);
            if ($user->point_rank >= $rolCheck->points) {
                $this->saveRanksRecord($rol_new, $rol_actual, $iduser);
            }
        }
    }

    /**
     * Permite actualizar el rango y guardar el registro del mismo 
     *
     * @param integer $rol_new
     * @param integer $rol_actual
     * @param integer $iduser
     * @return void
     */
    public function saveRanksRecord(int $rol_new, int $rol_actual, int $iduser)
    {

        // verifica el rango anterior 
        RankRecords::where([
            ['iduser', '=', $iduser],
            ['rank_actual_id', '=', $rol_actual],
            ['fecha_fin', '=', null]
        ])->update(['fecha_fin' => Carbon::now()]);

        // registra un nuevo rango
        RankRecords::create([
            'iduser' => $iduser, 
            'rank_actual_id' => $rol_new,
            'rank_previou_id' => ($rol_actual == 0)? null : $rol_actual,
            'fecha_inicio' => Carbon::now(),
        ]);

        // actualiza el rango
        User::where('id', $iduser)->update(['rank_id' => $rol_new]);   
    }  
}
