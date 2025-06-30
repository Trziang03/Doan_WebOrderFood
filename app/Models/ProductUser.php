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
                'categories.name as category_name',
                DB::raw('MIN(image_products.image) as image'), // Lấy hình ảnh đầu tiên
                // Lấy giá thấp nhất
            )
            ->join('image_products', 'products.id', '=', 'image_products.product_id')
            ->join('categories', 'brands.category_id', '=', 'categories.id')
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
            ->groupBy('products.id', 'products.name', 'products.slug', 'categories.name')
            ->orderBy('products.name')
            ->get();

        return $danhSachSanPham;
    }

    public static function HinhAnhSamPham($slug)
    {
        return DB::table('image_products')
            ->join('products', 'products.id', '=', 'image_products.product_id')
            ->select('image_products.image')
            ->where('products.slug', $slug)
            ->get();
    }
    public static function LuotThichSanPham($slug)
    {
        return DB::table('like_products')
            ->join('products', 'products.id', '=', 'like_products.product_id')
            ->select('like_products.id')
            ->where('products.slug', $slug)
            ->count();
    }
    public static function ThongSoKiThuatSanPham($slug)
    {
        return DB::table('product_specifications')
            ->join('products', 'products.id', '=', 'product_specifications.product_id')
            ->select('product_specifications.*')
            ->where('products.slug', $slug)
            ->get();
    }


    public static function LayThongTinSanPhamTheoMau($slug, $internal_memory, $color)
    {
        return DB::table('product_variants')
            ->join('products', 'product_id', '=', 'products.id')
            ->where('products.slug', $slug)
            ->where('product_variants.internal_memory', $internal_memory)
            ->where('product_variants.color', $color)
            ->select('product_variants.id', 'product_variants.price', 'product_variants.image', 'product_variants.stock')
            ->first();
    }
    public static function SanPhamBanChay()
    {
        return DB::table('order_items')
        ->select(
            'products.id',
            'products.name',
            'order_items.slug_product as slug',
            'products.rating',
            DB::raw('MIN(product_variants.id) as variants'),
            DB::raw('MIN(product_variants.price) as price'),
            DB::raw('SUM(order_items.quantity) as total')
        )
        ->join('product_variants','order_items.product_variant_id','=','product_variants.id')
        ->join('products','product_variants.product_id','=','products.id')
        ->join('orders','order_items.order_id', '=' ,'orders.id')
        ->groupBy('products.id', 'products.name', 'order_items.slug_product','products.rating','product_variants.product_id')
        ->orderBy(DB::raw('SUM(order_items.quantity)'),'desc')
        ->where('products.status',1)
        ->where('order_status_id',3)
        ->take(8)->get();
    }

    public static function SanPhamTuongDuong($category, $brand, $slug)
    {
        return DB::table('products')
            ->select(
                'products.name',
                'products.rating',
                'products.slug',
                DB::raw('MIN(product_variants.id) as variants'),
                DB::raw('MIN(image_products.image) as image'),
                DB::raw('MIN(product_variants.price) as price')
            )
            ->join('image_products', 'products.id', '=', 'image_products.product_id')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->join('categories', 'brands.category_id', '=', 'categories.id')
            ->where('products.status', 1)
            ->where('brands.name', $brand)
            ->where('products.status', 1)
            ->where('product_variants.status', 1)
            ->where('products.slug', '!=', $slug)
            ->where('categories.slug', $category)
            ->groupBy('products.name', 'products.rating', 'products.slug')
            ->take(8)->get();
    }
    public static function LayDanhSachSanPhamTheoDanhMuc($category, $slug, $brand)
    {
        return DB::table('products')
            ->select(
                'products.name',
                'products.rating',
                'products.slug',
                DB::raw('MIN(product_variants.id) as variants'),
                DB::raw('MIN(image_products.image) as image'),
                DB::raw('MIN(product_variants.price) as price')
            )
            ->join('image_products', 'products.id', '=', 'image_products.product_id')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->join('categories', 'brands.category_id', '=', 'categories.id')
            ->where('products.status', 1)
            ->where('products.slug', '!=', $slug)
            ->where('products.status', 1)
            ->where('brands.name', '!=', $brand)
            ->where('product_variants.status', 1)
            ->where('categories.slug', $category)->groupBy('products.name', 'products.rating', 'products.slug')
            ->take(8)->get();
    }
    public static function LayTenSanPhamTheoId($id)
    {
        return DB::table('products')
            ->where('id', $id)
            ->select('name')
            ->get();
    }
    public static function DiemDanhGia($slug)
    {
        return DB::table('products')
            ->join('ratings', 'products.id', '=', 'product_id')
            ->where('products.slug', $slug)
            ->avg('ratings.point');
    }

}

