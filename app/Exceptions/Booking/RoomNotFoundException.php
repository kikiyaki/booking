<?php

namespace App\Exceptions\Booking;

use RuntimeException;

class RoomNotFoundException extends RuntimeException
{
    public function __construct(int $roomId)
    {
        parent::__construct("Room #{$roomId} not found.");
    }
}
