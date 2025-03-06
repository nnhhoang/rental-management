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

    public function getAllTenants()
    {
        return $this->tenantRepository->all();
    }

    public function getTenant(int $id)
    {
        return $this->tenantRepository->find($id);
    }

    public function searchTenants(string $query)
    {
        return $this->tenantRepository->searchTenants($query);
    }

    public function getTenantsByUser(int $userId)
    {
        return $this->tenantRepository->getTenantsByUser($userId);
    }

    public function createTenant(array $data)
    {
        return $this->tenantRepository->create($data);
    }

    public function updateTenant(int $id, array $data)
    {
        return $this->tenantRepository->update($id, $data);
    }

    public function deleteTenant(int $id)
    {
        return $this->tenantRepository->delete($id);
    }
}
