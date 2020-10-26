<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
// modelos
use App\User;
// controlador
use App\Http\Controllers\ComisionesController;

class BonoBinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bono:binary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permite Pagar el bono binario';

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
            $comision = new ComisionesController;
            $users = User::where('rol_id', '!=', 0)->get();
            foreach ($users as $user) {
                // $comision->bonoLiderazgo($user->ID);
                $comision->bonoPorPuntos($user->ID);
            }
            $horaActual = Carbon::now()->format('Y-m-d H:i');
            $this->info('Bono Pagado Correctamente '.$horaActual);
        } catch (\Throwable $th) {
            $this->info($th);
        }
    }
}
