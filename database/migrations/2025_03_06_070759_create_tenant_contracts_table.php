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
        Schema::create('tenant_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_room_id')->constrained('apartment_rooms')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->integer('pay_period'); // 1, 3, 6, 12 months
            $table->bigInteger('price');
            $table->integer('electricity_pay_type'); // 1: per person, 2: fixed, 3: by usage
            $table->bigInteger('electricity_price');
            $table->integer('electricity_number_start');
            $table->integer('water_pay_type'); // 1: per person, 2: fixed, 3: by usage
            $table->bigInteger('water_price');
            $table->integer('water_number_start');
            $table->integer('number_of_tenant_current');
            $table->text('note')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_contracts');
    }
};
