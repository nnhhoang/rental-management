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
        // Xóa dữ liệu cũ
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
        DB::table('personal_access_tokens')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info('Đã xóa dữ liệu cũ, bắt đầu tạo dữ liệu mới');

        // Tạo user chủ trọ với thông tin đăng nhập đơn giản
        $landlord = User::create([
            'name' => 'Chủ Trọ Demo',
            'email' => 'landlord@example.com',
            'password' => Hash::make('password123')
        ]);

        // Tạo token cho user để dễ dàng sử dụng API
        $landlord->tokens()->delete(); // Xóa token cũ nếu có
        $token = $landlord->createToken('test-token')->plainTextToken;
        $this->command->info('Đã tạo tài khoản chủ trọ: landlord@example.com / password123');
        $this->command->info('Token API: ' . $token);

        // Tạo các chi phí hàng tháng cơ bản
        $monthlyCosts = [
            'Internet' => MonthlyCost::create(['name' => 'Internet']),
            'Rác' => MonthlyCost::create(['name' => 'Rác']),
        ];
        $this->command->info('Đã tạo chi phí hàng tháng: Internet, Rác');

        // Tạo 2 tòa nhà cho landlord
        $building1 = Apartment::create([
            'user_id' => $landlord->id,
            'name' => 'Chung cư Minh Khai',
            'address' => '123 Minh Khai, Hà Nội',
            'province_id' => '01',
            'district_id' => '001',
            'ward_id' => '00001',
        ]);

        $building2 = Apartment::create([
            'user_id' => $landlord->id,
            'name' => 'Nhà trọ Thanh Xuân',
            'address' => '456 Thanh Xuân, Hà Nội',
            'province_id' => '01',
            'district_id' => '002',
            'ward_id' => '00002',
        ]);
        $this->command->info('Đã tạo 2 tòa nhà: Chung cư Minh Khai, Nhà trọ Thanh Xuân');

        // Tạo phòng cho mỗi tòa nhà
        $rooms = [];

        // Phòng cho tòa nhà 1
        $room101 = ApartmentRoom::create([
            'apartment_id' => $building1->id,
            'room_number' => '101',
            'default_price' => 3000000,
            'max_tenant' => 2,
        ]);
        $rooms[] = $room101;

        $room102 = ApartmentRoom::create([
            'apartment_id' => $building1->id,
            'room_number' => '102',
            'default_price' => 3500000,
            'max_tenant' => 3,
        ]);
        $rooms[] = $room102;

        // Phòng cho tòa nhà 2
        $room201 = ApartmentRoom::create([
            'apartment_id' => $building2->id,
            'room_number' => '201',
            'default_price' => 2500000,
            'max_tenant' => 2,
        ]);
        $rooms[] = $room201;

        $room202 = ApartmentRoom::create([
            'apartment_id' => $building2->id,
            'room_number' => '202',
            'default_price' => 2800000,
            'max_tenant' => 2,
        ]);
        $rooms[] = $room202;

        $this->command->info('Đã tạo 4 phòng: 101, 102, 201, 202');

        // Tạo người thuê
        $tenant1 = Tenant::create([
            'name' => 'Nguyễn Văn A',
            'tel' => '0912345678',
            'email' => 'nguyenvana@example.com',
            'identity_card_number' => '123456789012',
        ]);

        $tenant2 = Tenant::create([
            'name' => 'Trần Thị B',
            'tel' => '0987654321',
            'email' => 'tranthib@example.com',
            'identity_card_number' => '987654321098',
        ]);

        $tenant3 = Tenant::create([
            'name' => 'Lê Văn C',
            'tel' => '0912345678',
            'email' => 'levanc@example.com',
            'identity_card_number' => '123456789013',
        ]);
        $this->command->info('Đã tạo 3 người thuê: Nguyễn Văn A, Trần Thị B, Lê Văn C');

        // Tạo hợp đồng
        $startDate1 = Carbon::create(2024, 1, 1);
        $contract1 = TenantContract::create([
            'apartment_room_id' => $room101->id,
            'tenant_id' => $tenant1->id,
            'pay_period' => 3, // 3 tháng
            'price' => 3000000,
            'electricity_pay_type' => 3, // theo mức sử dụng
            'electricity_price' => 3500, // 3,500 VND/kWh
            'electricity_number_start' => 100,
            'water_pay_type' => 2, // cố định
            'water_price' => 100000, // 100,000 VND/tháng
            'water_number_start' => 50,
            'number_of_tenant_current' => 1,
            'start_date' => $startDate1,
            'end_date' => null, // hợp đồng còn hiệu lực
        ]);

        // Tạo chi phí hàng tháng cho hợp đồng 1
        ContractMonthlyCost::create([
            'tenant_contract_id' => $contract1->id,
            'monthly_cost_id' => $monthlyCosts['Internet']->id,
            'pay_type' => 2, // cố định
            'price' => 150000, // 150,000 VND
        ]);

        ContractMonthlyCost::create([
            'tenant_contract_id' => $contract1->id,
            'monthly_cost_id' => $monthlyCosts['Rác']->id,
            'pay_type' => 2, // cố định
            'price' => 30000, // 30,000 VND
        ]);

        $startDate2 = Carbon::create(2023, 12, 1);
        $endDate2 = Carbon::create(2024, 3, 1);
        $contract2 = TenantContract::create([
            'apartment_room_id' => $room201->id,
            'tenant_id' => $tenant2->id,
            'pay_period' => 3, // 3 tháng
            'price' => 2500000,
            'electricity_pay_type' => 3, // theo mức sử dụng
            'electricity_price' => 3500, // 3,500 VND/kWh
            'electricity_number_start' => 50,
            'water_pay_type' => 2, // cố định
            'water_price' => 100000, // 100,000 VND/tháng
            'water_number_start' => 20,
            'number_of_tenant_current' => 2,
            'start_date' => $startDate2,
            'end_date' => $endDate2, // hợp đồng đã kết thúc
        ]);

        // Tạo hợp đồng mới cho phòng 201 sau khi hợp đồng cũ kết thúc
        $startDate3 = Carbon::create(2024, 3, 5);
        $contract3 = TenantContract::create([
            'apartment_room_id' => $room201->id,
            'tenant_id' => $tenant3->id,
            'pay_period' => 6, // 6 tháng
            'price' => 2600000,
            'electricity_pay_type' => 3, // theo mức sử dụng
            'electricity_price' => 3500, // 3,500 VND/kWh
            'electricity_number_start' => 150,
            'water_pay_type' => 2, // cố định
            'water_price' => 100000, // 100,000 VND/tháng
            'water_number_start' => 45,
            'number_of_tenant_current' => 1,
            'start_date' => $startDate3,
            'end_date' => null, // hợp đồng còn hiệu lực
        ]);
        $this->command->info('Đã tạo 3 hợp đồng: 2 đang hoạt động, 1 đã kết thúc');

        // Tạo chỉ số điện nước cho hợp đồng 1
        ElectricityUsage::create([
            'apartment_room_id' => $room101->id,
            'usage_number' => 150, // Số điện tháng đầu tiên
            'input_date' => (clone $startDate1)->addMonth(1),
        ]);

        ElectricityUsage::create([
            'apartment_room_id' => $room101->id,
            'usage_number' => 210, // Số điện tháng thứ hai
            'input_date' => (clone $startDate1)->addMonths(2),
        ]);

        WaterUsage::create([
            'apartment_room_id' => $room101->id,
            'usage_number' => 55, // Số nước tháng đầu tiên
            'input_date' => (clone $startDate1)->addMonth(1),
        ]);

        WaterUsage::create([
            'apartment_room_id' => $room101->id,
            'usage_number' => 61, // Số nước tháng thứ hai
            'input_date' => (clone $startDate1)->addMonths(2),
        ]);

        // Tạo phí thu cho hợp đồng 1
        $fee1 = RoomFeeCollection::create([
            'tenant_contract_id' => $contract1->id,
            'apartment_room_id' => $room101->id,
            'tenant_id' => $tenant1->id,
            'electricity_number_before' => 100,
            'electricity_number_after' => 150,
            'water_number_before' => 50,
            'water_number_after' => 55,
            'charge_date' => (clone $startDate1)->addMonth(1),
            'total_debt' => 0,
            'total_price' => 3350000, // Tiền phòng + điện nước + phí
            'total_paid' => 3350000, // Đã thanh toán đủ
            'fee_collection_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ]);

        // Tạo lịch sử thanh toán
        RoomFeeCollectionHistory::create([
            'room_fee_collection_id' => $fee1->id,
            'paid_date' => (clone $startDate1)->addMonth(1)->addDays(3),
            'price' => 3350000,
        ]);

        // Tạo phí thu cho hợp đồng 1 tháng thứ hai (chưa thanh toán)
        $fee2 = RoomFeeCollection::create([
            'tenant_contract_id' => $contract1->id,
            'apartment_room_id' => $room101->id,
            'tenant_id' => $tenant1->id,
            'electricity_number_before' => 150,
            'electricity_number_after' => 210,
            'water_number_before' => 55,
            'water_number_after' => 61,
            'charge_date' => (clone $startDate1)->addMonths(2),
            'total_debt' => 0,
            'total_price' => 3550000, // Tiền phòng + điện nước + phí
            'total_paid' => 1000000, // Mới trả một phần
            'fee_collection_uuid' => '123e4567-e89b-12d3-a456-426614174001',
        ]);

        // Tạo lịch sử thanh toán một phần
        RoomFeeCollectionHistory::create([
            'room_fee_collection_id' => $fee2->id,
            'paid_date' => (clone $startDate1)->addMonths(2)->addDays(2),
            'price' => 1000000,
        ]);

        $this->command->info('Đã tạo 2 kỳ phí thu: 1 đã thanh toán đủ, 1 còn thiếu');
        $this->command->info('Đã tạo thành công bộ dữ liệu test!');
    }
}
