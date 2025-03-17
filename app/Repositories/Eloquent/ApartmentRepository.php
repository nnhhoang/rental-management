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

    public function searchApartments(string $query, int $perPage = 15, ?int $userId = null)
    {
        $queryBuilder = $this->model->newQuery()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            });

        if ($userId) {
            $queryBuilder->where('user_id', $userId);
        }
        
        return $queryBuilder->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = null)
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($perPage) {
            return $query->paginate($perPage);
        }
        
        return $query->get();
    }
}