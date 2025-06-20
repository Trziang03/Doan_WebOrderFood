<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;


class Category extends Model
{
    //
    use HasFactory;

    protected $fillable = ['id', 'name', 'description', 'slug', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
