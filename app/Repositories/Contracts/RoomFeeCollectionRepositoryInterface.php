<?php
namespace App\Repositories\Contracts;

interface RoomFeeCollectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getUnpaidFees();
    public function getFeesByRoom(int $roomId);
    public function getUnpaidFeesByMonth($month, $year);
    public function getMonthlyFeeStatistics($year);
}