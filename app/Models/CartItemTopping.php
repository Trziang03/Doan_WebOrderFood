<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItemTopping extends Model
{
    protected $table = 'cart_item_toppings';
    public $timestamps = false;

    protected $fillable = [
        'cart_item_id',
        'topping_id',
        'quantity',
        'price',
    ];

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class, 'cart_item_id');
    }

    public function topping()
    {
        return $this->belongsTo(Topping::class);
    }
}

