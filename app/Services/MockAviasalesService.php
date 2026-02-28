<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MockAviasalesService extends AviasalesService
{
    /**
     * Mock search flights for testing without API token
     *
     * @param string $origin Origin IATA code
     * @param string $destination Destination IATA code
     * @param string $departureDate Departure date (YYYY-MM-DD)
     * @param string|null $returnDate Return date (YYYY-MM-DD)
     * @return array|null
     */
    public function searchFlights(string $origin, string $destination, string $departureDate, ?string $returnDate = null): ?array
    {
        Log::info('Using MOCK Aviasales API', [
            'origin' => $origin,
            'destination' => $destination,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);

        // Generate mock data that looks like real Aviasales response
        $mockData = [
            'success' => true,
            'data' => [
                [
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_at' => $departureDate,
                    'return_at' => $returnDate,
                    'price' => rand(5000, 50000),
                    'currency' => 'RUB',
                    'airline' => 'MOCK Airlines',
                    'link' => 'https://www.aviasales.ru',
                ],
                [
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_at' => $departureDate,
                    'return_at' => $returnDate,
                    'price' => rand(5000, 50000),
                    'currency' => 'RUB',
                    'airline' => 'Test Airways',
                    'link' => 'https://www.aviasales.ru',
                ],
            ],
        ];

        Log::info('MOCK API Response', ['data' => $mockData]);

        return $mockData;
    }
}
