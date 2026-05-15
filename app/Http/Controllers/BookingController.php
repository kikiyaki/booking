<?php

namespace App\Http\Controllers;

use App\Exceptions\Booking\BookingOverlapException;
use App\Exceptions\Booking\MissingBookingFilterException;
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
                (int) $request->input('room_id'),
                (int) $request->input('user_id'),
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
            $bookings = $this->bookingService->getBookings(
                $request->has('room_id') ? (int) $request->query('room_id') : null,
                $request->has('user_id') ? (int) $request->query('user_id') : null,
            );

            return response()->json($bookings);
        } catch (MissingBookingFilterException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (RoomNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function rooms(): JsonResponse
    {
        return response()->json(Room::all());
    }
}
