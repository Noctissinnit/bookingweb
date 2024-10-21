<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJabatan extends Model
{
    use HasFactory;


    protected $table = 'user_jabatans'; // Tentukan nama tabel jika diperlukan

    // Menentukan kolom yang dapat diisi secara massal
    protected $fillable = [
        'id_user_jabatan',
        'id_user',
        'id_jabatan',
        'id_department',
    ];
}
