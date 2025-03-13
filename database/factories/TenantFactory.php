<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'tel' => $this->faker->numerify('0#########'), // Format Vietnamese phone numbers
            'email' => $this->faker->unique()->safeEmail(),
            'identity_card_number' => $this->faker->numerify('############'),
        ];
    }
}
