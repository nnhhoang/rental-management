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

    /**
     * Get unpaid fees
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnpaidFees()
    {
        return $this->model
            ->whereRaw('total_paid < total_price')
            ->get();
    }

    /**
     * Get fees for a specific room
     * 
     * @param int $roomId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeesByRoom(int $roomId)
    {
        return $this->model
            ->where('apartment_room_id', $roomId)
            ->orderBy('charge_date', 'desc')
            ->get();
    }

    /**
     * Get unpaid fees for a specific month
     * 
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnpaidFeesByMonth($month, $year)
    {
        return $this->model
            ->whereRaw('total_paid < total_price')
            ->whereYear('charge_date', $year)
            ->whereMonth('charge_date', $month)
            ->get();
    }

    /**
     * Get monthly fee statistics for a given year
     * 
     * @param int $year
     * @return \Illuminate\Support\Collection
     */
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
    
    /**
     * Check if a contract has any fee collections
     * 
     * @param int $contractId
     * @return bool
     */
    public function contractHasFeeCollections(int $contractId)
    {
        return $this->model
            ->where('tenant_contract_id', $contractId)
            ->exists();
    }
}