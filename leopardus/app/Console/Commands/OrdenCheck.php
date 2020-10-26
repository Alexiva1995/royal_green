<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\IndexController;
use Carbon\Carbon;

class OrdenCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orden:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permite averiguar si los pagos ya fueron aprobados';

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
            // permite verificar el estado de las ordenes
            $informacion = new IndexController;
            $informacion->ordenesSistema();
            $horaActual = Carbon::now();
            $this->info('Ordenes de compra actualizadas '.$horaActual);
        } catch (\Throwable $th) {
            $this->info($th);
        }
    }
}
