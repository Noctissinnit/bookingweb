<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Booking extends Model
{
    protected $fillable = ['user_id', 'room_id', 'date', 'start_time', 'end_time', 'description', 'nama', 'email', 'department', 'approved'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'booking_users', 'id_booking');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
