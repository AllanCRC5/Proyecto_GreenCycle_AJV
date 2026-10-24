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
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // seeds
            $table->string('seed_type', 20)->default('BASIC');
            $table->integer('level')->default(0);
            $table->integer('health')->default(100);
            $table->integer('progress')->default(0);
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamp('last_care_at')->nullable();
            $table->timestamp('next_care_at')->nullable();
            $table->timestamp('last_decay_at')->nullable();
            $table->timestamp('harvested_at')->nullable();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'last_decay_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }

};

Schema::create('trees', function (Blueprint $table) {
    // ... tus columnas ...

    $table->index(['user_id', 'status']);        // para listar los árboles de un usuario por estado
    $table->index(['status', 'last_decay_at']);  // para que el Scheduler encuentre los ACTIVE pendientes de deterioro
});

