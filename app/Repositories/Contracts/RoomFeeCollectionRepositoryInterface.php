<?php

namespace App\Repositories\Contracts;

interface RoomFeeCollectionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get unpaid fees
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnpaidFees();

    /**
     * Get fees for a specific room
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeesByRoom(int $roomId);

    /**
     * Get unpaid fees for a specific month
     *
     * @param  int  $month
     * @param  int  $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnpaidFeesByMonth($month, $year);

    /**
     * Get monthly fee statistics for a given year
     *
     * @param  int  $year
     * @return \Illuminate\Support\Collection
     */
    public function getMonthlyFeeStatistics($year);

    /**
     * Check if a contract has any fee collections
     *
     * @return bool
     */
    public function contractHasFeeCollections(int $contractId);
}
