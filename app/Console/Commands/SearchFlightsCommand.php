<?php

namespace App\Console\Commands;

use App\Services\FlightSearchService;
use Illuminate\Console\Command;

class SearchFlightsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flights:search
                            {--origin=BUS : Origin airport IATA code}
                            {--departure-day=5 : Day of week for departure (0-6, where 0 is Sunday)}
                            {--return-day=0 : Day of week for return (0-6, where 0 is Sunday)}
                            {--days-ahead=90 : Number of days ahead to search}
                            {--limit=10 : Maximum number of results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search for flights to visa-free countries and send results to Telegram';

    protected FlightSearchService $searchService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FlightSearchService $searchService)
    {
        parent::__construct();
        $this->searchService = $searchService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting flight search...');

        $origin = $this->option('origin');
        
        // Weekend combinations: Friday/Saturday departure, Sunday/Monday return
        $departureDays = [5, 6]; // Friday, Saturday
        $returnDays = [0, 1];    // Sunday, Monday
        
        $daysAhead = (int) $this->option('days-ahead');
        $limit = (int) $this->option('limit');

        $this->info("Parameters:");
        $this->line("  Origin: {$origin}");
        $this->line("  Departure days: Friday, Saturday");
        $this->line("  Return days: Sunday, Monday");
        $this->line("  Valid combinations:");
        $this->line("    - Friday → Sunday (2 days, same week)");
        $this->line("    - Friday → Monday (3 days, next week)");
        $this->line("    - Saturday → Monday (2 days, next week)");
        $this->line("  Days ahead: {$daysAhead}");
        $this->line("  Limit: {$limit}");

        $success = $this->searchService->searchAndNotify(
            $origin,
            $departureDays,
            $returnDays,
            $daysAhead,
            $limit
        );

        if ($success) {
            $this->info('✓ Search completed successfully! Results sent to Telegram.');
            return Command::SUCCESS;
        } else {
            $this->error('✗ Search failed or could not send to Telegram.');
            return Command::FAILURE;
        }
    }

    /**
     * Get day name from day number
     *
     * @param int $day
     * @return string
     */
    protected function getDayName(int $day): string
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        return $days[$day] ?? 'Unknown';
    }
}
