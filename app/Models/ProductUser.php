<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductUser extends Model
{
    use HasFactory;
    public static function HienThiTatCaSanPham()
    {
        $HienThiTatCaSanPham = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.description',
                'products.price',
                'products.image_food as image',
                'categories.name as category_name'
            )
            ->where('products.status', 1)
            ->orderBy('products.name', 'asc')
            ->get();

        return $HienThiTatCaSanPham;
    }

    public static function LayThongTinSanPham($categoryName)
    {
        return DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.rating',
                'products.image_food',
                'products.price',
                'categories.name as category_name'
            )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.status', 1)
            ->where('categories.name', $categoryName)
            ->orderBy('products.created_at', 'desc')
            ->take(8)
            ->get();
    }

    public static function ThongTinSanPham($slug)
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name as name',
                'products.slug as slug',
                'products.image_food',
                'products.description',
                'products.price',
                'products.status',
                'categories.name as category',
                'categories.slug as category_slug'
            )
            ->where('products.slug', $slug)
            ->first();
    }
    public static function DanhSachSize($slug)
    {
        return DB::table('product_size')
            ->join('products', 'products.id', '=', 'product_size.product_id')
            ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
            ->select('sizes.id', 'sizes.name', 'sizes.price')
            ->where('products.slug', $slug)
            ->where('sizes.status', 1)
            ->get();
    }
    public static function DanhSachTopping($slug)
    {
        return DB::table('product_topping')
            ->join('products', 'products.id', '=', 'product_topping.product_id')
            ->join('toppings', 'toppings.id', '=', 'product_topping.topping_id')
            ->select('toppings.id', 'toppings.name', 'toppings.price', 'product_topping.quantity')
            ->where('products.slug', $slug)
            ->where('toppings.status', 1)
            ->get();
    }

    public static function TimKiemSanPham($slug)
    {
        $danhSachSanPham = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.image_food as image',
                'categories.name as category_name',
                'products.price'
            )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.status', 1)
            ->where('categories.slug', '=', $slug)
            ->groupBy(
                'products.id',
                'products.name',
                'products.slug',
                'products.image_food',
                'products.price',
                'categories.name'
            )
            ->get();

        return $danhSachSanPham;
    }
    public static function TimKiemTheoTuKhoa($key)
    {
        // Tách từ khóa
        $keywords = preg_split('/\s+/', trim($key));

        $danhSachSanPham = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.description',
                'products.price',
                'products.image_food as image',
                'categories.name as category_name'
                // Lấy giá thấp nhất
            )
            ->where('products.status', 1)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->where(function ($q) use ($word) {
                        $q->whereRaw('LOWER(categories.name COLLATE utf8mb4_unicode_ci ) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(products.name COLLATE utf8mb4_unicode_ci ) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(products.description COLLATE utf8mb4_unicode_ci ) LIKE ?', ["%{$word}%"]);
                    });
                }
            })
            ->groupBy('products.id', 'products.name', 'products.slug', 'products.rating', 'categories.name',)
            ->orderBy('products.name')
            ->get();

        return $danhSachSanPham;
    }

    public static function LayTenSanPhamTheoId($id)
    {
        return DB::table('products')
            ->where('id', $id)
            ->select('name')
            ->get();
    }
}

