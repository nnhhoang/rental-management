<?php

namespace App\Repositories\Contracts;

interface WaterUsageRepositoryInterface extends BaseRepositoryInterface
{
    public function getLatestByRoom(int $roomId);

    public function getByDateRange(int $roomId, $startDate, $endDate);
}
