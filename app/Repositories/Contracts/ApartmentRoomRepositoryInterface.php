<?php
namespace App\Repositories\Contracts;

interface ApartmentRoomRepositoryInterface extends BaseRepositoryInterface
{
    public function searchRooms(array $criteria, int $perPage = 15);
    public function getByApartment(int $apartmentId);
    public function findRoomsWithActiveContract();
    public function findRoomsWithoutTenant();
}