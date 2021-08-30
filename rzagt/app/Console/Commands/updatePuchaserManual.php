<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class updatePuchaserManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:purchasemanual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $pagos = DB::table('pagos')->where('estado', 0)->select('iduser')->get();
        foreach ($pagos as $pago) {
            dump($pago->iduser);
            $verificarTipo = DB::table('log_rentabilidad')->where([
                ['iduser', '=', $pago->iduser],				
            ])->get()->last();
            $checkPago = DB::table('wp_posts')->where([
                ['ID', '=', $verificarTipo->idcompra],
                ['to_ping', '=', 'Manual'],
            ])->first();
            if ($checkPago != null) {
                DB::table('log_rentabilidad')->where([
                    ['iduser', '=', $pago->iduser],
                    ['idcompra', '=', $checkPago->ID]
                ])->update(['nivel_minimo_cobro' => 7]);
                dump('procesado');
            }
        }
    }
}
