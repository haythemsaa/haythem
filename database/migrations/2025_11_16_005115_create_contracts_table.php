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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('contract_type');
            $table->string('supplier_name');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_cost', 10, 2);
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->boolean('auto_renewal')->default(false);
            $table->string('contract_document')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
