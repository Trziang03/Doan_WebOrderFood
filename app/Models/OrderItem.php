<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    //
    use HasFactory;
    protected $fillable = ['quantity', 'note', 'total_price', 'order_id', 'size_id', 'product_id'];
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'order_item_toppings', 'order_item_id', 'topping_id')
            ->withPivot('quantity', 'price');
    }
    // Quan hệ mới qua model trung gian
    public function orderItemToppings()
    {
        return $this->hasMany(OrderItemTopping::class, 'order_item_id');
    }
}
