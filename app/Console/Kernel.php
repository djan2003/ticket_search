<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SearchFlightsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run flight search every 6 hours
        $schedule->command('flights:search')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Alternative: Search 4 times a day at specific times
        // $schedule->command('flights:search')->dailyAt('09:00');
        // $schedule->command('flights:search')->dailyAt('13:00');
        // $schedule->command('flights:search')->dailyAt('17:00');
        // $schedule->command('flights:search')->dailyAt('21:00');
    }
}
