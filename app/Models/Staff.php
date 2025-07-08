<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'users'; // Dùng bảng users

    // Nếu bạn không có các cột timestamps (created_at, updated_at)
    public $timestamps = true;

    // Các cột bạn cho phép đổ dữ liệu vào
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'phone',
        'role',
        'gender',
        'status',
        'date_of_birth',
        'password',
        'image',
    ];

}
