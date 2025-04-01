<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\TerminateContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Resources\TenantContractResource;
use App\Models\TenantContract;
use App\Models\ApartmentRoom;
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

    public function show(TenantContract $tenantContract)
    {
        return $this->successResponse(
            new TenantContractResource($tenantContract)
        );
    }

    public function store(StoreContractRequest $request)
    {
        $data = $this->prepareContractData($request->validated());

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

    public function update(UpdateContractRequest $request, TenantContract $tenantContract)
    {
        if ($tenantContract->end_date < now()) {
            return $this->errorResponse('Contract has been terminated', 400);
        }

        $contract = $this->contractService->updateContract(
            $tenantContract,
            $request->validated()
        );

        return $this->successResponse(
            new TenantContractResource($contract),
            trans('messages.contract.updated_successfully')
        );
    }

    public function terminate(TerminateContractRequest $request, TenantContract $tenantContract)
    {
        $contract = $this->contractService->terminateContract(
            $tenantContract->id,
            $request->input('end_date', now())
        );

        return $this->successResponse(
            new TenantContractResource($contract),
            trans('messages.contract.terminated_successfully')
        );
    }

    public function destroy(TenantContract $tenantContract)
    {
        if ($this->contractService->contractHasFeeCollections($tenantContract->id)) {
            return $this->errorResponse(
                trans('messages.contract.has_fee_collections'),
                null,
                422
            );
        }

        $this->contractService->deleteContract($tenantContract->id);

        return $this->successResponse(
            null,
            trans('messages.contract.deleted_successfully')
        );
    }

    public function activeByRoom(ApartmentRoom $room)
    {
        $contract = $this->contractService->getActiveContractByRoom($room->id);

        if (!$contract) {
            return $this->notFoundResponse(trans('messages.contract.no_active_contract'));
        }

        return $this->successResponse(
            new TenantContractResource($contract)
        );
    }

    protected function prepareContractData(array $data): array
    {
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

        return $data;
    }
}
