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

    public function getActiveContracts()
    {
        return $this->model
            ->whereNull('end_date')
            ->orWhere('end_date', '>', now())
            ->get();
    }

    public function getActiveContractByRoom(int $roomId)
    {
        return $this->model
            ->where('apartment_room_id', $roomId)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->first();
    }

    public function getContractHistory(int $tenantId)
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}