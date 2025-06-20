<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TableStatus extends Model
{
    use HasFactory;

    protected $table = 'table_status';

    protected $fillable = [
        'name',
    ];

    /**
     * Danh sách các bàn có trạng thái này
     */
    public function tables()
    {
        return $this->hasMany(Table::class, 'table_status_id');
    }
}
