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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_number')->unique();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('actual_return_date')->nullable();
            $table->decimal('daily_rate', 10, 2);
            $table->integer('total_days');
            $table->decimal('total_cost', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->string('status')->default('reserved');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
