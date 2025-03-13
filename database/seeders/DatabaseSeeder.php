<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\ElectricityUsage;
use App\Models\RoomFeeCollection;
use App\Models\Tenant;
use App\Models\TenantContract;
use App\Models\User;
use App\Models\WaterUsage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt ràng buộc khóa ngoại để cải thiện hiệu suất
        Schema::disableForeignKeyConstraints();

        // Xóa dữ liệu hiện có
        RoomFeeCollection::truncate();
        ElectricityUsage::truncate();
        WaterUsage::truncate();
        TenantContract::truncate();
        ApartmentRoom::truncate();
        Apartment::truncate();
        Tenant::truncate();
        User::truncate();

        // Bật lại ràng buộc khóa ngoại
        Schema::enableForeignKeyConstraints();

        $this->command->info('đã xóa dữ liệu cũ, tạo dữ liệu mới');

        // Tạo 20 người dùng
        $users = User::factory()->count(20)->create();
        $this->command->info('tạo 20 người dùng');

        // Tạo 100 tòa nhà (mỗi user có khoảng 5 tòa)
        $apartments = [];
        foreach ($users as $user) {
            $userApartments = Apartment::factory()
                ->count(rand(3, 7))
                ->create(['user_id' => $user->id]);
            $apartments = array_merge($apartments, $userApartments->all());
        }
        $this->command->info('Đã tạo '.count($apartments).' tòa nhà');

        // Tạo 500 phòng (mỗi tòa nhà có khoảng 5 phòng)
        $rooms = [];
        foreach ($apartments as $apartment) {
            $apartmentRooms = ApartmentRoom::factory()
                ->count(rand(3, 7))
                ->create(['apartment_id' => $apartment->id]);
            $rooms = array_merge($rooms, $apartmentRooms->all());
        }
        $this->command->info('Đã tạo '.count($rooms).' phòng');

        // Tạo 200 người thuê
        $tenants = Tenant::factory()->count(200)->create();
        $this->command->info('Đã tạo 200 người thuê');

        // Tạo 300 hợp đồng (một số phòng có hợp đồng, một số không)
        $contracts = [];
        $roomIds = collect($rooms)->pluck('id')->toArray();
        shuffle($roomIds);
        $roomIds = array_slice($roomIds, 0, 300); // Chọn 300 phòng ngẫu nhiên

        foreach ($roomIds as $roomId) {
            $tenant = $tenants[array_rand($tenants->toArray())];

            // 70% hợp đồng đang hoạt động, 30% đã kết thúc
            if (rand(1, 100) <= 70) {
                $contract = TenantContract::factory()->active()->create([
                    'apartment_room_id' => $roomId,
                    'tenant_id' => $tenant->id,
                ]);
            } else {
                $contract = TenantContract::factory()->ended()->create([
                    'apartment_room_id' => $roomId,
                    'tenant_id' => $tenant->id,
                ]);
            }

            $contracts[] = $contract;
        }
        $this->command->info('Đã tạo '.count($contracts).' hợp đồng');

        // Tạo 1000 mục thu phí (mỗi hợp đồng có một số mục thu phí)
        $feeCollections = [];
        foreach ($contracts as $contract) {
            // Tạo 2-5 mục phí cho mỗi hợp đồng
            $feeCount = rand(2, 5);

            for ($i = 0; $i < $feeCount; $i++) {
                // Tạo các mục phí theo thứ tự thời gian
                // 70% đã thanh toán đủ, 30% chưa thanh toán đủ
                if (rand(1, 100) <= 70) {
                    $feeCollection = RoomFeeCollection::factory()->paid()->forContract($contract)->create();
                } else {
                    $feeCollection = RoomFeeCollection::factory()->unpaid()->forContract($contract)->create();
                }

                $feeCollections[] = $feeCollection;
            }
        }
        $this->command->info('Đã tạo '.count($feeCollections).' mục thu phí');

        // Tạo 1000 chỉ số điện và nước
        $roomIdsForUtilities = collect($rooms)->pluck('id')->toArray();

        // Tạo chỉ số điện
        foreach (array_slice($roomIdsForUtilities, 0, 500) as $roomId) {
            ElectricityUsage::factory()->create(['apartment_room_id' => $roomId]);
        }
        $this->command->info('Đã tạo 500 chỉ số điện');

        // Tạo chỉ số nước
        foreach (array_slice($roomIdsForUtilities, 0, 500) as $roomId) {
            WaterUsage::factory()->create(['apartment_room_id' => $roomId]);
        }
        $this->command->info('Đã tạo 500 chỉ số nước');

        $this->command->info('Đã tạo thành công tất cả dữ liệu!');
    }
}
