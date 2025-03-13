<?php

namespace Database\Factories;

use App\Models\ApartmentRoom;
use App\Models\ElectricityUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectricityUsageFactory extends Factory
{
    protected $model = ElectricityUsage::class;

    public function definition(): array
    {
        return [
            'apartment_room_id' => ApartmentRoom::factory(),
            'usage_number' => $this->faker->numberBetween(100, 2000),
            'input_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'image' => null, // $this->faker->imageUrl(640, 480, 'meter') - if needed
        ];
    }
}
