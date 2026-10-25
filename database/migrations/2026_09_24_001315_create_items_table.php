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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // foreignKey
            $table->foreignId('purchase_id')->constrained();
            $table->foreignId('inventory_id')->constrained();
            $table->string('name');
            $table->integer('effect_duration')->default(0);
            $table->string('effect_type');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
