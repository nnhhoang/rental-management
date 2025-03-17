<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Services\ApartmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ApartmentController extends Controller
{
    protected $apartmentService;

    public function __construct(ApartmentService $apartmentService)
    {
        $this->apartmentService = $apartmentService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Apartment::class);

        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        $userId = auth()->id();
        
        if ($search) {
            $apartments = $this->apartmentService->searchApartments($search, $perPage, $userId);
        } else {
            $apartments = $this->apartmentService->getUserApartments($userId, $perPage);
        }
        
        return ApartmentResource::collection($apartments);
    }

    public function show($id)
    {
        $apartment = $this->apartmentService->getApartment($id);

        if (!$apartment) {
            return $this->notFoundResponse(trans('messages.apartment.not_found'));
        }

        $this->authorize('view', $apartment);
        
        return $this->successResponse(
            new ApartmentResource($apartment)
        );
    }

    public function store(StoreApartmentRequest $request)
    {
        $this->authorize('create', Apartment::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        $apartment = $this->apartmentService->createApartment($data);
        
        return response()->json([
            'message' => trans('messages.apartment.created_successfully'),
            'data' => new ApartmentResource($apartment)
        ], 201);
    }

    public function update(UpdateApartmentRequest $request, $id)
    {
        $apartment = $this->apartmentService->getApartment($id);

        if (!$apartment) {
            return $this->notFoundResponse(trans('messages.apartment.not_found'));
        }

        $this->authorize('update', $apartment);

        $data = $request->validated();
        
        $apartment = $this->apartmentService->updateApartment($id, $data);
        
        return response()->json([
            'message' => trans('messages.apartment.updated_successfully'),
            'data' => new ApartmentResource($apartment)
        ]);
    }

    public function destroy($id)
    {
        $apartment = $this->apartmentService->getApartment($id);
    
        if (!$apartment) {
            return $this->notFoundResponse(trans('messages.apartment.not_found'));
        }

        $this->authorize('delete', $apartment);
        
        return response()->json([
            'message' => trans('messages.apartment.deleted_successfully')
        ]);
    }

    public function userApartments()
    {
        $this->authorize('viewAny', Apartment::class);
        
        $apartments = $this->apartmentService->getUserApartments(auth()->id());
        
        return ApartmentResource::collection($apartments);
    }
    
    /**
     * Change the current locale.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLocale(Request $request)
    {
        $locale = $request->input('locale');
        
        if (!in_array($locale, ['en', 'vi'])) {
            return response()->json([
                'message' => 'Invalid locale provided'
            ], 400);
        }
        
        // Set session locale
        $request->session()->put('locale', $locale);
        App::setLocale($locale);
        
        return response()->json([
            'message' => trans('messages.locale_changed'),
            'locale' => $locale
        ]);
    }
}