<?php

namespace App\Console\Commands;
use DB;
use Carbon\Carbon;

use Illuminate\Console\Command;
use App\Http\Controllers\RangoController;

class UpdateRol extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los roles de los usuarios';

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

        \Log::info('Ejecutado');
           $usuarios = DB::table('wp_users')->select('id')->where('rol_id', '>=', 1)->get();
           $rango=new RangoController();
           foreach($usuarios as $usuario){
            $rango->ValidarRango($usuario->id);
           }
        $this->info('Rango Actualizados '.Carbon::now());
    }
}
