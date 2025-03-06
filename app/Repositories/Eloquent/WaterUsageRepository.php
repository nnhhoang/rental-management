<?php
namespace App\Repositories\Eloquent;

use App\Models\WaterUsage;
use App\Repositories\Contracts\WaterUsageRepositoryInterface;

class WaterUsageRepository extends BaseRepository implements WaterUsageRepositoryInterface
{
    public function __construct(WaterUsage $model)
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