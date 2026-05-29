<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FeedService
{
    /**
     * Path to the JSON file that stores the latest search results per origin.
     */
    protected function path(): string
    {
        return storage_path('app/feed.json');
    }

    /**
     * Store the latest results for a single origin city.
     * Other origins already in the file are preserved.
     *
     * @param string $origin
     * @param array $results
     * @return void
     */
    public function store(string $origin, array $results): void
    {
        $feed = $this->read();

        $feed[$origin] = [
            'generated_at' => date('c'),
            'results' => array_values($results),
        ];

        $this->write($feed);
    }

    /**
     * Get the whole feed (all origins).
     *
     * @return array
     */
    public function all(): array
    {
        return $this->read();
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
     * @param array $feed
     * @return void
     */
    protected function write(array $feed): void
    {
        $path = $this->path();
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $json = json_encode($feed, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if (file_put_contents($path, $json) === false) {
            Log::error('Failed to write feed file', ['path' => $path]);
        }
    }
}
