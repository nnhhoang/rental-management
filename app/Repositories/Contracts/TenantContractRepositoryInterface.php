<?php
namespace App\Repositories\Contracts;

interface TenantContractRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveContracts();
    public function getActiveContractByRoom(int $roomId);
    public function getContractHistory(int $tenantId);
}