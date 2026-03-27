<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can create an appointment in the future', function () {
    $user = User::factory()->create();
    $doctor = Doctor::create([
        'name' => 'Dr. Test',
        'specialty' => 'Generaliste',
        'city' => 'Casablanca',
        'years_of_experience' => 5,
        'consultation_price' => 200,
        'available_days' => ['Monday', 'Friday'],
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/appointments', [
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->addDay()->toDateTimeString(),
        'notes' => 'First visit',
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.doctor_id', $doctor->id);
});

test('appointment date must be in the future', function () {
    $user = User::factory()->create();
    $doctor = Doctor::create([
        'name' => 'Dr. Old',
        'specialty' => 'Cardiologue',
        'city' => 'Rabat',
        'years_of_experience' => 10,
        'consultation_price' => 350,
        'available_days' => ['Tuesday'],
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/appointments', [
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->subDay()->toDateTimeString(),
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors(['appointment_date']);
});

test('user sees only their appointments', function () {
    $doctor = Doctor::create([
        'name' => 'Dr. Shared',
        'specialty' => 'Dermatologue',
        'city' => 'Marrakech',
        'years_of_experience' => 6,
        'consultation_price' => 300,
        'available_days' => ['Monday'],
    ]);

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    Appointment::create([
        'user_id' => $userA->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->addDays(2),
        'status' => Appointment::STATUS_PENDING,
    ]);

    Appointment::create([
        'user_id' => $userB->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->addDays(3),
        'status' => Appointment::STATUS_PENDING,
    ]);

    Sanctum::actingAs($userA);

    $response = $this->getJson('/api/appointments');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.user_id', $userA->id);
});
