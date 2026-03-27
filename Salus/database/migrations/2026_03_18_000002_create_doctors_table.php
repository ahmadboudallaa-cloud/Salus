<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('specialty');
            $table->string('city');
            $table->unsignedInteger('years_of_experience');
            $table->decimal('consultation_price', 8, 2);
            $table->json('available_days');
            $table->timestamps();

            $table->index(['specialty', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
