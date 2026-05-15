<?php

namespace App\Services;

use App\Exceptions\Booking\BookingOverlapException;
use App\Exceptions\Booking\RoomNotFoundException;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
    public function storeBooking(int $roomId, int $userId, string $startsAt, string $endsAt): Booking
    {
        $this->ensureRoomExists($roomId);

        if ($this->checkBookingsOverlap($roomId, $startsAt, $endsAt)) {
            throw new BookingOverlapException();
        }

        return Booking::create([
            'room_id'   => $roomId,
            'user_id'   => $userId,
            'starts_at' => $startsAt,
            'ends_at'   => $endsAt,
        ]);
    }

    public function getBookingsByUser(int $userId): Collection
    {
        return Booking::where('user_id', $userId)->get();
    }

    public function getBookingsByRoom(int $roomId): Collection
    {
        $this->ensureRoomExists($roomId);

        return Booking::where('room_id', $roomId)->get();
    }

    private function ensureRoomExists(int $roomId): void
    {
        if (!Room::where('id', $roomId)->exists()) {
            throw new RoomNotFoundException($roomId);
        }
    }

    private function checkBookingsOverlap(int $roomId, string $startsAt, string $endsAt): bool
    {
        return Booking::where('room_id', $roomId)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}
