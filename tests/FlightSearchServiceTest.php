<?php

namespace Tests;

use App\Services\AviasalesService;
use App\Services\FeedService;
use App\Services\FlightSearchService;
use App\Services\TelegramService;
use Mockery;

class FlightSearchServiceTest extends TestCase
{
    public function test_search_covers_every_configured_city_not_only_the_first_per_country()
    {
        $searched = [];

        $aviasales = Mockery::mock(AviasalesService::class);
        $aviasales->shouldReceive('searchFlights')
            ->andReturnUsing(function ($origin, $destination) use (&$searched) {
                $searched[$destination] = true;
                return ['data' => []];
            });

        $service = new FlightSearchService(
            $aviasales,
            Mockery::mock(TelegramService::class),
            Mockery::mock(FeedService::class)
        );

        $service->searchVisaFreeDestinations('BUS', 5, 0);

        $expected = [];
        foreach (config('countries.visa_free') as $country) {
            $expected = array_merge($expected, $country['cities']);
        }
        sort($expected);

        $actual = array_keys($searched);
        sort($actual);

        $this->assertSame($expected, $actual);
    }
}
