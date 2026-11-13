<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SubscriberService
{
    /**
     * Path to the JSON file that maps a Telegram chat id to the origin
     * cities it is subscribed to.
     */
    protected function path(): string
    {
        return storage_path('app/subscribers.json');
    }

    /**
     * @param string $chatId
     * @param string $origin Origin IATA code
     * @return void
     */
    public function subscribe(string $chatId, string $origin): void
    {
        $all = $this->read();
        $origins = $all[$chatId] ?? [];

        if (!in_array($origin, $origins, true)) {
            $origins[] = $origin;
        }

        $all[$chatId] = $origins;
        $this->write($all);
    }

    /**
     * Remove every subscription of a chat.
     *
     * @param string $chatId
     * @return void
     */
    public function unsubscribe(string $chatId): void
    {
        $all = $this->read();
        unset($all[$chatId]);
        $this->write($all);
    }

    /**
     * Chat ids subscribed to an origin city.
     *
     * @param string $origin
     * @return string[]
     */
    public function forOrigin(string $origin): array
    {
        $chatIds = [];

        foreach ($this->read() as $chatId => $origins) {
            if (in_array($origin, $origins, true)) {
                $chatIds[] = (string) $chatId;
            }
        }

        return $chatIds;
    }

    /**
     * @return array
     */
    protected function read(): array
    {
        $path = $this->path();

        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array $all
     * @return void
     */
    protected function write(array $all): void
    {
        $path = $this->path();
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (file_put_contents($path, json_encode($all, JSON_PRETTY_PRINT), LOCK_EX) === false) {
            Log::error('Failed to write subscribers file', ['path' => $path]);
        }
    }
}
