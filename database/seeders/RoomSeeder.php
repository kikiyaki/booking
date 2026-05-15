<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run()
    {
        Room::insert([
            ['name' => 'Room A'],
            ['name' => 'Room B'],
            ['name' => 'Room C'],
        ]);
    }
}
