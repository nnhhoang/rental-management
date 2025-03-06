<?php
namespace App\Services;

use App\Repositories\Contracts\RoomFeeCollectionRepositoryInterface;
use App\Repositories\Contracts\TenantContractRepositoryInterface;
use App\Repositories\Contracts\ElectricityUsageRepositoryInterface;
use App\Repositories\Contracts\WaterUsageRepositoryInterface;
use Illuminate\Support\Str;

class FeeCollectionService
{
    protected $feeCollectionRepository;
    protected $contractRepository;
    protected $electricityRepository;
    protected $waterRepository;

    public function __construct(
        RoomFeeCollectionRepositoryInterface $feeCollectionRepository,
        TenantContractRepositoryInterface $contractRepository,
        ElectricityUsageRepositoryInterface $electricityRepository,
        WaterUsageRepositoryInterface $waterRepository
    ) {
        $this->feeCollectionRepository = $feeCollectionRepository;
        $this->contractRepository = $contractRepository;
        $this->electricityRepository = $electricityRepository;
        $this->waterRepository = $waterRepository;
    }

    public function getAllFeeCollections()
    {
        return $this->feeCollectionRepository->all();
    }

    public function getFeeCollection(int $id)
    {
        return $this->feeCollectionRepository->find($id);
    }

    public function getFeesByRoom(int $roomId)
    {
        return $this->feeCollectionRepository->getFeesByRoom($roomId);
    }

    public function getUnpaidFees()
    {
        return $this->feeCollectionRepository->getUnpaidFees();
    }

    public function getUnpaidFeesByMonth($month, $year)
    {
        return $this->feeCollectionRepository->getUnpaidFeesByMonth($month, $year);
    }

    public function createFeeCollection(array $data)
    {
        // Check if room has an active contract
        $contract = $this->contractRepository->getActiveContractByRoom($data['apartment_room_id']);
        
        if (!$contract) {
            return [
                'success' => false,
                'message' => 'Room does not have an active contract'
            ];
        }
        
        // Set tenant contract and tenant data
        $data['tenant_contract_id'] = $contract->id;
        $data['tenant_id'] = $contract->tenant_id;
        
        // Generate unique ID
        $data['fee_collection_uuid'] = Str::uuid();
        
        // Calculate total debt (if any previous unpaid fees)
        $previousFees = $this->feeCollectionRepository->getFeesByRoom($data['apartment_room_id']);
        $previousDebt = 0;
        
        foreach ($previousFees as $fee) {
            if ($fee->total_paid < $fee->total_price) {
                $previousDebt += ($fee->total_price - $fee->total_paid);
            }
        }
        
        $data['total_debt'] = $previousDebt;
        
        $feeCollection = $this->feeCollectionRepository->create($data);
        
        return [
            'success' => true,
            'feeCollection' => $feeCollection
        ];
    }

    public function updateFeeCollection(int $id, array $data)
    {
        return $this->feeCollectionRepository->update($id, $data);
    }

    public function recordPayment(int $feeCollectionId, float $amount)
    {
        $feeCollection = $this->feeCollectionRepository->find($feeCollectionId);
        
        if (!$feeCollection) {
            return false;
        }
        
        $newTotalPaid = $feeCollection->total_paid + $amount;
        
        // Ensure we don't pay more than the total price
        if ($newTotalPaid > $feeCollection->total_price) {
            $newTotalPaid = $feeCollection->total_price;
        }
        
        return $this->feeCollectionRepository->update($feeCollectionId, [
            'total_paid' => $newTotalPaid
        ]);
    }

    public function deleteFeeCollection(int $id)
    {
        return $this->feeCollectionRepository->delete($id);
    }

    public function getMonthlyFeeStatistics($year)
    {
        return $this->feeCollectionRepository->getMonthlyFeeStatistics($year);
    }
}
