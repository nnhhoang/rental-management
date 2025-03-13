<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\TerminateContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Resources\TenantContractResource;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = [
            'active' => $request->query('active'),
            'tenant_id' => $request->query('tenant_id'),
            'apartment_id' => $request->query('apartment_id'),
            'room_id' => $request->query('room_id'),
        ];

        $contracts = $this->contractService->getFilteredContracts($filters);

        return $this->successResponse(
            TenantContractResource::collection($contracts)
        );
    }

    /**
     * Get a specific contract
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $contract = $this->contractService->getContract($id);

        if (! $contract) {
            return $this->notFoundResponse(trans('messages.contract.not_found'));
        }

        return $this->successResponse(
            new TenantContractResource($contract)
        );
    }

    /**
     * Create a new contract
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();

        $result = $this->contractService->createContract($data);

        if (! $result['success']) {
            return $this->errorResponse(
                $result['message'] ?? trans('messages.contract.active_contract_exists'),
                null,
                400
            );
        }

        $message = isset($result['tenant_created']) && $result['tenant_created']
            ? trans('messages.contract.created_with_tenant_successfully')
            : trans('messages.contract.created_successfully');

        return $this->successResponse(
            new TenantContractResource($result['contract']),
            $message,
            201
        );
    }

    /**
     * Update an existing contract
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateContractRequest $request, $id)
    {
        $data = $request->validated();

        $contract = $this->contractService->updateContract($id, $data);

        if (! $contract) {
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function terminate(TerminateContractRequest $request, $id)
    {
        $endDate = $request->input('end_date', now());

        $contract = $this->contractService->terminateContract($id, $endDate);

        if (! $contract) {
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
     * @param  int  $id
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

        if (! $result) {
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
     * @param  int  $roomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeByRoom($roomId)
    {
        $contract = $this->contractService->getActiveContractByRoom($roomId);

        if (! $contract) {
            return $this->notFoundResponse(trans('messages.contract.no_active_contract'));
        }

        return $this->successResponse(
            new TenantContractResource($contract)
        );
    }
}
