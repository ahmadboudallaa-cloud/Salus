<?php

use App\Models\AiAdvice;
use App\Models\Symptom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can request ai health advice', function () {
    config([
        'services.ai.provider' => 'openai',
        'services.openai.api_key' => 'test-key',
        'services.openai.model' => 'gpt-4o-mini',
        'services.openai.base_url' => 'https://api.openai.com/v1',
    ]);

    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Hydrate well and try regular sleep hours.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Symptom::create([
        'user_id' => $user->id,
        'name' => 'Headache',
        'severity' => 'mild',
        'date_recorded' => now()->toDateString(),
    ]);

    $response = $this->postJson('/api/ai/health-advice');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.advice', 'Hydrate well and try regular sleep hours.');

    $this->assertDatabaseCount('ai_advices', 1);
});

test('user can view ai advice history', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    AiAdvice::create([
        'user_id' => $user->id,
        'advice' => 'Rest and drink water.',
        'generated_at' => now(),
        'symptoms_snapshot' => [
            ['name' => 'Fatigue', 'severity' => 'moderate', 'date_recorded' => now()->toDateString()],
        ],
    ]);

    $response = $this->getJson('/api/ai/health-advice/history');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.advice', 'Rest and drink water.');
});
