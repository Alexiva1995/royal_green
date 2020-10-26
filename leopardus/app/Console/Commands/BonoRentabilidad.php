<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\FichasInactiva;
use Carbon\Carbon;
use App\Http\Controllers\ActivacionController;
use App\Http\Controllers\ComisionesController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\RangoController;

class BonoRentabilidad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bono:rentabilidad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permite pagar el bono de la rentabilidad mensualmente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $comisiones = new ComisionesController;
            $comisiones->rentabilidadMensual();

            $this->info('Bono de Rentabilidad pagado '. Carbon::now());
        } catch (\Throwable $th) {
            $this->info($th);
        }
    }
}
