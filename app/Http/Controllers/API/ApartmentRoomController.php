<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\ApartmentRoomResource;
use App\Services\ApartmentRoomService;
use Illuminate\Http\Request;

class ApartmentRoomController extends BaseController
{
    protected $roomService;

    public function __construct(ApartmentRoomService $roomService)
    {
        $this->roomService = $roomService;
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
        
        return $this->successResponse(
            ApartmentRoomResource::collection($rooms)
        );
    }

    public function show($id)
    {
        $room = $this->roomService->getRoom($id);
        
        if (!$room) {
            return $this->notFoundResponse(trans('messages.room.not_found'));
        }
        
        return $this->successResponse(
            new ApartmentRoomResource($room)
        );
    }

    public function store(StoreRoomRequest $request)
    {
        $data = $request->validated();
        
        $room = $this->roomService->createRoom($data);
        
        return $this->successResponse(
            new ApartmentRoomResource($room),
            trans('messages.room.created_successfully'),
            201
        );
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $data = $request->validated();
        
        $room = $this->roomService->updateRoom($id, $data);
        
        if (!$room) {
            return $this->notFoundResponse(trans('messages.room.not_found'));
        }
        
        return $this->successResponse(
            new ApartmentRoomResource($room),
            trans('messages.room.updated_successfully')
        );
    }

    public function destroy($id)
    {
        $result = $this->roomService->deleteRoom($id);
        
        if (!$result) {
            return $this->notFoundResponse(trans('messages.room.not_found'));
        }
        
        return $this->successResponse(
            null,
            trans('messages.room.deleted_successfully')
        );
    }

    public function byApartment($apartmentId)
    {
        $rooms = $this->roomService->getRoomsByApartment($apartmentId);
        
        return $this->successResponse(
            ApartmentRoomResource::collection($rooms)
        );
    }

    public function withActiveContract()
    {
        $rooms = $this->roomService->getRoomsWithActiveContract();
        
        return $this->successResponse(
            ApartmentRoomResource::collection($rooms)
        );
    }

    public function withoutTenant()
    {
        $rooms = $this->roomService->getRoomsWithoutTenant();
        
        return $this->successResponse(
            ApartmentRoomResource::collection($rooms)
        );
    }
}