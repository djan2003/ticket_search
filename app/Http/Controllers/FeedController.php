<?php

namespace App\Http\Controllers;

use App\Services\FeedService;
use Illuminate\Http\JsonResponse;

class FeedController extends Controller
{
    protected FeedService $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Return the cached daily search results (per origin) as JSON.
     *
     * @return JsonResponse
     */
    public function feed(): JsonResponse
    {
        return response()
            ->json([
                'success' => true,
                'feed' => $this->feedService->all(),
            ])
            ->header('Access-Control-Allow-Origin', '*');
    }
}
