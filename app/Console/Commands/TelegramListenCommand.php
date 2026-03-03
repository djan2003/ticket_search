<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Services\FlightSearchService;
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

    public function __construct(TelegramService $telegramService, FlightSearchService $flightService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
        $this->flightService = $flightService;
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

        // Security check: Only answer to the owner (the chat ID from .env)
        // Note: The chat ID in .env is usually a string, comparing to int from API.
        $allowedChatId = env('TELEGRAM_CHAT_ID');
        if ((string)$chatId !== (string)$allowedChatId) {
            Log::warning("Unauthorized bot access attempt from @{$username} (Chat ID: {$chatId})");
            return;
        }

        $this->info("Received message from @{$username}: {$text}");

        // Handle commands
        if ($text === '/start' || $text === '/ping') {
            $this->telegramService->sendMessage("🏓 Понг! Бот на связи и готов искать билеты.\n\nКоманды:\n/search — Запустить поиск билетов прямо сейчас\n/countries — Показать список стран для поиска");
        } 
        elseif ($text === '/countries') {
            $countries = config('countries.visa_free');
            $list = "📌 *Список стран для поиска:*\n\n";
            foreach ($countries as $code => $data) {
                $list .= "- {$data['name']} (" . implode(', ', $data['cities']) . ")\n";
            }
            $this->telegramService->sendMessage($list);
        }
        elseif ($text === '/search') {
            $this->telegramService->sendMessage("⏳ *Начинаю поиск билетов...*\nЭто займет 1-2 минуты. Пожалуйста, подождите.");
            
            // Re-use logic from FlightSearchCommand
            $origin = env('DEFAULT_ORIGIN', 'BUS');
            $depDay = env('DEFAULT_DEPARTURE_DAY', 5);
            $retDay = env('DEFAULT_RETURN_DAY', 0);
            $daysAhead = env('SEARCH_RANGE_DAYS', 90);
            
            $success = $this->flightService->searchAndNotify(
                $origin,
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
}
