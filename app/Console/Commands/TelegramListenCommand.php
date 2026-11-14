<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Services\FlightSearchService;
use App\Services\SubscriberService;
use App\Services\FeedService;
use Illuminate\Support\Facades\Log;

class TelegramListenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for incoming Telegram messages using Long Polling';

    protected TelegramService $telegramService;
    protected FlightSearchService $flightService;
    protected SubscriberService $subscribers;
    protected FeedService $feedService;

    public function __construct(
        TelegramService $telegramService,
        FlightSearchService $flightService,
        SubscriberService $subscribers,
        FeedService $feedService
    ) {
        parent::__construct();
        $this->telegramService = $telegramService;
        $this->flightService = $flightService;
        $this->subscribers = $subscribers;
        $this->feedService = $feedService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting Telegram Long Polling... Press Ctrl+C to stop.");
        Log::info("Telegram Long Polling started.");

        $lastUpdateId = 0;

        while (true) {
            try {
                // timeout: 30 seconds holds connection open on Telegram's side until an update arrives
                $updates = $this->telegramService->getUpdates([
                    'offset' => $lastUpdateId + 1,
                    'limit' => 10,
                    'timeout' => 30, 
                ]);

                foreach ($updates as $update) {
                    $lastUpdateId = $update['update_id'];
                    $message = $update['message'] ?? null;

                    if ($message && isset($message['text'])) {
                        $this->processMessage($message);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Long Polling Error', ['error' => $e->getMessage()]);
                // Sleep for a bit before retrying to avoid spamming errors if the network drops
                sleep(5);
            }
        }
    }

    /**
     * Process received text message
     *
     * @param array $message
     */
    protected function processMessage(array $message)
    {
        $chatId = $message['chat']['id'];
        $text = trim($message['text']);
        $username = $message['from']['username'] ?? 'Unknown';

        // Public part: anyone can subscribe to a departure city
        if ($this->handleSubscription((string) $chatId, $text)) {
            return;
        }

        // Everything below is for the owner only (the chat ID from .env)
        // Note: The chat ID in .env is usually a string, comparing to int from API.
        $allowedChatId = env('TELEGRAM_CHAT_ID');
        if ((string)$chatId !== (string)$allowedChatId) {
            $this->sendCityMenu((string) $chatId, "Я присылаю дешёвые авиабилеты на выходные по безвизовым направлениям — раз в день.\n\nВыберите город вылета:");
            return;
        }

        $this->info("Received message from @{$username}: {$text}");

        // Handle commands
        if ($text === '/start' || $text === '/ping') {
            $this->telegramService->sendMessage("🏓 Понг! Бот на связи и готов искать билеты.\n\nКоманды:\n/search — Запустить поиск билетов прямо сейчас\n/countries — Показать список стран для поиска\n/subs — Число подписчиков по городам");
        } 
        elseif ($text === '/countries') {
            $countries = config('countries.visa_free.RU');
            $list = "📌 *Список стран для поиска:*\n\n";
            foreach ($countries as $code => $data) {
                $list .= "- {$data['name']} (" . implode(', ', $data['cities']) . ")\n";
            }
            $this->telegramService->sendMessage($list);
        }
        elseif ($text === '/subs') {
            $counts = $this->subscribers->counts();
            $list = "👥 Подписчиков: {$counts['total']}\n\n";
            foreach (config('airports.origins') as $code => $origin) {
                $list .= "{$origin['label']}: " . ($counts['origins'][$code] ?? 0) . "\n";
            }
            $this->telegramService->sendMessage($list);
        }
        elseif ($text === '/search') {
            $this->telegramService->sendMessage("⏳ *Начинаю поиск билетов...*\nЭто займет 1-2 минуты. Пожалуйста, подождите.");
            
            // Re-use logic from FlightSearchCommand (search all configured origins)
            $depDay = env('DEFAULT_DEPARTURE_DAY', 5);
            $retDay = env('DEFAULT_RETURN_DAY', 0);
            $daysAhead = env('SEARCH_RANGE_DAYS', 90);

            $success = $this->flightService->searchAndNotifyAllOrigins(
                $depDay,
                $retDay,
                $daysAhead,
                10
            );

            if (!$success) {
                $this->telegramService->sendMessage("❌ При поиске возникла ошибка или билеты не найдены.");
            }
        }
        else {
            $this->telegramService->sendMessage("🤔 Неизвестная команда. Напиши /ping или /search");
        }
    }

    /**
     * Subscription commands, available to everyone:
     * "/start <slug>" (deep link from the site), a city button, /cities, /stop.
     *
     * @param string $chatId
     * @param string $text
     * @return bool True if the message was a subscription command
     */
    protected function handleSubscription(string $chatId, string $text): bool
    {
        if ($text === '/stop') {
            $this->subscribers->unsubscribe($chatId);
            $this->telegramService->sendTo($chatId, "Готово, рассылку отключил. Вернуться — /cities");
            return true;
        }

        if ($text === '/cities') {
            $this->sendCityMenu($chatId, "Выберите город вылета:");
            return true;
        }

        $wanted = str_starts_with($text, '/start ') ? trim(substr($text, 7)) : $text;

        foreach (config('airports.origins') as $code => $origin) {
            if ($wanted !== $origin['slug'] && $wanted !== $origin['label']) {
                continue;
            }

            $this->subscribers->subscribe($chatId, $code);

            $passport = config("countries.passports.{$origin['passport']}");
            $this->sendCityMenu(
                $chatId,
                "✅ Подписал на билеты из {$origin['name_ru']}.\n\n"
                . "Раз в день буду присылать самые дешёвые билеты на выходные — направления без визы для паспорта {$passport}.\n\n"
                . "Можно добавить ещё город кнопкой ниже. Отписаться — /stop"
            );

            // Show what is available right now instead of making them wait a day
            $flights = array_slice($this->feedService->all()[$code]['results'] ?? [], 0, 10);
            if (!empty($flights)) {
                $this->telegramService->sendFlightsTo($chatId, $flights, $code);
            }

            return true;
        }

        return false;
    }

    /**
     * Send a message with one button per departure city.
     *
     * @param string $chatId
     * @param string $text
     */
    protected function sendCityMenu(string $chatId, string $text): void
    {
        $rows = array_chunk(array_column(config('airports.origins'), 'label'), 2);
        $this->telegramService->sendTo($chatId, $text, $rows);
    }
}
