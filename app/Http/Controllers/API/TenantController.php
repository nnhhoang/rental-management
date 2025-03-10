<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Http\Resources\TenantContractResource;
use App\Services\TenantService;
use App\Services\ContractService;
use Illuminate\Http\Request;

class TenantController extends BaseController
{
    protected $tenantService;
    protected $contractService;

    public function __construct(TenantService $tenantService, ContractService $contractService)
    {
        $this->tenantService = $tenantService;
        $this->contractService = $contractService;
    }

    /**
     * Get all tenants, optionally with their contracts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $withContracts = $request->query('with_contracts', false);
        $search = $request->query('search');
        
        if ($search) {
            $tenants = $this->tenantService->searchTenants($search, $withContracts);
        } else {
            $tenants = $this->tenantService->getAllTenants($withContracts);
        }
        
        return $this->successResponse(
            TenantResource::collection($tenants)
        );
    }

    /**
     * Get a specific tenant, optionally with their contracts
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        $withContracts = $request->query('with_contracts', false);
        
        $tenant = $this->tenantService->getTenant($id, $withContracts);
        
        if (!$tenant) {
            return $this->notFoundResponse(trans('messages.tenant.not_found'));
        }
        
        return $this->successResponse(
            new TenantResource($tenant)
        );
    }

    /**
     * Create a new tenant
     *
     * @param StoreTenantRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTenantRequest $request)
    {
        $data = $request->validated();
        
        $tenant = $this->tenantService->createTenant($data);
        
        return $this->successResponse(
            new TenantResource($tenant),
            trans('messages.tenant.created_successfully'),
            201
        );
    }

    /**
     * Update an existing tenant
     *
     * @param UpdateTenantRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTenantRequest $request, $id)
    {
        $data = $request->validated();
        
        $tenant = $this->tenantService->updateTenant($id, $data);
        
        if (!$tenant) {
            return $this->notFoundResponse(trans('messages.tenant.not_found'));
        }
        
        return $this->successResponse(
            new TenantResource($tenant),
            trans('messages.tenant.updated_successfully')
        );
    }

    /**
     * Delete a tenant
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Check if tenant has active contracts before deleting
        $hasActiveContracts = $this->contractService->tenantHasActiveContracts($id);
        
        if ($hasActiveContracts) {
            return $this->errorResponse(
                trans('messages.tenant.has_active_contracts'),
                null,
                422
            );
        }
        
        $deleted = $this->tenantService->deleteTenant($id);
        
        if (!$deleted) {
            return $this->notFoundResponse(trans('messages.tenant.not_found'));
        }
        
        return $this->successResponse(
            null,
            trans('messages.tenant.deleted_successfully')
        );
    }

    /**
     * Get tenants associated with the current user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTenants(Request $request)
    {
        $withContracts = $request->query('with_contracts', false);
        $tenants = $this->tenantService->getTenantsByUser(auth()->id(), $withContracts);
        
        return $this->successResponse(
            TenantResource::collection($tenants)
        );
    }

    /**
     * Get tenant's contracts
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContracts($id)
    {
        $tenant = $this->tenantService->getTenant($id);
        
        if (!$tenant) {
            return $this->notFoundResponse(trans('messages.tenant.not_found'));
        }
        
        $contracts = $this->contractService->getContractHistory($id);
        
        return $this->successResponse(
            TenantContractResource::collection($contracts)
        );
    }
}