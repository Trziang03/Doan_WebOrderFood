<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    //
    use HasFactory;
    protected $fillable = ['name', 'slug', 'category_id', 'image_food', 'description', 'price', 'status'];
    
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'product_topping')
            ->withPivot('quantity');
    }
    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size');
    }
    public function image_products():HasMany
    {
        return $this->hasMany(ImageProduct::class);
    }
    public function product_specification():HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }
    // public function ratings():HasMany
    // {
    //     return $this->hasMany(Topping::class, 'product_topping')->withPivot('quantity');
    // }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'food_id');
    }
}

