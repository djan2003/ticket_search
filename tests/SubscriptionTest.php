<?php

namespace Tests;

use App\Console\Commands\TelegramListenCommand;
use App\Services\FeedService;
use App\Services\FlightSearchService;
use App\Services\SubscriberService;
use App\Services\TelegramService;
use Mockery;
use ReflectionMethod;
use ReflectionProperty;

class SubscriptionTest extends TestCase
{
    protected function tearDown(): void
    {
        @unlink(storage_path('app/subscribers.json'));
        parent::tearDown();
    }

    public function test_subscribers_are_stored_per_origin_and_can_unsubscribe()
    {
        $subscribers = new SubscriberService();
        $subscribers->subscribe('1', 'MOW');
        $subscribers->subscribe('1', 'MOW');
        $subscribers->subscribe('1', 'LED');
        $subscribers->subscribe('2', 'MOW');

        $this->assertSame(['1', '2'], $subscribers->forOrigin('MOW'));
        $this->assertSame(['1'], $subscribers->forOrigin('LED'));

        $subscribers->unsubscribe('1');

        $this->assertSame(['2'], $subscribers->forOrigin('MOW'));
        $this->assertSame([], $subscribers->forOrigin('LED'));
    }

    public function test_deep_link_subscribes_a_stranger_and_sends_current_flights()
    {
        $flights = [['origin' => 'MOW', 'destination' => 'MSQ', 'price' => 77]];

        $telegram = Mockery::mock(TelegramService::class);
        $telegram->shouldReceive('sendTo')->once()
            ->with('555', Mockery::pattern('/Подписал на билеты из Москвы.*паспорта России/us'), Mockery::type('array'));
        $telegram->shouldReceive('sendFlightsTo')->once()->with('555', $flights, 'MOW');

        $subscribers = Mockery::mock(SubscriberService::class);
        $subscribers->shouldReceive('subscribe')->once()->with('555', 'MOW');

        $feed = Mockery::mock(FeedService::class);
        $feed->shouldReceive('all')->andReturn(['MOW' => ['results' => $flights]]);

        $this->process($telegram, $subscribers, $feed, 555, '/start moscow');
    }

    public function test_stranger_cannot_run_owner_commands_and_gets_the_city_menu()
    {
        $telegram = Mockery::mock(TelegramService::class);
        $telegram->shouldReceive('sendTo')->once()
            ->with('555', Mockery::type('string'), Mockery::on(function ($rows) {
                return in_array('Москва', array_merge(...$rows), true);
            }));

        $flightService = Mockery::mock(FlightSearchService::class);
        $flightService->shouldNotReceive('searchAndNotifyAllOrigins');

        $this->process(
            $telegram,
            Mockery::mock(SubscriberService::class),
            Mockery::mock(FeedService::class),
            555,
            '/search',
            $flightService
        );
    }

    public function test_only_channel_origins_are_posted_to_the_channel_and_subscribers_get_their_city()
    {
        $flights = [['origin' => 'MOW', 'destination' => 'MSQ', 'price' => 77]];

        $subscribers = Mockery::mock(SubscriberService::class);
        $subscribers->shouldReceive('forOrigin')->with('MOW')->andReturn(['7']);
        $subscribers->shouldReceive('forOrigin')->with('BUS')->andReturn([]);

        $telegram = Mockery::mock(TelegramService::class)->makePartial();
        (new ReflectionProperty(TelegramService::class, 'subscribers'))->setValue($telegram, $subscribers);

        // Moscow: bot subscribers only, not the public channel
        $telegram->shouldReceive('sendToChannel')->never()->byDefault();
        $telegram->shouldReceive('sendTo')->once()->with('7', Mockery::pattern('/из Москвы/u'))->andReturn(true);
        $telegram->shouldReceive('sendMessage')->twice()->andReturn(true);
        $this->assertTrue($telegram->sendFlightResults($flights, 'MOW'));

        // Batumi: still goes to the channel
        $telegram->shouldReceive('sendToChannel')->once()->andReturn(true);
        $this->assertTrue($telegram->sendFlightResults($flights, 'BUS'));
    }

    protected function process($telegram, $subscribers, $feed, int $chatId, string $text, $flightService = null): void
    {
        $command = new TelegramListenCommand(
            $telegram,
            $flightService ?? Mockery::mock(FlightSearchService::class),
            $subscribers,
            $feed
        );

        $method = new ReflectionMethod($command, 'processMessage');
        $method->invoke($command, [
            'chat' => ['id' => $chatId],
            'text' => $text,
            'from' => ['username' => 'stranger'],
        ]);

        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
