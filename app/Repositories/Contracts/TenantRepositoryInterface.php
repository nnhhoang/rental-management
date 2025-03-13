<?php

namespace App\Repositories\Contracts;

interface TenantRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search tenants by query string
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchTenants(string $query);

    /**
     * Get tenants associated with a user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTenantsByUser(int $userId);

    /**
     * Get all tenants with their contracts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithContracts();

    /**
     * Find a tenant by ID with their contracts
     *
     * @param  int  $id
     * @return \App\Models\Tenant|null
     */
    public function findWithContracts($id);

    /**
     * Search tenants by query string and include their contracts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchWithContracts(string $query);

    /**
     * Get tenants associated with a user, including their contracts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTenantsByUserWithContracts(int $userId);
}
