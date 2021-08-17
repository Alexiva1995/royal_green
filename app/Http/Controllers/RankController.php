<?php

namespace App\Http\Controllers;

use App\Models\RankRecords;
use App\Models\Ranks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RankController extends Controller
{
    //

    /**
     * Permite verificar el rango de un usuario
     *
     * @param integer $iduser
     * @return void
     */
    public function testRank()
    {
        Log::info('Inicio Cron CheckRango '.Carbon::now());
        $userRanks = User::all()->where('point_rank', '>', 0);
        foreach ($userRanks as $user) {
            $this->checkRank($user->id);
        }
        Log::info('Fin Cron CheckRango '.Carbon::now());
    }

    public function checkRank(int $iduser)
    {
        $totalRanks = Ranks::all()->count();
        $user = User::find($iduser);
        $rol_actual = $user->rank_id;
        $rol_new = ($rol_actual + 1);
        if ($rol_new <= $totalRanks) {
            $rolCheck = Ranks::find($rol_new);
            if ($user->point_rank >= $rolCheck->points) {
               $saveRanks = $this->saveRanksRecord($rol_new, $rol_actual, $iduser);
               return $saveRanks;
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
        if($rol_new <= 5){
            $this->guardarRank( $rol_new, $rol_actual, $iduser);
        }else{
            switch ($rol_new) {
                case 6:
                    $izquierda = User::where('status', '1')->where('binary_side', 'I')->where('referred_id', $iduser)->where('rank_id', 4)->first();
                    $derecha = User::where('status', '1')->where('binary_side', 'D')->where('referred_id', $iduser)->where('rank_id', 4)->first();
                    if(isset($izquierda) && isset($derecha)){
                        $this->guardarRank( $rol_new, $rol_actual, $iduser);
                    }else{
                        $requisitos = "Necesitas 2 Directos Turquesa para optar por el siguiente rango";
                        return $requisitos;
                    }
                    break;
                case 7:
                    $izquierda = User::where('status', '1')->where('binary_side', 'I')->where('referred_id', $iduser)->where('rank_id', 5)->first();
                    $derecha = User::where('status', '1')->where('binary_side', 'D')->where('referred_id', $iduser)->where('rank_id', 5)->first();
                    if(isset($izquierda) && isset($derecha)){
                        $this->guardarRank( $rol_new, $rol_actual, $iduser);
                    }else{
                        return "Necesitas 2 Directos Amatista"; 
                    }
                    break;
                case 8:
                    $izquierda = User::where('status', '1')->where('binary_side', 'I')->where('referred_id', $iduser)->where('rank_id', 6)->first();
                    $derecha = User::where('status', '1')->where('binary_side', 'D')->where('referred_id', $iduser)->where('rank_id', 6)->first();
                    if(isset($izquierda) && isset($derecha)){
                        $this->guardarRank( $rol_new, $rol_actual, $iduser);
                    }else{
                        return "Necesitas 2 Directos Topacio Élite"; 
                    }
                    break;
                case 9:
                    $izquierda = User::where('status', '1')->where('binary_side', 'I')->where('referred_id', $iduser)->where('rank_id', 7)->first();  
                    $derecha = User::where('status', '1')->where('binary_side', 'D')->where('referred_id', $iduser)->where('rank_id', 7)->first();
                    if(isset($izquierda) && isset($derecha)){
                        $this->guardarRank( $rol_new, $rol_actual, $iduser);
                    }else{
                        return "Necesitas 2 Directos Zafiro"; 
                    }
                    break;
                case 10:
                    $izquierda = User::where('status', '1')->where('binary_side', 'I')->where('referred_id', $iduser)->where('rank_id', 8)->first();
                    $derecha = User::where('status', '1')->where('binary_side', 'D')->where('referred_id', $iduser)->where('rank_id', 8)->first();
                    if(isset($izquierda) && isset($derecha)){
                        $this->guardarRank( $rol_new, $rol_actual, $iduser);
                    }else{
                        return "Necesitas 2 Directos Rubíes"; 
                    }
                    break;
                case 11:
                    $izquierda = User::where('status', '1')->where('binary_side', 'I')->where('referred_id', $iduser)->where('rank_id', 9)->first();
                    $derecha = User::where('status', '1')->where('binary_side', 'D')->where('referred_id', $iduser)->where('rank_id', 9)->first();
                    if(isset($izquierda) && isset($derecha)){
                        $this->guardarRank( $rol_new, $rol_actual, $iduser);
                    }else{
                        return "Necesitas 2 Directos Esmeraldas"; 
                    }
                    break;
                default:
      
                    break;
            }
        }
    }  

    public function guardarRank(int $rol_new, int $rol_actual, int $iduser)
    {
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
