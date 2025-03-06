<?php
namespace App\Repositories\Eloquent;

use App\Models\ElectricityUsage;
use App\Repositories\Contracts\ElectricityUsageRepositoryInterface;

class ElectricityUsageRepository extends BaseRepository implements ElectricityUsageRepositoryInterface
{
    public function __construct(ElectricityUsage $model)
    {
        parent::__construct($model);
    }

    public function getLatestByRoom(int $roomId)
    {
        return $this->model
            ->where('apartment_room_id', $roomId)
            ->orderBy('input_date', 'desc')
            ->first();
    }

    public function getByDateRange(int $roomId, $startDate, $endDate)
    {
        return $this->model
            ->where('apartment_room_id', $roomId)
            ->whereBetween('input_date', [$startDate, $endDate])
            ->orderBy('input_date', 'asc')
            ->get();
    }
}