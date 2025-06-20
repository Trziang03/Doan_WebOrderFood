<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'status',
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_size');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'size_id');
    }
}