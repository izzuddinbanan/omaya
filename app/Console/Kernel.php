<?php

namespace App\Console;

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
        Commands\OmayaScheduler::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // withoutOverlapping to prevent scheduler run before previous job complete
        $schedule->command('omaya:main')->everyMinute()->withoutOverlapping();
        $schedule->command('omaya:service')->everyMinute()->withoutOverlapping();
        $schedule->command('report:dwell')->everyMinute()->withoutOverlapping();
        $schedule->command('report:general')->everyMinute()->withoutOverlapping();
        $schedule->command('report:heatmap')->everyMinute()->withoutOverlapping();
        $schedule->command('report:device-controller')->everyMinute()->withoutOverlapping();
        $schedule->command('report:cross-visit')->everyMinute()->withoutOverlapping();
        $schedule->command('omaya:blacklist')->everyMinute()->withoutOverlapping();
        $schedule->command('omaya:log-clear')->daily()->withoutOverlapping();


        $schedule->command('omaya:pre-report')->hourlyAt(45)->withoutOverlapping();

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
