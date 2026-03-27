<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAdvice extends Model
{
    use HasFactory;

    protected $table = 'ai_advices';

    protected $fillable = [
        'user_id',
        'advice',
        'generated_at',
        'symptoms_snapshot',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'symptoms_snapshot' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
