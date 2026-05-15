<?php

namespace App\Exceptions\Booking;

use RuntimeException;

class MissingBookingFilterException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Either room_id or user_id query parameter is required.');
    }
}
