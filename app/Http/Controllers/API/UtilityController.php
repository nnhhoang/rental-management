<?php
// app/Http/Controllers/API/UtilityController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\GetUsageByDateRangeRequest;
use App\Http\Requests\Utility\StoreElectricityUsageRequest;
use App\Http\Requests\Utility\StoreWaterUsageRequest;
use App\Http\Resources\ElectricityUsageResource;
use App\Http\Resources\WaterUsageResource;
use App\Services\UtilityService;

class UtilityController extends Controller
{
    protected $utilityService;

    public function __construct(UtilityService $utilityService)
    {
        $this->utilityService = $utilityService;
    }

    public function getLatestElectricity($roomId)
    {
        $usage = $this->utilityService->getLatestElectricityUsage($roomId);
        
        if (!$usage) {
            return response()->json(['message' => 'No electricity usage found for this room'], 404);
        }
        
        return new ElectricityUsageResource($usage);
    }

    public function getElectricityByDateRange(GetUsageByDateRangeRequest $request, $roomId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $usages = $this->utilityService->getElectricityUsageByDateRange($roomId, $startDate, $endDate);
        
        return ElectricityUsageResource::collection($usages);
    }

    public function createElectricityUsage(StoreElectricityUsageRequest $request)
    {
        $data = $request->validated();
        
        $usage = $this->utilityService->createElectricityUsage($data);
        
        return new ElectricityUsageResource($usage);
    }

    public function getLatestWater($roomId)
    {
        $usage = $this->utilityService->getLatestWaterUsage($roomId);
        
        if (!$usage) {
            return response()->json(['message' => 'No water usage found for this room'], 404);
        }
        
        return new WaterUsageResource($usage);
    }

    public function getWaterByDateRange(GetUsageByDateRangeRequest $request, $roomId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $usages = $this->utilityService->getWaterUsageByDateRange($roomId, $startDate, $endDate);
        
        return WaterUsageResource::collection($usages);
    }

    public function createWaterUsage(StoreWaterUsageRequest $request)
    {
        $data = $request->validated();
        
        $usage = $this->utilityService->createWaterUsage($data);
        
        return new WaterUsageResource($usage);
    }
}