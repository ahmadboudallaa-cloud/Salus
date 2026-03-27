<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can create a symptom', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Headache',
        'severity' => 'moderate',
        'description' => 'Pain in the temples',
        'date_recorded' => now()->toDateString(),
        'notes' => 'After long work',
    ];

    $response = $this->postJson('/api/symptoms', $payload);

    $response
        ->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Headache');
});

test('symptom severity must be valid', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/symptoms', [
        'name' => 'Cough',
        'severity' => 'high',
        'date_recorded' => now()->toDateString(),
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors(['severity']);
});
