<?php

namespace App\Http\Controllers;

use App\Exceptions\Booking\BookingOverlapException;
use App\Exceptions\Booking\RoomNotFoundException;
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
        try {
            $booking = $this->bookingService->storeBooking(
                $request->integer('room_id'),
                $request->integer('user_id'),
                $request->input('starts_at'),
                $request->input('ends_at')
            );

            return response()->json($booking, 201);
        } catch (BookingOverlapException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $bookings = $request->has('room_id')
                ? $this->bookingService->getBookingsByRoom($request->integer('room_id'))
                : $this->bookingService->getBookingsByUser($request->integer('user_id'));

            return response()->json($bookings);
        } catch (RoomNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function rooms(): JsonResponse
    {
        return response()->json(Room::all());
    }
}
