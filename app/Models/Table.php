<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'name',
        'qr_code',
        'table_status_id',
        'token',
    ];

    /**
     */
    public function status()
    {
        return $this->belongsTo(TableStatus::class, 'table_status_id');
    }

    /**
     * Các đơn hàng gắn với bàn này
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
