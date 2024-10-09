<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Member;

class Booking extends Model   
{
    protected $fillable = ['user_id', 'room_id', 'date', 'start_time', 'end_time', 'description','nama','email','department','approved'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    
    public function members(){
        return $this->belongsToMany(Member::class, 'booking_members', 'id_booking', 'id_member');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}