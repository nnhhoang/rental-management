<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
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

        return response()->json([
            'message' => trans('messages.apartment.created_successfully'),
            'data' => new ApartmentResource($apartment),
        ], 201);
    }

    public function update(UpdateApartmentRequest $request, $id)
    {
        $data = $request->validated();

        $apartment = $this->apartmentService->updateApartment($id, $data);

        return response()->json([
            'message' => trans('messages.apartment.updated_successfully'),
            'data' => new ApartmentResource($apartment),
        ]);
    }

    public function destroy($id)
    {
        $this->apartmentService->deleteApartment($id);

        return response()->json([
            'message' => trans('messages.apartment.deleted_successfully'),
        ]);
    }

    public function userApartments()
    {
        $apartments = $this->apartmentService->getUserApartments(auth()->id());

        return ApartmentResource::collection($apartments);
    }

    /**
     * Change the current locale.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLocale(Request $request)
    {
        $locale = $request->input('locale');

        if (! in_array($locale, ['en', 'vi'])) {
            return response()->json([
                'message' => 'Invalid locale provided',
            ], 400);
        }

        // Set session locale
        $request->session()->put('locale', $locale);
        App::setLocale($locale);

        return response()->json([
            'message' => trans('messages.locale_changed'),
            'locale' => $locale,
        ]);
    }
}
