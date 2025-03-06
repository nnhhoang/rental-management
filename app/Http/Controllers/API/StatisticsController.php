<?php
// app/Http/Controllers/API/StatisticsController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomFeeCollectionResource;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
        $this->middleware('auth:sanctum');
    }

    public function unpaidRooms()
    {
        $unpaidRooms = $this->statisticsService->getUnpaidRoomsForPreviousMonth();
        
        return RoomFeeCollectionResource::collection($unpaidRooms);
    }

    public function monthlyFeeStatistics(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $statistics = $this->statisticsService->getMonthlyFeeStatistics($year);
        
        return response()->json([
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
        
        return response()->json($data);
    }
}