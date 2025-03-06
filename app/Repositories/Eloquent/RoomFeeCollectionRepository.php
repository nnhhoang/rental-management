<?php
namespace App\Repositories\Eloquent;

use App\Models\RoomFeeCollection;
use App\Repositories\Contracts\RoomFeeCollectionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RoomFeeCollectionRepository extends BaseRepository implements RoomFeeCollectionRepositoryInterface
{
    public function __construct(RoomFeeCollection $model)
    {
        parent::__construct($model);
    }

    public function getUnpaidFees()
    {
        return $this->model
            ->whereRaw('total_paid < total_price')
            ->get();
    }

    public function getFeesByRoom(int $roomId)
    {
        return $this->model
            ->where('apartment_room_id', $roomId)
            ->orderBy('charge_date', 'desc')
            ->get();
    }

    public function getUnpaidFeesByMonth($month, $year)
    {
        return $this->model
            ->whereRaw('total_paid < total_price')
            ->whereYear('charge_date', $year)
            ->whereMonth('charge_date', $month)
            ->get();
    }

    public function getMonthlyFeeStatistics($year)
    {
        return $this->model
            ->select(
                DB::raw('MONTH(charge_date) as month'),
                DB::raw('SUM(total_price) as total_price'),
                DB::raw('SUM(total_paid) as total_paid'),
                DB::raw('SUM(total_price - total_paid) as total_debt')
            )
            ->whereYear('charge_date', $year)
            ->groupBy(DB::raw('MONTH(charge_date)'))
            ->orderBy(DB::raw('MONTH(charge_date)'))
            ->get();
    }
}
