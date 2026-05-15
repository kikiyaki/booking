<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($this->bookingService->checkBookingsOverlap($data['room_id'], $data['starts_at'], $data['ends_at'])) {
            return response()->json(['message' => 'The room is already booked for this time slot.'], 409);
        }

        $booking = $this->bookingService->storeBooking(
            $data['room_id'],
            $data['user_id'],
            $data['starts_at'],
            $data['ends_at']
        );

        return response()->json($booking, 201);
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->has('room_id')) {
            $room = Room::find($request->query('room_id'));

            if (!$room) {
                return response()->json(['message' => 'Room not found.'], 404);
            }

            return response()->json($this->bookingService->getBookingsByRoom($room->id));
        }

        return response()->json(
            $this->bookingService->getBookingsByUser((int) $request->query('user_id'))
        );
    }

    public function rooms(): JsonResponse
    {
        return response()->json(Room::all());
    }
}
