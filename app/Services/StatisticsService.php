<?php
namespace App\Services;

use App\Repositories\Contracts\RoomFeeCollectionRepositoryInterface;
use App\Repositories\Contracts\ApartmentRepositoryInterface;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;
use App\Repositories\Contracts\TenantContractRepositoryInterface;
use Carbon\Carbon;

class StatisticsService
{
    protected $feeCollectionRepository;
    protected $apartmentRepository;
    protected $roomRepository;
    protected $contractRepository;

    public function __construct(
        RoomFeeCollectionRepositoryInterface $feeCollectionRepository,
        ApartmentRepositoryInterface $apartmentRepository,
        ApartmentRoomRepositoryInterface $roomRepository,
        TenantContractRepositoryInterface $contractRepository
    ) {
        $this->feeCollectionRepository = $feeCollectionRepository;
        $this->apartmentRepository = $apartmentRepository;
        $this->roomRepository = $roomRepository;
        $this->contractRepository = $contractRepository;
    }

    public function getMonthlyFeeStatistics($year)
    {
        return $this->feeCollectionRepository->getMonthlyFeeStatistics($year);
    }

    public function getUnpaidRoomsForPreviousMonth()
    {
        $previousMonth = Carbon::now()->subMonth();
        $month = $previousMonth->month;
        $year = $previousMonth->year;
        
        return $this->feeCollectionRepository->getUnpaidFeesByMonth($month, $year);
    }

    public function getTotalApartments(int $userId = null)
    {
        if ($userId) {
            return $this->apartmentRepository->getByUser($userId)->count();
        }
        
        return $this->apartmentRepository->all()->count();
    }

    public function getTotalRooms(int $userId = null)
    {
        if ($userId) {
            $apartments = $this->apartmentRepository->getByUser($userId);
            $apartmentIds = $apartments->pluck('id')->toArray();
            
            return $this->roomRepository->findWhere(['apartment_id' => $apartmentIds])->count();
        }
        
        return $this->roomRepository->all()->count();
    }

    public function getTotalActiveContracts()
    {
        return $this->contractRepository->getActiveContracts()->count();
    }

    public function getOccupancyRate()
    {
        $totalRooms = $this->roomRepository->all()->count();
        $occupiedRooms = $this->roomRepository->findRoomsWithActiveContract()->count();
        
        if ($totalRooms === 0) {
            return 0;
        }
        
        return ($occupiedRooms / $totalRooms) * 100;
    }

    public function getIncomeStatistics($year)
    {
        $monthlyData = $this->feeCollectionRepository->getMonthlyFeeStatistics($year);
        
        $totalRevenue = $monthlyData->sum('total_price');
        $totalCollected = $monthlyData->sum('total_paid');
        $totalDebt = $monthlyData->sum('total_debt');
        
        return [
            'totalRevenue' => $totalRevenue,
            'totalCollected' => $totalCollected,
            'totalDebt' => $totalDebt,
            'collectionRate' => $totalRevenue > 0 ? ($totalCollected / $totalRevenue) * 100 : 0,
            'monthlyData' => $monthlyData
        ];
    }
}