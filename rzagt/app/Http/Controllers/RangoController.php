<?php

namespace App\Http\Controllers;

use App\User;
use App\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Rol;





class RangoController extends Controller
{
	function __construct()
	{
		
    }
	
	/**
	 * Permite verificar el rango de los usuarios
	 *
	 * @param integer $iduser
	 * @return void
	 */
	public function checkRango(int $iduser)
	{
		$user = User::find($iduser);
		$rangonew = $user->rol_id + 1;
		if ($rangonew < 12) {
			$rolnuevo = Rol::find($rangonew);
			$cantRequisito = 0;
			$cantAprobado = 0;

			// verificar los puntos necesario para la subida de red
			if ($rolnuevo->grupal > 0) {
				$cantRequisito++;
				if ($this->verificarPuntos($iduser, $rolnuevo->grupal) == 1) {
					$cantAprobado++;
				}
			}

			// Verifica si cumple con los rangos necesario en su red
			if ($rolnuevo->referidos > 0) {
				$cantRequisito++;
				if ($this->verificarRangosUser($iduser, $rolnuevo->rolnecesario, $rolnuevo->referidos) == 1) {
					$cantAprobado++;
				}
			}

			// Si cumple las condicion se actualiza el rango
			if ($rolnuevo->rolprevio == $user->rol_id && $cantRequisito == $cantAprobado) {
				$this->updateRank($iduser, $rangonew);
			}
		}
	}

	/**
	 * Permite actualizar el nivel del usuario
	 *
	 * @param integer $iduser
	 * @param integer $rol_nuevo
	 * @return void
	 */
	public function updateRank($iduser, $rol_nuevo)
	{
		User::where('ID', $iduser)->update(['rol_id' => $rol_nuevo]);
	}

	/**
	 * Permite verificar la cantidad de puntos necesarios
	 *
	 * @param integer $iduser - usuario a verificar
	 * @param integer $requisito - puntos necesario para subir de rango
	 * @return integer
	 */
	public function verificarPuntos($iduser, $requisito): int
	{
		$result = 0;
		$fecha = Carbon::now();
		$puntos = Wallet::where('iduser', $iduser)
						->whereDate('created_at', '>=', $fecha->subMonths(3))
						->get()->sum('puntos');
		if ($puntos >= $requisito ) {
			$result = 1;
		}

		return $result;
	}

	/**
	 * Permite verificar el ranfo del usuario
	 *
	 * @param integer $iduser
	 * @param integer $rangoRequisto
	 * @param integer $cantrequisito
	 * @return integer
	 */
	public function verificarRangosUser(int $iduser, int $rangoRequisto, int $cantrequisito): int
	{
		$result = 0;
		$referidos = User::where([
			['referred_id', '=', $iduser],
			['rol_id', '=', $rangoRequisto],
			['status', '=', 1]
		])->get()->count('ID');

		if ($referidos >= $cantrequisito) {
			$result = 1;
		}

		return $result;
	}

	/**
	 * Permite saber el progreso para alcanzar un nuevo nivel
	 *
	 * @param integer $iduser
	 * @return array
	 */
	public function getPointRango($iduser): array
	{
		$user = User::find($iduser);
		$rangonew = $user->rol_id + 1;
		$data = [];
		if ($rangonew < 12) {
			$rolnuevo = Rol::find($rangonew);
			$fecha = Carbon::now();
			$puntos = Wallet::where('iduser', $iduser)
							->whereDate('created_at', '>=', $fecha->subMonths(3))
							->get()->sum('puntos');
			$progresoRanfo = (($puntos * 100)  / 1);
			if ($rolnuevo->grupal) {
				$progresoRanfo = (($puntos * 100)  / $rolnuevo->grupal);
			}
		}
		$rangos = Rol::where('id', '>', 0)->select('id', 'name', 'imagen')->orderBy('id', 'asc')->get();

		$data = [
			'puntos' => $puntos,
			'progreso' => $progresoRanfo,
			'rangos' => $rangos,
			'total' => $rolnuevo->grupal
		];

		return $data;
	}

}
