<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'price',
    ];

    // Nếu cần quan hệ với sản phẩm
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_topping')
                    ->withPivot('quantity');
    }

    // Đổi từ belongsToMany → hasMany qua model trung gian
    public function orderItemToppings()
    {
        return $this->hasMany(OrderItemTopping::class, 'topping_id');
    }
}
