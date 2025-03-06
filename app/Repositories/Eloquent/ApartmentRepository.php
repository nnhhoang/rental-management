<?php
namespace App\Repositories\Eloquent;

use App\Models\Apartment;
use App\Repositories\Contracts\ApartmentRepositoryInterface;

class ApartmentRepository extends BaseRepository implements ApartmentRepositoryInterface
{
    public function __construct(Apartment $model)
    {
        parent::__construct($model);
    }

    public function searchApartments(string $query, int $perPage = 15)
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('address', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    public function getByUser(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}