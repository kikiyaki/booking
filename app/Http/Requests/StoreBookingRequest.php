<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id'   => ['required', 'integer', 'exists:rooms,id'],
            'user_id'   => ['required', 'integer', 'min:1'],
            'starts_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'ends_at'   => ['required', 'date_format:Y-m-d H:i:s', 'after:starts_at'],
        ];
    }
}
