<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateCalendar(): void
    {
        $response = $this->post(route('bookings.store'), [
            '_token' => csrf_token(),
            'nis' => 123456,
            'password' => 'password',
            'room_id' => 1,
            'date' => '2024-10-24',
            'start_time' => '07:00',
            'end_time' => '09:00',
            'description' => 'Booking',
            'nama' => 'Admin User',
            'email' => 'admin@example.com',
            'department' => 1,
            'members' => [1]
        ]);
        Log::debug($response->baseResponse);
        $response->assertStatus(200);
    }
}
