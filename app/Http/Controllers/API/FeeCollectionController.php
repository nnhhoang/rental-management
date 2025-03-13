<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Fee\RecordPaymentRequest;
use App\Http\Requests\Fee\StoreFeeRequest;
use App\Http\Requests\Fee\UpdateFeeRequest;
use App\Http\Resources\RoomFeeCollectionResource;
use App\Services\FeeCollectionService;

class FeeCollectionController extends BaseController
{
    protected $feeCollectionService;

    public function __construct(FeeCollectionService $feeCollectionService)
    {
        $this->feeCollectionService = $feeCollectionService;
    }

    public function index()
    {
        $fees = $this->feeCollectionService->getAllFeeCollections();

        return $this->successResponse(
            RoomFeeCollectionResource::collection($fees)
        );
    }

    public function show($id)
    {
        $fee = $this->feeCollectionService->getFeeCollection($id);

        if (! $fee) {
            return $this->notFoundResponse(trans('messages.fee.not_found'));
        }

        return $this->successResponse(
            new RoomFeeCollectionResource($fee)
        );
    }

    public function store(StoreFeeRequest $request)
    {
        $data = $request->validated();

        $result = $this->feeCollectionService->createFeeCollection($data);

        if (! $result['success']) {
            return $this->errorResponse(
                $result['message'] ?? trans('messages.contract.no_active_contract'),
                null,
                400
            );
        }

        return $this->successResponse(
            new RoomFeeCollectionResource($result['feeCollection']),
            trans('messages.fee.created_successfully'),
            201
        );
    }

    public function update(UpdateFeeRequest $request, $id)
    {
        $data = $request->validated();

        $fee = $this->feeCollectionService->updateFeeCollection($id, $data);

        if (! $fee) {
            return $this->notFoundResponse(trans('messages.fee.not_found'));
        }

        return $this->successResponse(
            new RoomFeeCollectionResource($fee),
            trans('messages.fee.updated_successfully')
        );
    }

    public function recordPayment(RecordPaymentRequest $request, $id)
    {
        $amount = $request->input('amount');

        $fee = $this->feeCollectionService->recordPayment($id, $amount);

        if (! $fee) {
            return $this->notFoundResponse(trans('messages.fee.not_found'));
        }

        return $this->successResponse(
            new RoomFeeCollectionResource($fee),
            trans('messages.fee.payment_recorded')
        );
    }

    public function destroy($id)
    {
        $result = $this->feeCollectionService->deleteFeeCollection($id);

        if (! $result) {
            return $this->notFoundResponse(trans('messages.fee.not_found'));
        }

        return $this->successResponse(
            null,
            trans('messages.fee.deleted_successfully')
        );
    }

    public function byRoom($roomId)
    {
        $fees = $this->feeCollectionService->getFeesByRoom($roomId);

        return $this->successResponse(
            RoomFeeCollectionResource::collection($fees)
        );
    }

    public function unpaid()
    {
        $fees = $this->feeCollectionService->getUnpaidFees();

        return $this->successResponse(
            RoomFeeCollectionResource::collection($fees)
        );
    }
}
