<?php
namespace App\Repositories\Contracts;

interface LogRepositoryInterface extends BaseRepositoryInterface
{
    public function createLog(int $userId, string $action, string $description, array $data = null);
    public function getLogsByUser(int $userId);
    public function getLogsByAction(string $action);
    public function getRecentLogs($limit = 10);
}