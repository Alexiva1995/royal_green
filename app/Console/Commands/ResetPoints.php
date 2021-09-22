<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\RankController;

class ResetPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetea los puntos si no cumple con el rango';

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
     * @return int
     */
    public function handle()
    {
        try {
            Log::info('Inicio Reseteo de Puntos Mensual - '.Carbon::now());
            $wallet = new RankController();
            $wallet->resetPoints();
            Log::info('Fin de Reseteo de Puntos Mensual - '.Carbon::now());
        } catch (\Throwable $th) {
            Log::error('Error Cron Reseteo de Puntos Mensual -> '.$th);
        }
    }
}
