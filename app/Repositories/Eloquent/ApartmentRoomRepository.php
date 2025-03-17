<?php

namespace App\Repositories\Eloquent;

use App\Models\ApartmentRoom;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;

class ApartmentRoomRepository extends BaseRepository implements ApartmentRoomRepositoryInterface
{
    public function __construct(ApartmentRoom $model)
    {
        parent::__construct($model);
    }

    public function searchRooms(array $criteria, int $perPage = 15)
    {
        $query = $this->model->newQuery();

        if (isset($criteria['apartment_id'])) {
            $query->where('apartment_id', $criteria['apartment_id']);
        }

        if (isset($criteria['room_number'])) {
            $query->where('room_number', 'like', "%{$criteria['room_number']}%");
        }

        return $query->with('apartment')->paginate($perPage);
    }

    public function getByApartment(int $apartmentId)
    {
        return $this->model->where('apartment_id', $apartmentId)->get();
    }

    public function findRoomsWithActiveContract()
    {
        return $this->model->whereHas('contracts', function ($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>', now());
        })->get();
    }

    public function findRoomsWithoutTenant()
    {
        return $this->model->whereDoesntHave('contracts', function ($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>', now());
        })->get();
    }
    public function findRoomsWithoutTenantByApartment(int $apartmentId)
    {
        return $this->model
            ->where('apartment_id', $apartmentId)
            ->whereDoesntHave('contracts', function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->get();
    }
}
