<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\TenantContractRepositoryInterface;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;
use App\Repositories\Contracts\RoomFeeCollectionRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;

class ContractService {
    protected $contractRepository;
    protected $roomRepository;
    protected $feeCollectionRepository;
    protected $tenantRepository;

    public function __construct(
        TenantContractRepositoryInterface $contractRepository,
        ApartmentRoomRepositoryInterface $roomRepository,
        RoomFeeCollectionRepositoryInterface $feeCollectionRepository,
        TenantRepositoryInterface $tenantRepository
    ) 
    {
        $this->contractRepository = $contractRepository;
        $this->roomRepository = $roomRepository;
        $this->feeCollectionRepository = $feeCollectionRepository;
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * Get all contracts
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllContracts()
    {
        return $this->contractRepository->all();
    }

    /**
     * Get contracts with filters
     * 
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredContracts(array $filters)
    {
        if (isset($filters['active']) && $filters['active']) {
            return $this->contractRepository->getActiveContracts();
        }
        
        if (isset($filters['tenant_id']) && $filters['tenant_id']) {
            return $this->getContractHistory($filters['tenant_id']);
        }
        
        if (isset($filters['room_id']) && $filters['room_id']) {
            return $this->contractRepository->getContractsByRoom($filters['room_id']);
        }
        
        if (isset($filters['apartment_id']) && $filters['apartment_id']) {
            return $this->contractRepository->getContractsByApartment($filters['apartment_id']);
        }
        
        return $this->contractRepository->all();
    }

    /**
     * Get a specific contract
     * 
     * @param int $id
     * @return \App\Models\TenantContract|null
     */
    public function getContract(int $id)
    {
        return $this->contractRepository->find($id);
    }

    /**
     * Get active contracts
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveContracts()
    {
        return $this->contractRepository->getActiveContracts();
    }

    /**
     * Get active contract by room
     * 
     * @param int $roomId
     * @return \App\Models\TenantContract|null
     */
    public function getActiveContractByRoom(int $roomId)
    {
        return $this->contractRepository->getActiveContractByRoom($roomId);
    }

    /**
     * Get contract history for a tenant
     * 
     * @param int $tenantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractHistory(int $tenantId)
    {
        return $this->contractRepository->getContractHistory($tenantId);
    }

    /**
     * Check if tenant has active contracts
     * 
     * @param int $tenantId
     * @return bool
     */
    public function tenantHasActiveContracts(int $tenantId)
    {
        return $this->contractRepository->tenantHasActiveContracts($tenantId);
    }

    /**
     * Check if contract has fee collections
     * 
     * @param int $contractId
     * @return bool
     */
    public function contractHasFeeCollections(int $contractId)
    {
        return $this->feeCollectionRepository->contractHasFeeCollections($contractId);
    }

    /**
     * Create a new contract
     * 
     * @param array $data
     * @return array
     */
    public function createContract(array $data)
    {
        DB::beginTransaction();
    
        try {
            
            if ($data['is_create_tenant']) {
                $tenant = $this->tenantRepository->create([
                    'name' => $data['name'],
                    'tel' => $data['tel'],
                    'email' => $data['email'],
                    'identity_card_number' => $data['identity_card_number']
                ]);
                $tenantId = $tenant->id;
            } else {
                $tenantId = $data['tenant_id'];
            }            

            $contract = $this->contractRepository->create([
                'apartment_room_id' => $data['apartment_room_id'],
                'tenant_id' => $tenantId,
                'pay_period' => $data['pay_period'],
                'price' => $data['price'],
                'number_of_tenant_current' => $data['number_of_tenant_current'],
                'note' => $data['note'] ?? null,
                'electricity_pay_type' => $data['electricity_pay_type'],
                'electricity_price' => $data['electricity_price'],
                'electricity_number_start' => $data['electricity_number_start'],
                'water_pay_type' => $data['water_pay_type'],
                'water_price' => $data['water_price'],
                'water_number_start' => $data['water_number_start'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);
    
            DB::commit();
    
            return [
                'success' => true,
                'contract' => $contract,
                'tenant_created' => $data['is_create_tenant'] ?? false
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Update a contract
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\TenantContract|bool
     */
    public function updateContract(int $id, array $data)
    {
        return $this->contractRepository->update($id, $data);
    }

    /**
     * Terminate a contract
     * 
     * @param int $id
     * @param mixed $endDate
     * @return \App\Models\TenantContract|bool
     */
    public function terminateContract(int $id, $endDate = null)
    {
        $endDate = $endDate ?: now();
        
        return $this->contractRepository->update($id, [
            'end_date' => $endDate
        ]);
    }

    /**
     * Delete a contract
     * 
     * @param int $id
     * @return bool
     */
    public function deleteContract(int $id)
    {
        return $this->contractRepository->delete($id);
    }
}