<?php
namespace App\Repositories\Eloquent;

use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all tenants with their contracts
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithContracts()
    {
        return $this->model->with('contracts')->get();
    }

    /**
     * Find a tenant by ID with their contracts
     * 
     * @param int $id
     * @return \App\Models\Tenant|null
     */
    public function findWithContracts($id)
    {
        return $this->model->with('contracts')->find($id);
    }

    /**
     * Search tenants by query string
     * 
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchTenants(string $query)
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('tel', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }
    
    /**
     * Search tenants by query string and include their contracts
     * 
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchWithContracts(string $query)
    {
        return $this->model
            ->with('contracts')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('tel', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }

    /**
     * Get tenants associated with a user
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTenantsByUser(int $userId)
    {
        return $this->model->whereHas('contracts.room.apartment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }
    
    /**
     * Get tenants associated with a user, including their contracts
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTenantsByUserWithContracts(int $userId)
    {
        return $this->model
            ->with('contracts')
            ->whereHas('contracts.room.apartment', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }
}