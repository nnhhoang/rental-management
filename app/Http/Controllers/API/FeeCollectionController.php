<?php
// app/Http/Controllers/API/FeeCollectionController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fee\RecordPaymentRequest;
use App\Http\Requests\Fee\StoreFeeRequest;
use App\Http\Requests\Fee\UpdateFeeRequest;
use App\Http\Resources\RoomFeeCollectionResource;
use App\Services\FeeCollectionService;

class FeeCollectionController extends Controller
{
    protected $feeCollectionService;

    public function __construct(FeeCollectionService $feeCollectionService)
    {
        $this->feeCollectionService = $feeCollectionService;
    }

    public function index()
    {
        $fees = $this->feeCollectionService->getAllFeeCollections();
        
        return RoomFeeCollectionResource::collection($fees);
    }

    public function show($id)
    {
        $fee = $this->feeCollectionService->getFeeCollection($id);
        
        return new RoomFeeCollectionResource($fee);
    }

    public function store(StoreFeeRequest $request)
    {
        $data = $request->validated();
        
        $result = $this->feeCollectionService->createFeeCollection($data);
        
        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }
        
        return new RoomFeeCollectionResource($result['feeCollection']);
    }

    public function update(UpdateFeeRequest $request, $id)
    {
        $data = $request->validated();
        
        $fee = $this->feeCollectionService->updateFeeCollection($id, $data);
        
        return new RoomFeeCollectionResource($fee);
    }

    public function recordPayment(RecordPaymentRequest $request, $id)
    {
        $amount = $request->input('amount');
        
        $fee = $this->feeCollectionService->recordPayment($id, $amount);
        
        return new RoomFeeCollectionResource($fee);
    }

    public function destroy($id)
    {
        $this->feeCollectionService->deleteFeeCollection($id);
        
        return response()->json(['message' => 'Fee collection deleted successfully']);
    }

    public function byRoom($roomId)
    {
        $fees = $this->feeCollectionService->getFeesByRoom($roomId);
        
        return RoomFeeCollectionResource::collection($fees);
    }

    public function unpaid()
    {
        $fees = $this->feeCollectionService->getUnpaidFees();
        
        return RoomFeeCollectionResource::collection($fees);
    }
}