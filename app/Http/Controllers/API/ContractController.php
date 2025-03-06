<?php
// app/Http/Controllers/API/ContractController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\TerminateContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Resources\TenantContractResource;
use App\Services\ContractService;
use App\Services\TenantService;

class ContractController extends Controller
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

    public function index()
    {
        $contracts = $this->contractService->getAllContracts();
        
        return TenantContractResource::collection($contracts);
    }

    public function show($id)
    {
        $contract = $this->contractService->getContract($id);
        
        return new TenantContractResource($contract);
    }

    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();
        
        $result = $this->contractService->createContract($data);
        
        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }
        
        return new TenantContractResource($result['contract']);
    }

    public function update(UpdateContractRequest $request, $id)
    {
        $data = $request->validated();
        
        $contract = $this->contractService->updateContract($id, $data);
        
        return new TenantContractResource($contract);
    }

    public function terminate(TerminateContractRequest $request, $id)
    {
        $endDate = $request->input('end_date', now());
        
        $contract = $this->contractService->terminateContract($id, $endDate);
        
        return new TenantContractResource($contract);
    }

    public function destroy($id)
    {
        $this->contractService->deleteContract($id);
        
        return response()->json(['message' => 'Contract deleted successfully']);
    }

    public function active()
    {
        $contracts = $this->contractService->getActiveContracts();
        
        return TenantContractResource::collection($contracts);
    }

    public function activeByRoom($roomId)
    {
        $contract = $this->contractService->getActiveContractByRoom($roomId);
        
        if (!$contract) {
            return response()->json(['message' => 'No active contract found for this room'], 404);
        }
        
        return new TenantContractResource($contract);
    }

    public function tenantHistory($tenantId)
    {
        $history = $this->contractService->getContractHistory($tenantId);
        
        return TenantContractResource::collection($history);
    }
}