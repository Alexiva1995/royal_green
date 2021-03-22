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

class StatusStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar el estado de las compras';

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
        // try {
        //     $tienda = new TiendaController();
        //     $compras = $tienda->ArregloCompra();
        //     foreach ($compras as $compra) {
        //         if ($compra['estado'] == 'En Espera') {
        //             if ($compra['coinpayment'] == 'Pago Existoso') {
        //             //    $tienda->accionSolicitud($compra['idcompra'], 'wc-completed'); 
        //             }
        //             // elseif($compra['coinpayment'] == 'Pago Normal'){
        //             //    $tienda->accionSolicitud($compra['idcompra'], 'wc-completed');
        //             // } 
        //         }
        //     }

        //     $this->info('Compras verificadas '.Carbon::now());
        // } catch (\Throwable $th) {
        //     $this->info($th);
        // }
    }
}
