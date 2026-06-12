<?php

namespace App\Http\Controllers;

use App\Services\AviasalesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CalendarController extends Controller
{
    protected AviasalesService $aviasales;

    public function __construct(AviasalesService $aviasales)
    {
        $this->aviasales = $aviasales;
    }

    /**
     * Cheapest price per month for a route (origin -> destination).
     * Cached for 6 hours to keep API usage low.
     *
     * @param string $origin Origin IATA code
     * @param string $destination Destination IATA code
     * @return JsonResponse
     */
    public function calendar(string $origin, string $destination): JsonResponse
    {
        $origin = strtoupper($origin);
        $destination = strtoupper($destination);

        if (!preg_match('/^[A-Z]{3}$/', $origin) || !preg_match('/^[A-Z]{3}$/', $destination)) {
            return response()
                ->json(['success' => false, 'error' => 'Invalid IATA code'], 422)
                ->header('Access-Control-Allow-Origin', '*');
        }

        $months = Cache::remember(
            "calendar:{$origin}:{$destination}",
            21600,
            function () use ($origin, $destination) {
                $data = $this->aviasales->getMonthlyPrices($origin, $destination);

                $months = [];
                foreach ($data as $month => $info) {
                    if (!isset($info['price'])) {
                        continue;
                    }
                    $months[] = [
                        'month' => $month,
                        'price' => (int) round($info['price']),
                    ];
                }

                usort($months, fn ($a, $b) => strcmp($a['month'], $b['month']));

                return $months;
            }
        );

        return response()
            ->json([
                'success' => true,
                'origin' => $origin,
                'destination' => $destination,
                'months' => $months,
            ])
            ->header('Access-Control-Allow-Origin', '*');
    }
}
