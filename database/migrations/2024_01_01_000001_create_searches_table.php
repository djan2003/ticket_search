<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id();
            $table->string('origin', 3); // IATA code
            $table->string('destination', 3); // IATA code
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('search_params')->nullable(); // Store additional search parameters
            $table->json('api_response')->nullable(); // Store full API response for debugging
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['origin', 'destination', 'departure_date']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('searches');
    }
};
