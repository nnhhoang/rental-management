<?php
namespace App\Services;

use App\Events\RoomCreated;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $imagePath = null;
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $imagePath = $this->uploadImage($data['image']);
            $data['image'] = $imagePath;
        }
        
        $room = $this->roomRepository->create($data);
        
        // Dispatch room created event
        event(new RoomCreated($room));
        
        return $room;
    }

    public function updateRoom(int $id, array $data)
    {
        $room = $this->roomRepository->find($id);
        
        if (!$room) {
            return false;
        }
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            if ($room->image) {
                Storage::delete($room->image);
            }
            
            // Upload new image
            $imagePath = $this->uploadImage($data['image']);
            $data['image'] = $imagePath;
        }
        
        return $this->roomRepository->update($id, $data);
    }

    public function deleteRoom(int $id)
    {
        $room = $this->roomRepository->find($id);
        
        if (!$room) {
            return false;
        }
        
        // Delete image if exists
        if ($room->image) {
            Storage::delete($room->image);
        }
        
        return $this->roomRepository->delete($id);
    }

    public function getRoomsWithActiveContract()
    {
        return $this->roomRepository->findRoomsWithActiveContract();
    }

    public function getRoomsWithoutTenant()
    {
        return $this->roomRepository->findRoomsWithoutTenant();
    }

    private function uploadImage(UploadedFile $file)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('rooms', $filename, 'public');
        return $path;
    }
}
