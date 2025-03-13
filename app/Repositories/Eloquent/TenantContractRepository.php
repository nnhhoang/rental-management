<?php

namespace App\Repositories\Eloquent;

use App\Models\TenantContract;
use App\Repositories\Contracts\TenantContractRepositoryInterface;

class TenantContractRepository extends BaseRepository implements TenantContractRepositoryInterface
{
    public function __construct(TenantContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active contracts (not terminated or end date is in the future)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveContracts()
    {
        return $this->model
            ->with(['room.apartment', 'tenant'])
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active contract for a specific room
     *
     * @return \App\Models\TenantContract|null
     */
    public function getActiveContractByRoom(int $roomId)
    {
        return $this->model
            ->with(['tenant'])
            ->where('apartment_room_id', $roomId)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->first();
    }

    /**
     * Get all contracts for a specific tenant
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractHistory(int $tenantId)
    {
        return $this->model
            ->with(['room.apartment'])
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if tenant has any active contracts
     *
     * @return bool
     */
    public function tenantHasActiveContracts(int $tenantId)
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->exists();
    }

    /**
     * Get all contracts for rooms in a specific apartment
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractsByApartment(int $apartmentId)
    {
        return $this->model
            ->with(['room', 'tenant'])
            ->whereHas('room', function ($query) use ($apartmentId) {
                $query->where('apartment_id', $apartmentId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all contracts for a specific room
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContractsByRoom(int $roomId)
    {
        return $this->model
            ->with(['tenant'])
            ->where('apartment_room_id', $roomId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
