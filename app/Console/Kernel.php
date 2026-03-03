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
        Commands\TelegramListenCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run flight search once a day during the day (13:00 UTC)
        $schedule->command('flights:search')
            ->dailyAt('13:00')
            ->withoutOverlapping()
            ->runInBackground();
    }
}
