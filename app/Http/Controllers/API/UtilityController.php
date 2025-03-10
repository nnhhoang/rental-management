<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Utility\GetUsageByDateRangeRequest;
use App\Http\Requests\Utility\StoreElectricityUsageRequest;
use App\Http\Requests\Utility\StoreWaterUsageRequest;
use App\Http\Resources\ElectricityUsageResource;
use App\Http\Resources\WaterUsageResource;
use App\Services\UtilityService;

class UtilityController extends BaseController
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
            return $this->notFoundResponse(trans('messages.utility.not_found'));
        }
        
        return $this->successResponse(
            new ElectricityUsageResource($usage)
        );
    }

    public function getElectricityByDateRange(GetUsageByDateRangeRequest $request, $roomId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $usages = $this->utilityService->getElectricityUsageByDateRange($roomId, $startDate, $endDate);
        
        return $this->successResponse(
            ElectricityUsageResource::collection($usages)
        );
    }

    public function createElectricityUsage(StoreElectricityUsageRequest $request)
    {
        $data = $request->validated();
        
        $usage = $this->utilityService->createElectricityUsage($data);
        
        return $this->successResponse(
            new ElectricityUsageResource($usage),
            trans('messages.utility.created_successfully'),
            201
        );
    }

    public function getLatestWater($roomId)
    {
        $usage = $this->utilityService->getLatestWaterUsage($roomId);
        
        if (!$usage) {
            return $this->notFoundResponse(trans('messages.utility.not_found'));
        }
        
        return $this->successResponse(
            new WaterUsageResource($usage)
        );
    }

    public function getWaterByDateRange(GetUsageByDateRangeRequest $request, $roomId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $usages = $this->utilityService->getWaterUsageByDateRange($roomId, $startDate, $endDate);
        
        return $this->successResponse(
            WaterUsageResource::collection($usages)
        );
    }

    public function createWaterUsage(StoreWaterUsageRequest $request)
    {
        $data = $request->validated();
        
        $usage = $this->utilityService->createWaterUsage($data);
        
        return $this->successResponse(
            new WaterUsageResource($usage),
            trans('messages.utility.created_successfully'),
            201
        );
    }
}