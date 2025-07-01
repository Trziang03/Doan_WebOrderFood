<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    //
    use HasFactory;

    protected $fillable = ['name_method']; // các cột cho phép gán dữ liệuAdd commentMore actions
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_method_id');
    }
}
