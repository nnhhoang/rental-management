<?php
namespace App\Repositories\Contracts;

interface TenantRepositoryInterface extends BaseRepositoryInterface
{
    public function searchTenants(string $query);
    public function getTenantsByUser(int $userId);
}