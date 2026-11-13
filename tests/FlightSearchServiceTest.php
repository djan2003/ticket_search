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
        foreach (config('countries.visa_free.RU') as $country) {
            $expected = array_merge($expected, $country['cities']);
        }
        sort($expected);

        $actual = array_keys($searched);
        sort($actual);

        $this->assertSame($expected, $actual);
    }

    public function test_origin_searches_the_visa_free_list_of_its_passport_and_tags_the_feed()
    {
        $searched = [];

        $aviasales = Mockery::mock(AviasalesService::class);
        $aviasales->shouldReceive('searchFlights')
            ->andReturnUsing(function ($origin, $destination) use (&$searched) {
                $searched[$destination] = true;
                return ['data' => []];
            });

        $telegram = Mockery::mock(TelegramService::class);
        $telegram->shouldReceive('sendFlightResults')->once()->with([], 'ALA')->andReturn(true);

        $feed = Mockery::mock(FeedService::class);
        $feed->shouldReceive('store')->once()->with('ALA', [], 'KZ');

        $service = new FlightSearchService($aviasales, $telegram, $feed);

        $this->assertTrue($service->searchAndNotify('ALA', [5], [0]));

        $expected = [];
        foreach (config('countries.visa_free.KZ') as $country) {
            $expected = array_merge($expected, $country['cities']);
        }
        // The origin itself is never searched as a destination
        $expected = array_values(array_diff($expected, ['ALA']));
        sort($expected);

        $actual = array_keys($searched);
        sort($actual);

        $this->assertNotEmpty($expected);
        $this->assertSame($expected, $actual);
    }
}
