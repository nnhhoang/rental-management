<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\RoomFeeCollectionResource;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends BaseController
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function unpaidRooms()
    {
        $unpaidRooms = $this->statisticsService->getUnpaidRoomsForPreviousMonth();
        
        return $this->successResponse(
            RoomFeeCollectionResource::collection($unpaidRooms)
        );
    }

    public function monthlyFeeStatistics(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $statistics = $this->statisticsService->getMonthlyFeeStatistics($year);
        
        return $this->successResponse([
            'data' => $statistics,
            'year' => $year
        ]);
    }

    public function dashboard()
    {
        $userId = auth()->id();
        
        $data = [
            'total_apartments' => $this->statisticsService->getTotalApartments($userId),
            'total_rooms' => $this->statisticsService->getTotalRooms($userId),
            'total_active_contracts' => $this->statisticsService->getTotalActiveContracts(),
            'occupancy_rate' => $this->statisticsService->getOccupancyRate(),
            'income_statistics' => $this->statisticsService->getIncomeStatistics(date('Y')),
        ];
        
        return $this->successResponse($data);
    }
}