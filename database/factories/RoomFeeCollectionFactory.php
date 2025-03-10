<?php

namespace Database\Factories;

use App\Models\ApartmentRoom;
use App\Models\RoomFeeCollection;
use App\Models\Tenant;
use App\Models\TenantContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoomFeeCollectionFactory extends Factory
{
    protected $model = RoomFeeCollection::class;

    public function definition(): array
    {
        $contract = TenantContract::factory()->create();
        $electricityBefore = $this->faker->numberBetween(100, 1000);
        $electricityAfter = $electricityBefore + $this->faker->numberBetween(30, 200);
        $waterBefore = $this->faker->numberBetween(10, 100);
        $waterAfter = $waterBefore + $this->faker->numberBetween(3, 20);
        
        // Calculate base fee components
        $electricityUsage = $electricityAfter - $electricityBefore;
        $waterUsage = $waterAfter - $waterBefore;
        
        // Base price from contract
        $basePrice = $contract->price;
        
        // Calculate electricity cost based on pay type
        $electricityCost = 0;
        if ($contract->electricity_pay_type == 1) { // per person
            $electricityCost = $contract->electricity_price * $contract->number_of_tenant_current;
        } elseif ($contract->electricity_pay_type == 2) { // fixed
            $electricityCost = $contract->electricity_price;
        } else { // by usage
            $electricityCost = $electricityUsage * $contract->electricity_price;
        }
        
        // Calculate water cost based on pay type
        $waterCost = 0;
        if ($contract->water_pay_type == 1) { // per person
            $waterCost = $contract->water_price * $contract->number_of_tenant_current;
        } elseif ($contract->water_pay_type == 2) { // fixed
            $waterCost = $contract->water_price;
        } else { // by usage
            $waterCost = $waterUsage * $contract->water_price;
        }
        
        // Total price
        $totalPrice = $basePrice + $electricityCost + $waterCost;
        
        // Determine if fully paid
        $fullyPaid = $this->faker->boolean(70); // 70% chance of being fully paid
        $totalPaid = $fullyPaid ? $totalPrice : $this->faker->numberBetween($totalPrice * 0.3, $totalPrice * 0.9);
        
        return [
            'tenant_contract_id' => $contract->id,
            'apartment_room_id' => $contract->apartment_room_id,
            'tenant_id' => $contract->tenant_id,
            'electricity_number_before' => $electricityBefore,
            'electricity_number_after' => $electricityAfter,
            'water_number_before' => $waterBefore,
            'water_number_after' => $waterAfter,
            'charge_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'total_debt' => $this->faker->numberBetween(0, 1000000),
            'total_price' => $totalPrice,
            'total_paid' => $totalPaid,
            'fee_collection_uuid' => Str::uuid(),
        ];
    }
    
    /**
     * Define a state for paid fees
     */
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'total_paid' => $attributes['total_price'],
            ];
        });
    }
    
    /**
     * Define a state for unpaid fees
     */
    public function unpaid()
    {
        return $this->state(function (array $attributes) {
            return [
                'total_paid' => $this->faker->numberBetween(0, $attributes['total_price'] * 0.8),
            ];
        });
    }
    
    /**
     * For fees created with specific existing relations
     */
    public function forContract(TenantContract $contract)
    {
        return $this->state(function (array $attributes) use ($contract) {
            return [
                'tenant_contract_id' => $contract->id,
                'apartment_room_id' => $contract->apartment_room_id,
                'tenant_id' => $contract->tenant_id,
            ];
        });
    }
}