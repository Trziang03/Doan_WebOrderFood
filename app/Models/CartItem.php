<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    public $timestamps = false;

    protected $fillable = [
        'table_id',
        'product_id',
        'size_id',
        'quantity',
        'note',
        'created_at',
    ];

    // Một cart item thuộc về một sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Một cart item thuộc về một size
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    // Một cart item có thể có nhiều topping
    public function toppings()
    {
        return $this->hasMany(CartItemTopping::class, 'cart_item_id');
    }

    // Một cart item thuộc về một bàn
    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

}
