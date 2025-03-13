<?php

namespace App\Repositories\Contracts;

interface ApartmentRepositoryInterface extends BaseRepositoryInterface
{
    public function searchApartments(string $query, int $perPage = 15);

    public function getByUser(int $userId);
}
