<?php

namespace App\Console;

use DB;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\StatusUser',
        'App\Console\Commands\OrdenCheck',
        'App\Console\Commands\BonoBinary',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('status:user')->everyMinute()->everyThirtyMinutes();// verifica el estado del usuario - corre cada media hora
        $schedule->command('bono:binary')->everyMinute()->daily();// pago del bono binario - corre una vez al dia
        $schedule->command('orden:check')->everyMinute()->hourly();// verifica el estado de las compras - corre cada hora
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
