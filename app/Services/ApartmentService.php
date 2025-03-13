<?php
namespace App\Services;

use App\Events\ApartmentCreated;
use App\Repositories\Contracts\ApartmentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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
        try {
            DB::beginTransaction();
            
            $imagePath = null;
            
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $this->uploadImage($data['image']);
                $data['image'] = $imagePath;
            }
            
            $apartment = $this->apartmentRepository->create($data);
            
            // Dispatch apartment created event
            event(new ApartmentCreated($apartment));
            
            DB::commit();
            return $apartment;
        } catch (\Exception $e) {
            DB::rollBack();
            // If file was uploaded, delete it
            if (isset($imagePath) && $imagePath) {
                Storage::delete($imagePath);
            }
            throw $e;
        }
    }

    public function updateApartment(int $id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $apartment = $this->apartmentRepository->find($id);
            
            if (!$apartment) {
                return false;
            }
            
            $oldImage = $apartment->image;
            $newImage = null;
            
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Upload new image
                $newImage = $this->uploadImage($data['image']);
                $data['image'] = $newImage;
            }
            
            $result = $this->apartmentRepository->update($id, $data);
            
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
    
    public function deleteApartment(int $id)
    {
        try {
            DB::beginTransaction();
            
            $apartment = $this->apartmentRepository->find($id);
            
            if (!$apartment) {
                return false;
            }
            
            $image = $apartment->image;
            
            $result = $this->apartmentRepository->delete($id);
            
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
    private function uploadImage(UploadedFile $file)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('apartments', $filename, 'public');
        return $path;
    }
}