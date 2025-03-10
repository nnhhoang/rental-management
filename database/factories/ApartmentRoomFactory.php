<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\ApartmentRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentRoomFactory extends Factory
{
    protected $model = ApartmentRoom::class;

    public function definition(): array
    {
        return [
            'apartment_id' => Apartment::factory(),
            'room_number' => $this->faker->numerify('P###'), // P101, P102, etc.
            'default_price' => $this->faker->numberBetween(2000000, 8000000),
            'max_tenant' => $this->faker->numberBetween(1, 4),
            'image' => null, // $this->faker->imageUrl(640, 480, 'room') - if you need images
        ];
    }
}