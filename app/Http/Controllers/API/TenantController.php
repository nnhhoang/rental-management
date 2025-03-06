<?php
// app/Http/Controllers/API/TenantController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        
        if ($search) {
            $tenants = $this->tenantService->searchTenants($search);
        } else {
            $tenants = $this->tenantService->getAllTenants();
        }
        
        return TenantResource::collection($tenants);
    }

    public function show($id)
    {
        $tenant = $this->tenantService->getTenant($id);
        
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }
        
        return new TenantResource($tenant);
    }

    public function store(StoreTenantRequest $request)
    {
        $data = $request->validated();
        
        $tenant = $this->tenantService->createTenant($data);
        
        return new TenantResource($tenant);
    }

    public function update(UpdateTenantRequest $request, $id)
    {
        $data = $request->validated();
        
        $tenant = $this->tenantService->updateTenant($id, $data);
        
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }
        
        return new TenantResource($tenant);
    }

    public function destroy($id)
    {
        $deleted = $this->tenantService->deleteTenant($id);
        
        if (!$deleted) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }
        
        return response()->json(['message' => 'Tenant deleted successfully']);
    }

    public function userTenants()
    {
        $tenants = $this->tenantService->getTenantsByUser(auth()->id());
        
        return TenantResource::collection($tenants);
    }
}