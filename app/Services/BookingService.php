<?php

namespace App\Services;

use App\Exceptions\Booking\BookingOverlapException;
use App\Exceptions\Booking\MissingBookingFilterException;
use App\Exceptions\Booking\RoomNotFoundException;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function storeBooking(int $roomId, int $userId, string $startsAt, string $endsAt): Booking
    {
        DB::beginTransaction();

        try {
            $room = Room::query()
                ->lockForUpdate()
                ->find($roomId);
            if (!$room) {
                throw new RoomNotFoundException($roomId);
            }

            if ($this->checkBookingsOverlap($roomId, $startsAt, $endsAt)) {
                throw new BookingOverlapException();
            }

            $createdBooking = Booking::create([
                'room_id' => $roomId,
                'user_id' => $userId,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            DB::commit();

            return $createdBooking;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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

    public function getBookings(?int $roomId, ?int $userId): Collection
    {
        if ($roomId !== null) {
            return $this->getBookingsByRoom($roomId);
        }

        if ($userId !== null) {
            return $this->getBookingsByUser($userId);
        }

        throw new MissingBookingFilterException();
    }

    public function getBookingsByRoom(int $roomId): Collection
    {
        $this->ensureRoomExists($roomId);

        return Booking::where('room_id', $roomId)->get();
    }

    public function getBookingsByUser(int $userId): Collection
    {
        return Booking::where('user_id', $userId)->get();
    }
}
