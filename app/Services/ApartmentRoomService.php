<?php

namespace App\Services;

use App\Events\RoomCreated;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApartmentRoomService
{
    protected $roomRepository;

    public function __construct(ApartmentRoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function getAllRooms(int $perPage = 15)
    {
        return $this->roomRepository->paginate($perPage);
    }

    public function searchRooms(array $criteria, int $perPage = 15)
    {
        return $this->roomRepository->searchRooms($criteria, $perPage);
    }

    public function getRoom(int $id)
    {
        return $this->roomRepository->find($id);
    }

    public function getRoomsByApartment(int $apartmentId)
    {
        return $this->roomRepository->getByApartment($apartmentId);
    }

    public function createRoom(array $data)
    {
        try {
            DB::beginTransaction();

            $imagePath = null;

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $this->uploadImage($data['image']);
                $data['image'] = $imagePath;
            }

            $room = $this->roomRepository->create($data);

            // Dispatch room created event
            event(new RoomCreated($room));

            DB::commit();
            return $room;
        } catch (\Exception $e) {
            DB::rollBack();
            // If file was uploaded, delete it
            if (isset($imagePath) && $imagePath) {
                Storage::delete($imagePath);
            }
            throw $e;
        }
    }

    public function updateRoom(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $room = $this->roomRepository->find($id);

            if (!$room) {
                return false;
            }

            $oldImage = $room->image;
            $newImage = null;

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Upload new image
                $newImage = $this->uploadImage($data['image']);
                $data['image'] = $newImage;
            }

            $result = $this->roomRepository->update($id, $data);

            // Delete old image if exists and a new one was uploaded
            if ($oldImage && $newImage) {
                Storage::delete($oldImage);
            }

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            // If a new file was uploaded, delete it
            if (isset($newImage) && $newImage) {
                Storage::delete($newImage);
            }
            throw $e;
        }
    }

    public function deleteRoom(int $id)
    {
        try {
            DB::beginTransaction();

            $room = $this->roomRepository->find($id);

            if (!$room) {
                return false;
            }

            $image = $room->image;

            $result = $this->roomRepository->delete($id);

            // Delete image if exists
            if ($image) {
                Storage::delete($image);
            }

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getRoomsWithActiveContract()
    {
        return $this->roomRepository->findRoomsWithActiveContract();
    }

    /**
     * Get all rooms, regardless of tenant status
     * (Previously returned only rooms without tenants, but now we allow multiple contracts per room)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoomsWithoutTenant()
    {
        return $this->roomRepository->findRoomsWithoutTenant();
    }

    /**
     * Get all rooms in an apartment, regardless of tenant status
     * (Previously returned only rooms without tenants, but now we allow multiple contracts per room)
     * 
     * @param int $apartmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoomsWithoutTenantByApartment(int $apartmentId)
    {
        return $this->roomRepository->findRoomsWithoutTenantByApartment($apartmentId);
    }

    private function uploadImage(UploadedFile $file)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('rooms', $filename, 'public');
        return $path;
    }
}
