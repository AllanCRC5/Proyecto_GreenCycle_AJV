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
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            //foreign id
            $table->foreignId('user_id');
            $table->foreignId('item_id');
            $table->foreignId('tree_id');
            //fin foreign id
            $table->varchar("effect_type");
            $table->timestamp("expires_at");
            $table->timestamp("started_at");
            $table->varchar("status");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
