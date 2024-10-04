<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class);
    // }
    public function booking()
    {
        return Booking::where('room_id', $this->id)->first();
    }
    // Jika room dibooking oleh user
    public function isBooked()
    {
        return Booking::where('room_id', $this->id)->exists();
    }
    // Jika room dibooking oleh user yang sekarang terautentikasi
    public function isBookedAuth()
    {
        return Booking::where('room_id', $this->id)->where('user_id', Auth::id())->exists();
    }
}
