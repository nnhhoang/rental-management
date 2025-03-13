<?php

namespace Database\Factories;

use App\Models\ApartmentRoom;
use App\Models\WaterUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaterUsageFactory extends Factory
{
    protected $model = WaterUsage::class;

    public function definition(): array
    {
        return [
            'apartment_room_id' => ApartmentRoom::factory(),
            'usage_number' => $this->faker->numberBetween(10, 200),
            'input_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'image' => null, // $this->faker->imageUrl(640, 480, 'meter') - if needed
        ];
    }
}
