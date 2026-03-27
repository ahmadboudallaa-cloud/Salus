<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiHealthService
{
    public function generateAdvice(Collection $symptoms): string
    {
        $provider = config('services.ai.provider', 'openai');
        $prompt = $this->buildPrompt($symptoms);

        return match ($provider) {
            'openai' => $this->callOpenAi($prompt),
            'gemini' => $this->callGemini($prompt),
            default => throw new RuntimeException('AI provider non supporte.'),
        };
    }

    private function buildPrompt(Collection $symptoms): string
    {
        $lines = $symptoms->map(function ($symptom): string {
            return sprintf(
                '- %s (%s, %s)',
                $symptom->name,
                $symptom->severity,
                $symptom->date_recorded?->format('Y-m-d')
            );
        })->implode("\n");

        return "User symptoms:\n" . $lines . "\nProvide general wellness advice, not medical diagnosis.";
    }

    private function callOpenAi(string $prompt): string
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model');
        $baseUrl = rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/');

        if (! $apiKey || ! $model) {
            throw new RuntimeException('Configuration OpenAI manquante.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(20)
            ->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful health assistant. Provide general wellness advice, not medical diagnosis.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.4,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Echec de la requete OpenAI.');
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! $content) {
            throw new RuntimeException('Reponse OpenAI invalide.');
        }

        return trim($content);
    }

    private function callGemini(string $prompt): string
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model');
        $baseUrl = rtrim(config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

        if (! $apiKey || ! $model) {
            throw new RuntimeException('Configuration Gemini manquante.');
        }

        $response = Http::timeout(20)
            ->post($baseUrl . '/models/' . $model . ':generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Echec de la requete Gemini.');
        }

        $content = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $content) {
            throw new RuntimeException('Reponse Gemini invalide.');
        }

        return trim($content);
    }
}
