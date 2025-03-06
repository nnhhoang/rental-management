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

    public function searchTenants(string $query)
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('tel', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }

    public function getTenantsByUser(int $userId)
    {
        return $this->model->whereHas('contracts.room.apartment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }
}