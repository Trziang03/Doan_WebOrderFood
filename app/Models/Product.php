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
<<<<<<< HEAD
    protected $fillable = ['name','slug','brand_id','description','status'];
    protected $table = 'Products'; 
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    } 
=======
    protected $fillable = ['name', 'slug', 'category_id', 'image_food', 'description', 'price', 'status'];
>>>>>>> 521da537225f710a7f10e4c2ea3d0c804cd43cb5


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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'food_id');
    }
}

