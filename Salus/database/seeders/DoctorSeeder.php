<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::truncate();

        Doctor::insert([
            [
                'name' => 'Dr. Sara Benali',
                'specialty' => 'Generaliste',
                'city' => 'Casablanca',
                'years_of_experience' => 8,
                'consultation_price' => 250.00,
                'available_days' => json_encode(['Monday', 'Wednesday', 'Friday']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Ahmed El Idrissi',
                'specialty' => 'Cardiologue',
                'city' => 'Rabat',
                'years_of_experience' => 12,
                'consultation_price' => 400.00,
                'available_days' => json_encode(['Tuesday', 'Thursday']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Lina Haddad',
                'specialty' => 'Dermatologue',
                'city' => 'Marrakech',
                'years_of_experience' => 6,
                'consultation_price' => 300.00,
                'available_days' => json_encode(['Monday', 'Tuesday', 'Saturday']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
