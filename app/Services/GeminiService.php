<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;


class GeminiService
{
    protected array $providers;

    public function __construct()
    {
        $endpoint1 = config('services.gemini.endpoint1');
        $endpoint2 = config('services.gemini.endpoint2');
        $key1=config('services.gemini.key1');
        $key2=config('services.gemini.key2');
        $this->providers = [
            ['endpoint' => $endpoint1, 'key' => $key1, 'id' => 'm1k1', 'isExh' => false],
            ['endpoint' => $endpoint2, 'key' => $key1, 'id' => 'm2k1', 'isExh' => false],
            ['endpoint' => $endpoint1, 'key' => $key2, 'id' => 'm1k2', 'isExh' => false],
            ['endpoint' => $endpoint2, 'key' => $key2, 'id' => 'm2k2', 'isExh' => false],
        ];

    }

    public function generate(string $prompt): string
    {
        foreach ($this->providers as &$provider) {
            if ($provider['isExh'] === false) {
                $result = $this->sendPrompt(
                    $provider['endpoint'],
                    $provider['key'],
                    $prompt,
                    $provider
                );
                if ($result !== null && $result !== '') {
                    return $result;
                }

            }
            unset($provider);
        }
        return 'ff';
    }

    private function sendPrompt(string $endpoint, string $key, string $prompt, array &$provider): ?string
    {
        try {
            $response = Http::timeout(20)->retry(3, 200, function ($exception) {
                    return $exception instanceof ConnectionException;
                })->post(
                $endpoint . '?key=' . $key,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );
            if ($response->status() === 429) {
                $this->markExhausted($provider);

                Log::warning('Gemini quota exhausted', [
                    'provider' => $provider['id'],
                    'status' => 429,
                ]);

                return null;
            }

            if ($response->failed()) {
                Log::error('Gemini API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'provider' => $provider['id']
                ]);
                return null;
            }
            return $response->json('candidates.0.content.parts.0.text') ?? '';
        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'provider' => $provider['id'],
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function markExhausted(array &$provider) {
        $provider['isExh'] = true;
    }
}
