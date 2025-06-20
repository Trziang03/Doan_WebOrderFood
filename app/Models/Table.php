<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'table_name',
        'qr_code_table',
        'table_status_id',
    ];

    /**
     * Trạng thái của bàn (ví dụ: trống, đã đặt, đang phục vụ)
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