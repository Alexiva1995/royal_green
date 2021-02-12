<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use DB;
// modelos
use App\User;
// controlador
use App\Http\Controllers\ComisionesController;

class CronJobBonoBinario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permite borrar los puntos binarios de los usuarios';

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
        // $comision = new ComisionesController;    
        // $comision->cronjobBinario();

        // $horaActual = Carbon::now()->format('Y-m-d H:i');
        // $this->info('Bonos binarios actualizados '.$horaActual);
    }
}
