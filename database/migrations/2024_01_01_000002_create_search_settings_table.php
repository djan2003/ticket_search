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
        Schema::create('search_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Search'); // Search configuration name
            $table->string('departure_point', 3)->default('BUS'); // IATA code
            $table->tinyInteger('departure_day')->default(5); // 0 = Sunday, 6 = Saturday
            $table->tinyInteger('return_day')->default(0); // 0 = Sunday, 6 = Saturday
            $table->integer('date_range_days')->default(90); // How many days ahead to search
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_settings');
    }
};
