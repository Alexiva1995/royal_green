<?php

namespace App\Console\Commands;

use App\Http\Controllers\RangoController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class ResetPointRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetpoint:rank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permite Resetear los puntos de los rango mensualemente';

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
        Log::info('Ejecutado ResetPointRank ' . Carbon::now());
        $rango = new RangoController();
        $rango->resetPoints();
        Log::info('Rango Reseteados -> ' . Carbon::now());
    }
}
