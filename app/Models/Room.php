<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function isBooked()
    {
        return Booking::where('room_id', $this->id)->exists();
    }
}
