<?php

namespace App\Http\Controllers;

use App\Services\FlightSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlightSearchController extends Controller
{
    protected FlightSearchService $searchService;

    public function __construct(FlightSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search for flights
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin' => 'sometimes|string|size:3',
            'departure_day' => 'sometimes|integer|min:0|max:6',
            'return_day' => 'sometimes|integer|min:0|max:6',
            'days_ahead' => 'sometimes|integer|min:1|max:365',
            'limit' => 'sometimes|integer|min:1|max:50',
            'notify' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $origin = $request->input('origin', env('DEFAULT_ORIGIN', 'BUS'));
        $departureDay = $request->input('departure_day', env('DEFAULT_DEPARTURE_DAY', 5));
        $returnDay = $request->input('return_day', env('DEFAULT_RETURN_DAY', 0));
        $daysAhead = $request->input('days_ahead', env('SEARCH_RANGE_DAYS', 90));
        $limit = $request->input('limit', 10);
        $notify = $request->input('notify', true);

        if ($notify) {
            $success = $this->searchService->searchAndNotify(
                $origin,
                $departureDay,
                $returnDay,
                $daysAhead,
                $limit
            );

            return response()->json([
                'success' => $success,
                'message' => $success 
                    ? 'Search completed and results sent to Telegram' 
                    : 'Search completed but failed to send to Telegram',
            ]);
        } else {
            $results = $this->searchService->searchVisaFreeDestinations(
                $origin,
                $departureDay,
                $returnDay,
                $daysAhead,
                $limit
            );

            return response()->json([
                'success' => true,
                'count' => count($results),
                'results' => $results,
            ]);
        }
    }

    /**
     * Get list of visa-free countries
     *
     * @return JsonResponse
     */
    public function countries(): JsonResponse
    {
        $countries = config('countries.visa_free');

        return response()->json([
            'success' => true,
            'count' => count($countries),
            'countries' => $countries,
        ]);
    }
}
