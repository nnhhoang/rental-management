<?php
namespace App\Services;

use App\Repositories\Contracts\ElectricityUsageRepositoryInterface;
use App\Repositories\Contracts\WaterUsageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UtilityService
{
    protected $electricityRepository;
    protected $waterRepository;

    public function __construct(
        ElectricityUsageRepositoryInterface $electricityRepository,
        WaterUsageRepositoryInterface $waterRepository
    ) {
        $this->electricityRepository = $electricityRepository;
        $this->waterRepository = $waterRepository;
    }

    public function getLatestElectricityUsage(int $roomId)
    {
        return $this->electricityRepository->getLatestByRoom($roomId);
    }

    public function getElectricityUsageByDateRange(int $roomId, $startDate, $endDate)
    {
        return $this->electricityRepository->getByDateRange($roomId, $startDate, $endDate);
    }

    public function createElectricityUsage(array $data)
    {
        try {
            DB::beginTransaction();
            
            $imagePath = null;
            
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $this->uploadImage($data['image'], 'electricity');
                $data['image'] = $imagePath;
            }
            
            $usage = $this->electricityRepository->create($data);
            
            DB::commit();
            return $usage;
        } catch (\Exception $e) {
            DB::rollBack();
            // If file was uploaded, delete it
            if (isset($imagePath) && $imagePath) {
                Storage::delete($imagePath);
            }
            throw $e;
        }
    }

    public function getLatestWaterUsage(int $roomId)
    {
        return $this->waterRepository->getLatestByRoom($roomId);
    }

    public function getWaterUsageByDateRange(int $roomId, $startDate, $endDate)
    {
        return $this->waterRepository->getByDateRange($roomId, $startDate, $endDate);
    }

    public function createWaterUsage(array $data)
    {
        try {
            DB::beginTransaction();
            
            $imagePath = null;
            
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $this->uploadImage($data['image'], 'water');
                $data['image'] = $imagePath;
            }
            
            $usage = $this->waterRepository->create($data);
            
            DB::commit();
            return $usage;
        } catch (\Exception $e) {
            DB::rollBack();
            // If file was uploaded, delete it
            if (isset($imagePath) && $imagePath) {
                Storage::delete($imagePath);
            }
            throw $e;
        }
    }

    private function uploadImage(UploadedFile $file, string $type)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("utilities/{$type}", $filename, 'public');
        return $path;
    }
}
