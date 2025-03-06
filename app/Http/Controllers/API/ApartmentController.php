<?php
// app/Http/Controllers/API/ApartmentController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Services\ApartmentService;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    protected $apartmentService;

    public function __construct(ApartmentService $apartmentService)
    {
        $this->apartmentService = $apartmentService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        
        if ($search) {
            $apartments = $this->apartmentService->searchApartments($search, $perPage);
        } else {
            $apartments = $this->apartmentService->getAllApartments($perPage);
        }
        
        return ApartmentResource::collection($apartments);
    }

    public function show($id)
    {
        $apartment = $this->apartmentService->getApartment($id);
        
        return new ApartmentResource($apartment);
    }

    public function store(StoreApartmentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        $apartment = $this->apartmentService->createApartment($data);
        
        return new ApartmentResource($apartment);
    }

    public function update(UpdateApartmentRequest $request, $id)
    {
        $data = $request->validated();
        
        $apartment = $this->apartmentService->updateApartment($id, $data);
        
        return new ApartmentResource($apartment);
    }

    public function destroy($id)
    {
        $this->apartmentService->deleteApartment($id);
        
        return response()->json([
            'message' => 'Apartment deleted successfully'
        ]);
    }

    public function userApartments()
    {
        $apartments = $this->apartmentService->getUserApartments(auth()->id());
        
        return ApartmentResource::collection($apartments);
    }
}