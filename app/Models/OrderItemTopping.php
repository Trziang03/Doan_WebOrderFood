<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemTopping extends Model
{
    protected $table = 'order_item_toppings';
    public $timestamps = false;
    protected $fillable = [
        'order_item_id',
        'topping_id',
        'quantity',
        'price',
        'note',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function topping()
    {
        return $this->belongsTo(Topping::class, 'topping_id');
    }
}
