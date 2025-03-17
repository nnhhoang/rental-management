<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\TerminateContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Resources\TenantContractResource;
use App\Models\TenantContract;
use App\Services\ContractService;
use App\Services\TenantService;
use Illuminate\Http\Request;

class ContractController extends BaseController
{
    protected $contractService;
    protected $tenantService;

    public function __construct(
        ContractService $contractService,
        TenantService $tenantService
    ) {
        $this->contractService = $contractService;
        $this->tenantService = $tenantService;
    }

    /**
     * Get all contracts with filtering options
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = [
            'active' => $request->query('active'),
            'tenant_id' => $request->query('tenant_id'),
            'apartment_id' => $request->query('apartment_id'),
            'room_id' => $request->query('room_id')
        ];

        $contracts = $this->contractService->getFilteredContracts($filters);

        return $this->successResponse(
            TenantContractResource::collection($contracts)
        );
    }

    /**
     * Get a specific contract
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $contract = $this->contractService->getContract($id);

        if (!$contract) {
            return $this->notFoundResponse(trans('messages.contract.not_found'));
        }

        return $this->successResponse(
            new TenantContractResource($contract)
        );
    }

    /**
     * Create a new contract
     * 
     * @param StoreContractRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();

        if (isset($data['start_date'])) {
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        }

        $numericFields = [
            'pay_period',
            'price',
            'electricity_pay_type',
            'electricity_price',
            'electricity_number_start',
            'water_pay_type',
            'water_price',
            'water_number_start',
            'number_of_tenant_current'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = is_numeric($data[$field]) ?
                    (strpos($data[$field], '.') !== false ?
                        (float)$data[$field] : (int)$data[$field]) :
                    $data[$field];
            }
        }

        $result = $this->contractService->createContract($data);

        if (!$result['success']) {
            return $this->errorResponse(
                $result['message'] ?? trans('messages.contract.no_active_contract'),
                null,
                400
            );
        }

        return $this->successResponse(
            new TenantContractResource($result['contract']),
            trans('messages.contract.created_successfully'),
            201
        );
    }

    /**
     * Update an existing contract
     * 
     * @param UpdateContractRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateContractRequest $request, TenantContract $tenantContract)
    {

        $data = $request->validated();

        if ($tenantContract->end_date < now()) {
            return $this->errorResponse(
                'Contract has been terminated',
                400
            );
        }
        $contract = $this->contractService->updateContract($tenantContract->id, $data);

        if (!$contract) {
            return $this->notFoundResponse(trans('messages.contract.not_found'));
        }

        return $this->successResponse(
            new TenantContractResource($contract),
            trans('messages.contract.updated_successfully')
        );
    }

    /**
     * Terminate a contract
     * 
     * @param TerminateContractRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function terminate(TerminateContractRequest $request, $id)
    {
        $endDate = $request->input('end_date', now());

        $contract = $this->contractService->terminateContract($id, $endDate);

        if (!$contract) {
            return $this->notFoundResponse(trans('messages.contract.not_found'));
        }

        return $this->successResponse(
            new TenantContractResource($contract),
            trans('messages.contract.terminated_successfully')
        );
    }

    /**
     * Delete a contract
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Check if contract has fee collections before deleting
        if ($this->contractService->contractHasFeeCollections($id)) {
            return $this->errorResponse(
                trans('messages.contract.has_fee_collections'),
                null,
                422
            );
        }

        $result = $this->contractService->deleteContract($id);

        if (!$result) {
            return $this->notFoundResponse(trans('messages.contract.not_found'));
        }

        return $this->successResponse(
            null,
            trans('messages.contract.deleted_successfully')
        );
    }

    /**
     * Get active contract for a specific room
     * 
     * @param int $roomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeByRoom($roomId)
    {
        $contract = $this->contractService->getActiveContractByRoom($roomId);

        if (!$contract) {
            return $this->notFoundResponse(trans('messages.contract.no_active_contract'));
        }

        return $this->successResponse(
            new TenantContractResource($contract)
        );
    }
}
