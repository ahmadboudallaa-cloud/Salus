<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty',
        'city',
        'years_of_experience',
        'consultation_price',
        'available_days',
    ];

    protected $hidden = [
        'years_of_experience',
    ];

    protected $appends = [
        'yearsofexperience',
    ];

    protected $casts = [
        'available_days' => 'array',
        'consultation_price' => 'decimal:2',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getYearsofexperienceAttribute(): int
    {
        return (int) ($this->getAttribute('years_of_experience') ?? 0);
    }
}
