<?php

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can list doctors', function () {
    Doctor::create([
        'name' => 'Dr. List',
        'specialty' => 'Generaliste',
        'city' => 'Casablanca',
        'years_of_experience' => 7,
        'consultation_price' => 220,
        'available_days' => ['Monday'],
    ]);

    $response = $this->getJson('/api/doctors');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data');
});

test('user can search doctors by city', function () {
    Doctor::create([
        'name' => 'Dr. City',
        'specialty' => 'Cardiologue',
        'city' => 'Rabat',
        'years_of_experience' => 10,
        'consultation_price' => 400,
        'available_days' => ['Tuesday'],
    ]);

    Doctor::create([
        'name' => 'Dr. Other',
        'specialty' => 'Cardiologue',
        'city' => 'Marrakech',
        'years_of_experience' => 8,
        'consultation_price' => 380,
        'available_days' => ['Thursday'],
    ]);

    $response = $this->getJson('/api/doctors/search?city=Rabat');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.city', 'Rabat');
});
