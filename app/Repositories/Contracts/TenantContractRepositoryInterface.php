<?php
namespace App\Repositories\Contracts;

interface TenantContractRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active contracts
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveContracts();
    
    /**
     * Get active contract for a specific room
     * 
     * @param int $roomId
     * @return \App\Models\TenantContract|null
     */
    public function getActiveContractByRoom(int $roomId);
    
    /**
     * Get all contracts for a specific tenant
     * 
     * @param int $tenantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractHistory(int $tenantId);
    
    /**
     * Check if tenant has any active contracts
     * 
     * @param int $tenantId
     * @return bool
     */
    public function tenantHasActiveContracts(int $tenantId);
    
    /**
     * Get all contracts for rooms in a specific apartment
     * 
     * @param int $apartmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractsByApartment(int $apartmentId);
    
    /**
     * Get all contracts for a specific room
     * 
     * @param int $roomId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractsByRoom(int $roomId);
}