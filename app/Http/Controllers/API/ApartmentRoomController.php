<?php
// app/Http/Controllers/API/ApartmentRoomController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\ApartmentRoomResource;
use App\Services\ApartmentRoomService;
use Illuminate\Http\Request;

class ApartmentRoomController extends Controller
{
    protected $roomService;

    public function __construct(ApartmentRoomService $roomService)
    {
        $this->roomService = $roomService;
        $this->middleware('auth:sanctum');
        $this->middleware('check.room.ownership')->only(['show', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $apartmentId = $request->query('apartment_id');
        $roomNumber = $request->query('room_number');
        
        $criteria = [];
        
        if ($apartmentId) {
            $criteria['apartment_id'] = $apartmentId;
        }
        
        if ($roomNumber) {
            $criteria['room_number'] = $roomNumber;
        }
        
        if (!empty($criteria)) {
            $rooms = $this->roomService->searchRooms($criteria, $perPage);
        } else {
            $rooms = $this->roomService->getAllRooms($perPage);
        }
        
        return ApartmentRoomResource::collection($rooms);
    }

    public function show($id)
    {
        $room = $this->roomService->getRoom($id);
        
        return new ApartmentRoomResource($room);
    }

    public function store(StoreRoomRequest $request)
    {
        $data = $request->validated();
        
        $room = $this->roomService->createRoom($data);
        
        return new ApartmentRoomResource($room);
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $data = $request->validated();
        
        $room = $this->roomService->updateRoom($id, $data);
        
        return new ApartmentRoomResource($room);
    }

    public function destroy($id)
    {
        $this->roomService->deleteRoom($id);
        
        return response()->json([
            'message' => 'Room deleted successfully'
        ]);
    }

    public function byApartment($apartmentId)
    {
        // Check if the apartment belongs to the authenticated user
        // Could add middleware for this
        
        $rooms = $this->roomService->getRoomsByApartment($apartmentId);
        
        return ApartmentRoomResource::collection($rooms);
    }

    public function withActiveContract()
    {
        $rooms = $this->roomService->getRoomsWithActiveContract();
        
        return ApartmentRoomResource::collection($rooms);
    }

    public function withoutTenant()
    {
        $rooms = $this->roomService->getRoomsWithoutTenant();
        
        return ApartmentRoomResource::collection($rooms);
    }
}