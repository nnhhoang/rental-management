<?php

namespace App\Services;

use App\Repositories\Contracts\TenantRepositoryInterface;

class TenantService
{
    protected $tenantRepository;

    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * Get all tenants, optionally with their contracts
     *
     * @param  bool  $withContracts
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTenants($withContracts = false)
    {
        if ($withContracts) {
            return $this->tenantRepository->getAllWithContracts();
        }

        return $this->tenantRepository->all();
    }

    /**
     * Search tenants by query string, optionally with their contracts
     *
     * @param  bool  $withContracts
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchTenants(string $query, $withContracts = false)
    {
        if ($withContracts) {
            return $this->tenantRepository->searchWithContracts($query);
        }

        return $this->tenantRepository->searchTenants($query);
    }

    /**
     * Get a tenant by ID, optionally with their contracts
     *
     * @param  bool  $withContracts
     * @return \App\Models\Tenant|null
     */
    public function getTenant(int $id, $withContracts = false)
    {
        if ($withContracts) {
            return $this->tenantRepository->findWithContracts($id);
        }

        return $this->tenantRepository->find($id);
    }

    /**
     * Get tenants by user ID, optionally with their contracts
     *
     * @param  bool  $withContracts
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTenantsByUser(int $userId, $withContracts = false)
    {
        if ($withContracts) {
            return $this->tenantRepository->getTenantsByUserWithContracts($userId);
        }

        return $this->tenantRepository->getTenantsByUser($userId);
    }

    /**
     * Create a new tenant
     *
     * @return \App\Models\Tenant
     */
    public function createTenant(array $data)
    {
        return $this->tenantRepository->create($data);
    }

    /**
     * Update an existing tenant
     *
     * @return \App\Models\Tenant|bool
     */
    public function updateTenant(int $id, array $data)
    {
        return $this->tenantRepository->update($id, $data);
    }

    /**
     * Delete a tenant
     *
     * @return bool
     */
    public function deleteTenant(int $id)
    {
        return $this->tenantRepository->delete($id);
    }
}
