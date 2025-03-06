<?php
namespace App\Services;

use App\Events\ApartmentCreated;
use App\Repositories\Contracts\ApartmentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApartmentService
{
    protected $apartmentRepository;

    public function __construct(ApartmentRepositoryInterface $apartmentRepository)
    {
        $this->apartmentRepository = $apartmentRepository;
    }

    public function getAllApartments(int $perPage = 15)
    {
        return $this->apartmentRepository->paginate($perPage);
    }

    public function getUserApartments(int $userId)
    {
        return $this->apartmentRepository->getByUser($userId);
    }

    public function searchApartments(string $query, int $perPage = 15)
    {
        return $this->apartmentRepository->searchApartments($query, $perPage);
    }

    public function getApartment(int $id)
    {
        return $this->apartmentRepository->find($id);
    }

    public function createApartment(array $data)
    {
        $imagePath = null;
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $imagePath = $this->uploadImage($data['image']);
            $data['image'] = $imagePath;
        }
        
        $apartment = $this->apartmentRepository->create($data);
        
        // Dispatch apartment created event
        event(new ApartmentCreated($apartment));
        
        return $apartment;
    }

    public function updateApartment(int $id, array $data)
    {
        $apartment = $this->apartmentRepository->find($id);
        
        if (!$apartment) {
            return false;
        }
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            if ($apartment->image) {
                Storage::delete($apartment->image);
            }
            
            // Upload new image
            $imagePath = $this->uploadImage($data['image']);
            $data['image'] = $imagePath;
        }
        
        return $this->apartmentRepository->update($id, $data);
    }

    public function deleteApartment(int $id)
    {
        $apartment = $this->apartmentRepository->find($id);
        
        if (!$apartment) {
            return false;
        }
        
        // Delete image if exists
        if ($apartment->image) {
            Storage::delete($apartment->image);
        }
        
        return $this->apartmentRepository->delete($id);
    }

    private function uploadImage(UploadedFile $file)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('apartments', $filename, 'public');
        return $path;
    }
}