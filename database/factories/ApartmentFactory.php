<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    protected $model = Apartment::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'user_id' => User::factory(),
            'name' => substr($name . ' Apartment', 0, 45), 
            'province_id' => $this->faker->randomElement(['01', '02', '03', '04', '05']),
            'district_id' => $this->faker->randomElement(['001', '002', '003', '004', '005']),
            'ward_id' => $this->faker->randomElement(['00001', '00002', '00003', '00004', '00005']),
            'address' => substr($this->faker->address(), 0, 255),
            'image' => null,
        ];
    }
}