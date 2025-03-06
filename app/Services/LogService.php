<?php
namespace App\Services;

use App\Repositories\Contracts\LogRepositoryInterface;

class LogService
{
    protected $logRepository;

    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function createLog(int $userId, string $action, string $description, array $data = null)
    {
        return $this->logRepository->createLog($userId, $action, $description, $data);
    }

    public function getLogsByUser(int $userId)
    {
        return $this->logRepository->getLogsByUser($userId);
    }

    public function getLogsByAction(string $action)
    {
        return $this->logRepository->getLogsByAction($action);
    }

    public function getAllLogs()
    {
        return $this->logRepository->all();
    }
}