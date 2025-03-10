<?php

namespace Database\Factories;

use App\Models\ApartmentRoom;
use App\Models\Tenant;
use App\Models\TenantContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantContractFactory extends Factory
{
    protected $model = TenantContract::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', '-1 month');
        $hasEndDate = $this->faker->boolean(30); // 30% chance to have end date (contract ended)
        $endDate = $hasEndDate 
            ? $this->faker->dateTimeBetween($startDate, 'now') 
            : null;
            
        // Get the electricity and water starting numbers
        $electricityStart = $this->faker->numberBetween(100, 1000);
        $waterStart = $this->faker->numberBetween(10, 100);
            
        return [
            'apartment_room_id' => ApartmentRoom::factory(),
            'tenant_id' => Tenant::factory(),
            'pay_period' => $this->faker->randomElement([1, 3, 6, 12]), // months
            'price' => $this->faker->numberBetween(2000000, 8000000),
            'electricity_pay_type' => $this->faker->randomElement([1, 2, 3]), // 1: per person, 2: fixed, 3: by usage
            'electricity_price' => $this->faker->numberBetween(3000, 5000), // VND per kWh
            'electricity_number_start' => $electricityStart,
            'water_pay_type' => $this->faker->randomElement([1, 2, 3]), // 1: per person, 2: fixed, 3: by usage
            'water_price' => $this->faker->numberBetween(15000, 30000), // VND per m3
            'water_number_start' => $waterStart,
            'number_of_tenant_current' => $this->faker->numberBetween(1, 4),
            'note' => $this->faker->optional(30)->text(200),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
    
    /**
     * Define a state for active contracts
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'end_date' => null,
            ];
        });
    }
    
    /**
     * Define a state for ended contracts
     */
    public function ended()
    {
        return $this->state(function (array $attributes) {
            return [
                'end_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            ];
        });
    }
}