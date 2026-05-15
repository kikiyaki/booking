<?php

namespace App\Exceptions\Booking;

use RuntimeException;

class BookingOverlapException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The room is already booked for this time slot.');
    }
}
