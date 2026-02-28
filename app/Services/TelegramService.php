<?php

namespace App\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected Api $telegram;
    protected string $chatId;

    public function __construct()
    {
        try {
            $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            $this->chatId = env('TELEGRAM_CHAT_ID');
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
    public function sendFlightResults(array $flights): bool
    {
        if (empty($flights)) {
            return $this->sendMessage("🔍 Поиск завершен. Билеты не найдены.");
        }

        $message = $this->formatFlightResults($flights);
        return $this->sendMessage($message);
    }

    /**
     * Format flight results for Telegram message
     *
     * @param array $flights
     * @return string
     */
    protected function formatFlightResults(array $flights): string
    {
        $message = "✈️ *Найдены авиабилеты*\n\n";

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
}
