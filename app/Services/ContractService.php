<?php
namespace App\Services;

use App\Repositories\Contracts\TenantContractRepositoryInterface;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;

class ContractService
{
    protected $contractRepository;
    protected $roomRepository;

    public function __construct(
        TenantContractRepositoryInterface $contractRepository,
        ApartmentRoomRepositoryInterface $roomRepository
    ) {
        $this->contractRepository = $contractRepository;
        $this->roomRepository = $roomRepository;
    }

    public function getAllContracts()
    {
        return $this->contractRepository->all();
    }

    public function getContract(int $id)
    {
        return $this->contractRepository->find($id);
    }

    public function getActiveContracts()
    {
        return $this->contractRepository->getActiveContracts();
    }

    public function getActiveContractByRoom(int $roomId)
    {
        return $this->contractRepository->getActiveContractByRoom($roomId);
    }

    public function getContractHistory(int $tenantId)
    {
        return $this->contractRepository->getContractHistory($tenantId);
    }

    public function createContract(array $data)
    {
        // Check if room already has an active contract
        $activeContract = $this->contractRepository->getActiveContractByRoom($data['apartment_room_id']);
        
        if ($activeContract) {
            return [
                'success' => false,
                'message' => 'Room already has an active contract'
            ];
        }
        
        $contract = $this->contractRepository->create($data);
        
        return [
            'success' => true,
            'contract' => $contract
        ];
    }

    public function updateContract(int $id, array $data)
    {
        return $this->contractRepository->update($id, $data);
    }

    public function terminateContract(int $id, $endDate = null)
    {
        $endDate = $endDate ?: now();
        
        return $this->contractRepository->update($id, [
            'end_date' => $endDate
        ]);
    }

    public function deleteContract(int $id)
    {
        return $this->contractRepository->delete($id);
    }
}