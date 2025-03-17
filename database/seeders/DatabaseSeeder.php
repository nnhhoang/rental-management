<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\ContractMonthlyCost;
use App\Models\ElectricityUsage;
use App\Models\MonthlyCost;
use App\Models\RoomFeeCollection;
use App\Models\RoomFeeCollectionHistory;
use App\Models\Tenant;
use App\Models\TenantContract;
use App\Models\User;
use App\Models\WaterUsage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data first
        Schema::disableForeignKeyConstraints();
        RoomFeeCollectionHistory::truncate();
        RoomFeeCollection::truncate();
        ContractMonthlyCost::truncate();
        ElectricityUsage::truncate();
        WaterUsage::truncate();
        TenantContract::truncate();
        ApartmentRoom::truncate();
        Apartment::truncate();
        MonthlyCost::truncate();
        Tenant::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info('Đã xóa dữ liệu cũ, tạo dữ liệu mới');

        // Create fixed password for all users
        $standardPassword = Hash::make('password123');

        // Create admin user with known credentials
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => $standardPassword
        ]);
        $this->command->info('Đã tạo tài khoản admin: admin@example.com / password123');

        // Create demo user for easy testing
        $demoUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => $standardPassword
        ]);
        $this->command->info('Đã tạo tài khoản demo: demo@example.com / password123');

        // Create regular users
        $users = User::factory()
            ->count(18) // 18 + admin + demo = 20 users total
            ->create([
                'password' => $standardPassword
            ]);
        $users = $users->concat([$admin, $demoUser]);
        $this->command->info('Tạo 20 người dùng với mật khẩu: password123');

        // Create monthly costs
        $monthlyCosts = [
            'Internet' => MonthlyCost::create(['name' => 'Internet']),
            'Trash' => MonthlyCost::create(['name' => 'Trash']),
            'Security' => MonthlyCost::create(['name' => 'Security']),
            'Cleaning' => MonthlyCost::create(['name' => 'Cleaning']),
            'Parking' => MonthlyCost::create(['name' => 'Parking']),
        ];
        $this->command->info('Đã tạo các loại chi phí hàng tháng');

        // Create apartments for each user
        $apartments = [];
        foreach ($users as $user) {
            $userApartments = Apartment::factory()
                ->count(rand(3, 7))
                ->create(['user_id' => $user->id]);
            $apartments = array_merge($apartments, $userApartments->all());
        }
        $this->command->info('Đã tạo ' . count($apartments) . ' tòa nhà');

        // Create rooms for each apartment
        $rooms = [];
        foreach ($apartments as $apartment) {
            $apartmentRooms = ApartmentRoom::factory()
                ->count(rand(3, 7))
                ->create(['apartment_id' => $apartment->id]);
            $rooms = array_merge($rooms, $apartmentRooms->all());
        }
        $this->command->info('Đã tạo ' . count($rooms) . ' phòng');

        // Create tenants
        $tenants = Tenant::factory()->count(200)->create();
        $this->command->info('Đã tạo 200 người thuê');

        // Create contracts - ensure every tenant has at least one contract
        $contracts = [];

        // First ensure every tenant has at least one contract
        foreach ($tenants as $tenant) {
            // Find a room without an active contract
            $roomId = null;
            foreach ($rooms as $room) {
                $hasActiveContract = TenantContract::where('apartment_room_id', $room->id)
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>', now());
                    })->exists();

                if (!$hasActiveContract) {
                    $roomId = $room->id;
                    break;
                }
            }

            // If no room found, just pick a random one
            if (!$roomId && count($rooms) > 0) {
                $room = $rooms[array_rand($rooms)];
                $roomId = $room->id;
            }

            if ($roomId) {
                // 70% active contracts, 30% ended
                if (rand(1, 100) <= 70) {
                    $contract = TenantContract::factory()->active()->create([
                        'apartment_room_id' => $roomId,
                        'tenant_id' => $tenant->id
                    ]);
                } else {
                    $contract = TenantContract::factory()->ended()->create([
                        'apartment_room_id' => $roomId,
                        'tenant_id' => $tenant->id
                    ]);
                }

                $contracts[] = $contract;

                // Add 1-3 monthly costs to each contract
                $costCount = rand(1, 3);
                $selectedCosts = array_rand($monthlyCosts, $costCount);

                if (!is_array($selectedCosts)) {
                    $selectedCosts = [$selectedCosts];
                }

                foreach ($selectedCosts as $costKey) {
                    ContractMonthlyCost::create([
                        'tenant_contract_id' => $contract->id,
                        'monthly_cost_id' => $monthlyCosts[$costKey]->id,
                        'pay_type' => rand(1, 3), // 1: per person, 2: fixed, 3: by usage
                        'price' => rand(5, 30) * 10000 // 50,000 - 300,000 VND
                    ]);
                }
            }
        }

        // Create more contracts to reach desired number
        $roomIds = collect($rooms)->pluck('id')->toArray();
        shuffle($roomIds);

        // Count how many more contracts to create
        $additionalContractsNeeded = max(0, 300 - count($contracts));

        // Create additional contracts
        for ($i = 0; $i < $additionalContractsNeeded; $i++) {
            $roomId = $roomIds[$i % count($roomIds)]; // Cycle through rooms
            $tenant = $tenants[array_rand($tenants->toArray())];

            if (rand(1, 100) <= 70) {
                $contract = TenantContract::factory()->active()->create([
                    'apartment_room_id' => $roomId,
                    'tenant_id' => $tenant->id
                ]);
            } else {
                $contract = TenantContract::factory()->ended()->create([
                    'apartment_room_id' => $roomId,
                    'tenant_id' => $tenant->id
                ]);
            }

            $contracts[] = $contract;

            // Add monthly costs
            $costCount = rand(1, 3);
            $selectedCosts = array_rand($monthlyCosts, $costCount);

            if (!is_array($selectedCosts)) {
                $selectedCosts = [$selectedCosts];
            }

            foreach ($selectedCosts as $costKey) {
                ContractMonthlyCost::create([
                    'tenant_contract_id' => $contract->id,
                    'monthly_cost_id' => $monthlyCosts[$costKey]->id,
                    'pay_type' => rand(1, 3),
                    'price' => rand(5, 30) * 10000
                ]);
            }
        }

        $this->command->info('Đã tạo ' . count($contracts) . ' hợp đồng');

        // Create fee collections
        $feeCollections = [];
        foreach ($contracts as $contract) {
            $feeCount = rand(1, 5);
            $startDate = new Carbon($contract->start_date);

            for ($i = 0; $i < $feeCount; $i++) {
                $chargeDate = (clone $startDate)->addMonths($i);

                // Don't create fee collections for future months
                if ($chargeDate > now()) {
                    continue;
                }

                if (rand(1, 100) <= 70) {
                    $feeCollection = RoomFeeCollection::factory()
                        ->paid()
                        ->forContract($contract)
                        ->create([
                            'charge_date' => $chargeDate
                        ]);

                    // Add payment history
                    RoomFeeCollectionHistory::create([
                        'room_fee_collection_id' => $feeCollection->id,
                        'paid_date' => (clone $chargeDate)->addDays(rand(1, 5)),
                        'price' => $feeCollection->total_price
                    ]);
                } else {
                    $feeCollection = RoomFeeCollection::factory()
                        ->unpaid()
                        ->forContract($contract)
                        ->create([
                            'charge_date' => $chargeDate
                        ]);
                }

                $feeCollections[] = $feeCollection;
            }
        }
        $this->command->info('Đã tạo ' . count($feeCollections) . ' mục thu phí');

        // Create utility readings aligned with contracts
        $electricityReadings = [];
        $waterReadings = [];

        foreach ($contracts as $contract) {
            $roomId = $contract->apartment_room_id;
            $startDate = new Carbon($contract->start_date);
            $endDate = $contract->end_date ? new Carbon($contract->end_date) : now();

            // Create monthly readings from contract start to contract end or current date
            $currentDate = clone $startDate;
            $lastElectricityReading = $contract->electricity_number_start;
            $lastWaterReading = $contract->water_number_start;

            while ($currentDate <= $endDate) {
                // Create electricity reading
                $newReading = $lastElectricityReading + rand(20, 100);
                $electricityUsage = ElectricityUsage::create([
                    'apartment_room_id' => $roomId,
                    'usage_number' => $newReading,
                    'input_date' => clone $currentDate,
                    'image' => null
                ]);
                $electricityReadings[] = $electricityUsage;
                $lastElectricityReading = $newReading;

                // Create water reading
                $newReading = $lastWaterReading + rand(2, 10);
                $waterUsage = WaterUsage::create([
                    'apartment_room_id' => $roomId,
                    'usage_number' => $newReading,
                    'input_date' => clone $currentDate,
                    'image' => null
                ]);
                $waterReadings[] = $waterUsage;
                $lastWaterReading = $newReading;

                $currentDate->addMonth();
            }
        }

        $this->command->info('Đã tạo ' . count($electricityReadings) . ' chỉ số điện');
        $this->command->info('Đã tạo ' . count($waterReadings) . ' chỉ số nước');
        $this->command->info('Đã tạo thành công tất cả dữ liệu!');
    }
}
