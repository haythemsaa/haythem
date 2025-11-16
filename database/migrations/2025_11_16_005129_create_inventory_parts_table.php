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
        Schema::create('inventory_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_number')->unique();
            $table->string('part_name');
            $table->string('category')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity_in_stock')->default(0);
            $table->integer('minimum_stock')->default(5);
            $table->decimal('unit_price', 10, 2);
            $table->string('location')->nullable();
            $table->string('unit')->default('pièce');
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
        Schema::dropIfExists('inventory_parts');
    }
};
