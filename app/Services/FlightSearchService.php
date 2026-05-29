<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FlightSearchService
{
    protected AviasalesService $aviasalesService;
    protected TelegramService $telegramService;

    public function __construct(AviasalesService $aviasalesService, TelegramService $telegramService)
    {
        $this->aviasalesService = $aviasalesService;
        $this->telegramService = $telegramService;
    }

    /**
     * Search flights to visa-free countries
     *
     * @param string $origin Origin IATA code
     * @param int $departureDayOfWeek Day of week for departure (0-6)
     * @param int $returnDayOfWeek Day of week for return (0-6)
     * @param int $daysAhead How many days ahead to search
     * @param int $limit Maximum number of results
     * @return array
     */
    public function searchVisaFreeDestinations(
        string $origin = 'BUS',
        int $departureDayOfWeek = 5,
        int $returnDayOfWeek = 0,
        int $daysAhead = 90,
        int $limit = 10
    ): array {
        $results = [];
        $countries = config('countries.visa_free');

        Log::info('Starting visa-free search', [
            'origin' => $origin,
            'departure_day' => $departureDayOfWeek,
            'return_day' => $returnDayOfWeek,
        ]);

        // Get all destination cities (limit to 1 per country for speed)
        $destinations = $this->getDestinationCities($countries, 1);
        
        // Limit search to first 4 weeks only (instead of 90 days) for speed
        $searchDays = min($daysAhead, 28);

        foreach ($destinations as $destination) {
            // Early exit if we have enough results
            if (count($results) >= $limit * 2) {
                break;
            }

            // Skip self-route (e.g. EVN is both an origin and a destination)
            if ($destination === $origin) {
                continue;
            }

            $flights = $this->searchDestination(
                $origin,
                $destination,
                $departureDayOfWeek,
                $returnDayOfWeek,
                $searchDays
            );

            if (!empty($flights)) {
                $results = array_merge($results, $flights);
            }
        }

        // Sort by price
        usort($results, function ($a, $b) {
            return ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX);
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * Search specific destination
     *
     * @param string $origin
     * @param string $destination
     * @param int $departureDayOfWeek
     * @param int $returnDayOfWeek
     * @param int $daysAhead
     * @return array
     */
    protected function searchDestination(
        string $origin,
        string $destination,
        int $departureDayOfWeek,
        int $returnDayOfWeek,
        int $daysAhead
    ): array {
        $results = [];
        $today = Carbon::now();

        // Search for the next few occurrences of the departure day
        for ($i = 0; $i < $daysAhead; $i++) {
            $date = $today->copy()->addDays($i);
            
            // Check if this is the desired departure day
            if ($date->dayOfWeek !== $departureDayOfWeek) {
                continue;
            }

            // Find the return date (next occurrence of return day)
            $returnDate = $this->findNextDayOfWeek($date, $returnDayOfWeek, $departureDayOfWeek);
            
            // CRITICAL: Validate that return is after departure
            if ($returnDate->lte($date)) {
                Log::warning('Invalid date combination - return before departure', [
                    'departure' => $date->format('Y-m-d'),
                    'return' => $returnDate->format('Y-m-d'),
                ]);
                continue;
            }
            
            // Validate trip duration is within 1 week
            $tripDays = $returnDate->diffInDays($date);
            if ($tripDays > 7) {
                Log::warning('Trip duration too long', [
                    'departure' => $date->format('Y-m-d'),
                    'return' => $returnDate->format('Y-m-d'),
                    'days' => $tripDays,
                ]);
                continue;
            }

            // Search via API
            $apiResults = $this->aviasalesService->searchFlights(
                $origin,
                $destination,
                $date->format('Y-m-d'),
                $returnDate->format('Y-m-d')
            );

            if ($apiResults && isset($apiResults['data'])) {
                foreach ($apiResults['data'] as $ticket) {
                    $ticketDeparture = $ticket['departure_at'] ?? $date->format('Y-m-d');
                    $ticketReturn = $ticket['return_at'] ?? $returnDate->format('Y-m-d');
                    
                    // Additional validation: ensure API returned dates are valid
                    $depDate = Carbon::parse($ticketDeparture);
                    $retDate = Carbon::parse($ticketReturn);
                    
                    // CRITICAL: Validate departure day matches expected day of week
                    if ($depDate->dayOfWeek !== $departureDayOfWeek) {
                        Log::warning('API returned wrong departure day', [
                            'expected_day' => $departureDayOfWeek,
                            'got_day' => $depDate->dayOfWeek,
                            'date' => $depDate->format('Y-m-d l'),
                        ]);
                        continue;
                    }
                    
                    // Skip if return is not after departure
                    if ($retDate->lte($depDate)) {
                        continue;
                    }
                    
                    // Skip if trip is longer than 1 week
                    if ($retDate->diffInDays($depDate) > 7) {
                        continue;
                    }
                    
                    // Filter by number of stops (max 1 stop)
                    $stops = $ticket['transfers'] ?? $ticket['stops'] ?? null;
                    if ($stops !== null && $stops > 1) {
                        continue; // Skip flights with more than 1 stop
                    }
                    
                    // Generate proper Aviasales search link
                    $link = $this->generateAviasalesLink(
                        $origin,
                        $destination,
                        $depDate->format('Y-m-d'),
                        $retDate->format('Y-m-d')
                    );
                    
                    $results[] = [
                        'origin' => $origin,
                        'destination' => $destination,
                        'departure_date' => $ticketDeparture,
                        'return_date' => $ticketReturn,
                        'price' => $ticket['price'] ?? 0,
                        'currency' => $ticket['currency'] ?? 'USD',
                        'link' => $link,
                        'airline' => $ticket['airline'] ?? null,
                        'stops' => $stops ?? 0, // For sorting: direct flights first
                    ];
                }
            }

            // Limit API calls - only check 2 occurrences per destination (reduced from 4)
            if (count(array_filter($results, fn($r) => $r['destination'] === $destination)) >= 2) {
                break;
            }
        }
        
        // Sort results: direct flights first, then by price
        usort($results, function ($a, $b) {
            // First priority: number of stops (direct flights first)
            $stopCompare = ($a['stops'] ?? 0) <=> ($b['stops'] ?? 0);
            if ($stopCompare !== 0) {
                return $stopCompare;
            }
            // Second priority: price
            return ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX);
        });

        return $results;
    }
    
    /**
     * Generate Aviasales search link
     *
     * @param string $origin
     * @param string $destination
     * @param string $departureDate
     * @param string $returnDate
     * @return string
     */
    protected function generateAviasalesLink(string $origin, string $destination, string $departureDate, string $returnDate): string
    {
        $marker = env('AVIASALES_MARKER', '');
        
        // Формат Aviasales: Откуда + ДатаТуда(ДДММ) + Куда + ДатаОбратно(ДДММ) + Взрослые
        // Пример: BUS0603IST08031 (BUS 06.03 -> IST 08.03, 1 взрослый)
        $depDateFormatted = date('dm', strtotime($departureDate));
        $retDateFormatted = date('dm', strtotime($returnDate));
        
        $route = "{$origin}{$depDateFormatted}{$destination}{$retDateFormatted}1";
        $url = "https://www.aviasales.ru/search/{$route}";
        
        if ($marker) {
            $url .= "?marker={$marker}";
        }
        
        return $url;
    }

    /**
     * Find next occurrence of a specific day of week
     * Handles week boundaries for weekend trips
     *
     * @param Carbon $startDate Departure date
     * @param int $targetDayOfWeek Return day of week (0-6)
     * @param int $departureDayOfWeek Departure day of week (0-6)
     * @return Carbon
     */
    protected function findNextDayOfWeek(Carbon $startDate, int $targetDayOfWeek, int $departureDayOfWeek = null): Carbon
    {
        // For Friday -> Sunday (2 days later in same week)
        if ($departureDayOfWeek === 5 && $targetDayOfWeek === 0) {
            return $startDate->copy()->addDays(2); // Friday + 2 = Sunday
        }
        
        // For Friday -> Monday (3 days later, crosses into next week)
        if ($departureDayOfWeek === 5 && $targetDayOfWeek === 1) {
            return $startDate->copy()->addDays(3); // Friday + 3 = Monday
        }
        
        // For Saturday -> Monday (2 days later, crosses into next week)
        if ($departureDayOfWeek === 6 && $targetDayOfWeek === 1) {
            return $startDate->copy()->addDays(2); // Saturday + 2 = Monday
        }
        
        // Default: find next occurrence (shouldn't happen with our validation)
        $date = $startDate->copy()->addDay();
        $maxDays = 7; // Don't search more than a week
        $daysSearched = 0;
        
        while ($date->dayOfWeek !== $targetDayOfWeek && $daysSearched < $maxDays) {
            $date->addDay();
            $daysSearched++;
        }

        return $date;
    }

    /**
     * Get all destination city codes from countries config
     *
     * @param array $countries
     * @param int $maxCitiesPerCountry Limit cities per country (0 = all)
     * @return array
     */
    protected function getDestinationCities(array $countries, int $maxCitiesPerCountry = 0): array
    {
        $cities = [];

        foreach ($countries as $countryCode => $countryData) {
            if (isset($countryData['cities']) && is_array($countryData['cities'])) {
                $countryCities = $countryData['cities'];
                
                // Limit cities if specified
                if ($maxCitiesPerCountry > 0) {
                    $countryCities = array_slice($countryCities, 0, $maxCitiesPerCountry);
                }
                
                $cities = array_merge($cities, $countryCities);
            }
        }

        return array_unique($cities);
    }

    /**
     * Search and notify via Telegram with weekend combinations
     *
     * @param string $origin
     * @param array $departureDays Array of departure days
     * @param array $returnDays Array of return days
     * @param int $daysAhead
     * @param int $limit
     * @return bool
     */
    public function searchAndNotify(
        string $origin = 'BUS',
        int|array $departureDays = 5,
        int|array $returnDays = 0,
        int $daysAhead = 90,
        int $limit = 10
    ): bool {
        try {
            // Convert single values to arrays for unified processing
            $departureDays = is_array($departureDays) ? $departureDays : [$departureDays];
            $returnDays = is_array($returnDays) ? $returnDays : [$returnDays];
            
            $allResults = [];
            
            // Search for each valid combination
            foreach ($departureDays as $depDay) {
                foreach ($returnDays as $retDay) {
                    // Validate the combination
                    if (!$this->isValidCombination($depDay, $retDay)) {
                        continue;
                    }
                    
                    $results = $this->searchVisaFreeDestinations(
                        $origin,
                        $depDay,
                        $retDay,
                        $daysAhead,
                        $limit
                    );
                    
                    $allResults = array_merge($allResults, $results);
                }
            }
            
            // Sort by price
            usort($allResults, function ($a, $b) {
                return ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX);
            });

            // Cap to a few per destination so the list covers more cities
            $allResults = $this->limitPerDestination($allResults, 3, $limit);

            return $this->telegramService->sendFlightResults($allResults, $origin);
        } catch (\Exception $e) {
            Log::error('Search and notify error', ['message' => $e->getMessage()]);
            $this->telegramService->sendError('Ошибка при поиске билетов: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Keep at most $maxPerDestination flights per destination, filling the
     * remaining slots with the next cheapest flights to other destinations.
     * Expects $results already sorted by price (cheapest first).
     *
     * @param array $results
     * @param int $maxPerDestination
     * @param int $limit
     * @return array
     */
    protected function limitPerDestination(array $results, int $maxPerDestination, int $limit): array
    {
        $selected = [];
        $countPerDestination = [];

        foreach ($results as $flight) {
            $destination = $flight['destination'] ?? null;
            $count = $countPerDestination[$destination] ?? 0;

            if ($count >= $maxPerDestination) {
                continue;
            }

            $selected[] = $flight;
            $countPerDestination[$destination] = $count + 1;

            if (count($selected) >= $limit) {
                break;
            }
        }

        return $selected;
    }

    /**
     * Search and notify for every configured origin city (multi-city)
     *
     * @param int|array $departureDays
     * @param int|array $returnDays
     * @param int $daysAhead
     * @param int $limit
     * @return bool
     */
    public function searchAndNotifyAllOrigins(
        int|array $departureDays = 5,
        int|array $returnDays = 0,
        int $daysAhead = 90,
        int $limit = 10
    ): bool {
        $origins = array_keys(config('airports.origins'));
        $success = true;

        foreach ($origins as $origin) {
            $ok = $this->searchAndNotify($origin, $departureDays, $returnDays, $daysAhead, $limit);
            $success = $success && $ok;
        }

        return $success;
    }

    /**
     * Validate day combination based on weekend rules
     * 
     * Rules:
     * - Friday (5) -> Sunday (0): 2 days, same week ✓
     * - Friday (5) -> Monday (1): 3 days, next week ✓
     * - Saturday (6) -> Monday (1): 2 days, next week ✓
     * - Saturday (6) -> Sunday (0): 1 day only ✗
     *
     * @param int $departureDay
     * @param int $returnDay
     * @return bool
     */
    protected function isValidCombination(int $departureDay, int $returnDay): bool
    {
        // Friday -> Sunday (same week, 2 days)
        if ($departureDay === 5 && $returnDay === 0) {
            return true;
        }
        
        // Friday -> Monday (next week, 3 days)
        if ($departureDay === 5 && $returnDay === 1) {
            return true;
        }
        
        // Saturday -> Monday (next week, 2 days)
        if ($departureDay === 6 && $returnDay === 1) {
            return true;
        }
        
        // All other combinations are invalid
        return false;
    }
}
