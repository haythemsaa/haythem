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
        Schema::create('gps_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 5, 2)->nullable(); // km/h
            $table->integer('heading')->nullable(); // 0-360 degrees
            $table->decimal('altitude', 8, 2)->nullable(); // meters
            $table->decimal('accuracy', 6, 2)->nullable(); // meters
            $table->string('address')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['vehicle_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gps_locations');
    }
};
