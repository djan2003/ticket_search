<?php

namespace App\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected Api $telegram;
    protected string $chatId;
    protected ?string $channelId;
    protected SubscriberService $subscribers;

    public function __construct(SubscriberService $subscribers)
    {
        $this->subscribers = $subscribers;

        try {
            $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            $this->chatId = env('TELEGRAM_CHAT_ID');
            $this->channelId = env('TELEGRAM_CHANNEL_ID');
        } catch (TelegramSDKException $e) {
            Log::error('Telegram initialization error', ['message' => $e->getMessage()]);
        }
    }

    /**
     * Send flight results to Telegram
     *
     * @param array $flights
     * @return bool
     */
    public function sendFlightResults(array $flights, ?string $origin = null): bool
    {
        if (empty($flights)) {
            return $this->sendMessage("🔍 Поиск завершен. Билеты не найдены.");
        }

        $message = $this->formatFlightResults($flights, $origin);

        if (!$origin || config("airports.origins.{$origin}.channel")) {
            $this->sendToChannel($message);
        }

        if ($origin) {
            foreach ($this->subscribers->forOrigin($origin) as $chatId) {
                $this->sendTo($chatId, $message);
            }
        }

        return $this->sendMessage($message);
    }

    /**
     * Send the given flights from one origin to a single chat.
     *
     * @param string $chatId
     * @param array $flights
     * @param string $origin
     * @return bool
     */
    public function sendFlightsTo(string $chatId, array $flights, string $origin): bool
    {
        return $this->sendTo($chatId, $this->formatFlightResults($flights, $origin));
    }

    /**
     * Send a message to an arbitrary chat (bot subscribers).
     * A chat that blocked the bot is unsubscribed.
     *
     * @param string $chatId
     * @param string $message
     * @param array|null $keyboard Rows of button labels for a reply keyboard
     * @return bool
     */
    public function sendTo(string $chatId, string $message, ?array $keyboard = null): bool
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ];

        if ($keyboard !== null) {
            $params['reply_markup'] = json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
            ]);
        }

        try {
            $this->telegram->sendMessage($params);
            return true;
        } catch (TelegramSDKException $e) {
            Log::error('Telegram send error', [
                'message' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);

            if ($e->getCode() === 403) {
                $this->subscribers->unsubscribe($chatId);
            }

            return false;
        }
    }

    public function sendToChannel(string $message): bool
    {
        if (!$this->channelId) {
            return false;
        }

        try {
            $this->telegram->sendMessage([
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
            ]);

            Log::info('Telegram channel message sent', ['channel_id' => $this->channelId]);
            return true;
        } catch (TelegramSDKException $e) {
            Log::error('Telegram channel send error', [
                'message' => $e->getMessage(),
                'channel_id' => $this->channelId,
            ]);
            return false;
        }
    }

    /**
     * Format flight results for Telegram message
     *
     * @param array $flights
     * @return string
     */
    protected function formatFlightResults(array $flights, ?string $origin = null): string
    {
        $originName = $origin ? config("airports.origins.{$origin}.name_ru") : null;
        $message = $originName
            ? "✈️ *Дешёвые билеты из {$originName}*\n\n"
            : "✈️ *Найдены авиабилеты*\n\n";

        $passport = $origin ? config("airports.origins.{$origin}.passport") : null;
        if ($passport) {
            $message .= "🛂 Без визы для паспорта " . config("countries.passports.{$passport}") . "\n\n";
        }

        foreach ($flights as $index => $flight) {
            $number = $index + 1;
            $origin = $flight['origin'] ?? 'N/A';
            $destination = $flight['destination'] ?? 'N/A';
            $price = $flight['price'] ?? 0;
            $currency = $flight['currency'] ?? 'USD';
            $departureDate = $flight['departure_date'] ?? 'N/A';
            $returnDate = $flight['return_date'] ?? null;
            $stops = $flight['stops'] ?? null;
            
            $message .= "🎫 *Вариант {$number}*\n";
            $message .= "📍 Маршрут: {$origin} → {$destination}\n";
            
            // Show if direct or with stops
            if ($stops === 0) {
                $message .= "🛫 Прямой рейс\n";
            } elseif ($stops === 1) {
                $message .= "🛫 1 пересадка\n";
            }
            
            $message .= "📅 Вылет: " . $this->formatDate($departureDate) . "\n";
            
            if ($returnDate) {
                $message .= "📅 Возврат: " . $this->formatDate($returnDate) . "\n";
            }
            
            $message .= "💰 Цена: *{$price} {$currency}*\n";
            
            if (isset($flight['link'])) {
                $message .= "🔗 [Купить билет]({$flight['link']})\n";
            }
            
            $message .= "\n";
        }

        return $message;
    }

    /**
     * Send a simple message
     *
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $message): bool
    {
        try {
            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            Log::info('Telegram message sent', ['chat_id' => $this->chatId]);
            return true;
        } catch (TelegramSDKException $e) {
            Log::error('Telegram send error', [
                'message' => $e->getMessage(),
                'chat_id' => $this->chatId,
            ]);
            return false;
        }
    }

    /**
     * Format date for display
     *
     * @param string $date
     * @return string
     */
    protected function formatDate(string $date): string
    {
        try {
            $timestamp = strtotime($date);
            $dayOfWeek = $this->getDayOfWeekRussian($timestamp);
            return date('d.m.Y', $timestamp) . " ({$dayOfWeek})";
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Get Russian day of week name
     *
     * @param int $timestamp
     * @return string
     */
    protected function getDayOfWeekRussian(int $timestamp): string
    {
        $days = [
            0 => 'Вс',
            1 => 'Пн',
            2 => 'Вт',
            3 => 'Ср',
            4 => 'Чт',
            5 => 'Пт',
            6 => 'Сб',
        ];

        return $days[(int) date('w', $timestamp)] ?? '';
    }

    /**
     * Send error notification
     *
     * @param string $error
     * @return bool
     */
    public function sendError(string $error): bool
    {
        $message = "⚠️ *Ошибка*\n\n{$error}";
        return $this->sendMessage($message);
    }
    /**
     * Get updates from Telegram (Long Polling)
     *
     * @param array $params
     * @return array
     */
    public function getUpdates(array $params = []): array
    {
        try {
            return $this->telegram->getUpdates($params);
        } catch (TelegramSDKException $e) {
            Log::error('Telegram getUpdates error', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }
}
