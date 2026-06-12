<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AviasalesService
{
    protected Client $client;
    protected string $apiToken;
    protected string $marker;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.travelpayouts.com/',
            'timeout' => 30,
        ]);
        
        $this->apiToken = env('AVIASALES_API_TOKEN');
        $this->marker = env('AVIASALES_MARKER', '');
    }

    /**
     * Search for cheapest flights
     *
     * @param string $origin Origin IATA code
     * @param string $destination Destination IATA code
     * @param string $departureDate Departure date (YYYY-MM-DD)
     * @param string|null $returnDate Return date (YYYY-MM-DD)
     * @return array|null
     */
    public function searchFlights(string $origin, string $destination, string $departureDate, ?string $returnDate = null): ?array
    {
        try {
            $params = [
                'origin' => $origin,
                'destination' => $destination,
                'departure_at' => $departureDate,
                'currency' => 'USD',
                'token' => $this->apiToken,
            ];

            if ($returnDate) {
                $params['return_at'] = $returnDate;
            }

            Log::info('Aviasales API Request', $params);

            $response = $this->client->get('aviasales/v3/prices_for_dates', [
                'query' => $params,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Aviasales API Response', ['data' => $data]);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Aviasales API Error', [
                'message' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination,
            ]);
            
            return null;
        }
    }

    /**
     * Get the cheapest price for each month (price calendar) for a route.
     * Uses the grouped_prices endpoint with group_by=month.
     *
     * @param string $origin Origin IATA code
     * @param string $destination Destination IATA code
     * @return array Map of "YYYY-MM" => ticket info (incl. price). Empty on error.
     */
    public function getMonthlyPrices(string $origin, string $destination): array
    {
        try {
            $response = $this->client->get('aviasales/v3/grouped_prices', [
                'query' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'currency' => 'USD',
                    'group_by' => 'month',
                    'token' => $this->apiToken,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['data'] ?? [];
        } catch (GuzzleException $e) {
            Log::error('Aviasales grouped_prices error', [
                'message' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination,
            ]);

            return [];
        }
    }

    /**
     * Get cheapest ticket from search results
     *
     * @param array $searchResults
     * @return array|null
     */
    public function getCheapestTicket(array $searchResults): ?array
    {
        if (!isset($searchResults['data']) || empty($searchResults['data'])) {
            return null;
        }

        $tickets = $searchResults['data'];
        
        // Sort by price
        usort($tickets, function ($a, $b) {
            return ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX);
        });

        return $tickets[0] ?? null;
    }

    /**
     * Filter tickets by day of week
     *
     * @param array $tickets
     * @param int $departureDayOfWeek 0 (Sunday) to 6 (Saturday)
     * @param int|null $returnDayOfWeek
     * @return array
     */
    public function filterByDayOfWeek(array $tickets, int $departureDayOfWeek, ?int $returnDayOfWeek = null): array
    {
        return array_filter($tickets, function ($ticket) use ($departureDayOfWeek, $returnDayOfWeek) {
            $departureDate = $ticket['departure_at'] ?? null;
            $returnDate = $ticket['return_at'] ?? null;

            if (!$departureDate) {
                return false;
            }

            $departureDow = (int) date('w', strtotime($departureDate));
            
            if ($departureDow !== $departureDayOfWeek) {
                return false;
            }

            if ($returnDayOfWeek !== null && $returnDate) {
                $returnDow = (int) date('w', strtotime($returnDate));
                return $returnDow === $returnDayOfWeek;
            }

            return true;
        });
    }

    /**
     * Format price with currency
     *
     * @param float $price
     * @param string $currency
     * @return string
     */
    public function formatPrice(float $price, string $currency = 'USD'): string
    {
        return number_format($price, 2) . ' ' . $currency;
    }
}
