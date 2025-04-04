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
        Schema::create('electricity_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_room_id')->constrained('apartment_rooms')->onDelete('cascade');
            $table->integer('usage_number');
            $table->dateTime('input_date');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_usages');
    }
};
