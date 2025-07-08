<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Topping;
use App\Models\Size;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Product::with('category'); // để load tên danh mục

        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = strtolower(trim($request->keyword));

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('price', 'like', '%' . $keyword . '%');

                // Tìm theo tên danh mục (từ bảng categories)
                $q->orWhereHas('category', function ($cat) use ($keyword) {
                    $cat->where('name', 'like', '%' . $keyword . '%');
                });

                // Tìm theo trạng thái
                if (in_array($keyword, ['hiện', 'hien', '1'])) {
                    $q->orWhere('status', 1);
                } elseif (in_array($keyword, ['ẩn', 'an', '0'])) {
                    $q->orWhere('status', 0);
                }
            });
        }

        $danhSachSanPham = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();


        return view('admin.product.product', [
            'danhSachSanPham' => $danhSachSanPham,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $danhSachPhanLoai = Category::where('status', 1)->get();
        $toppings = Topping::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get(); // Thêm dòng này

        return view('admin.product.addproduct', [
            'danhSachPhanLoai' => $danhSachPhanLoai,
            'toppings' => $toppings,
            'sizes' => $sizes // Truyền sang view
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Xử lý hình ảnh
        $image_food = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/'), $imageName);
            $image_food = 'images/' . $imageName;
        }

        // Tạo sản phẩm
        $product = Product::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'status' => $request->input('status'),
            'price' => $request->input('price'),
            'image_food' => $image_food ?? 'images/default.png',
        ]);

        // Gắn sizes nếu có
        if ($request->has('sizes')) {
            $product->sizes()->sync($request->sizes);
        }

        // Gắn toppings với quantity nếu có
        $toppings = [];
        if ($request->has('toppings')) {
            foreach ($request->toppings as $toppingId => $data) {
                if (isset($data['selected']) && $data['selected'] == 1) {
                    $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
                    $toppings[$toppingId] = ['quantity' => $quantity];
                }
            }
            $product->toppings()->sync($toppings);
        }

        return redirect()->route('admin.product')
            ->with('message', 'Thêm món ăn thành công');
    }

    public function storeTopping(Request $request)
    {
        Topping::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => 1,
        ]);
        return back()->with('message', 'Đã thêm topping mới.');
    }

    public function storeSize(Request $request)
    {
        Size::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => 1
        ]);
        return back()->with('message', 'Đã thêm size mới.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sanPham = Product::with(['toppings', 'sizes'])->findOrFail($id);
        $danhSachPhanLoai = Category::where('status', 1)->get();
        $toppings = Topping::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();

        return view('admin.product.editproduct', [
            'sanPham' => $sanPham,
            'danhSachPhanLoai' => $danhSachPhanLoai,
            'toppings' => $toppings,
            'sizes' => $sizes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean',
            'value.*' => 'nullable', // nếu có thuộc tính động
        ], [
            'name.required' => 'Vui lòng nhập tên món ăn',
            'description.required' => 'Vui lòng nhập mô tả',
            'price.required' => 'Vui lòng nhập giá',
            'price.numeric' => 'Giá phải là số',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'status.required' => 'Vui lòng chọn trạng thái',
        ]);

        $product = Product::findOrFail($id);

        // Kiểm tra trùng slug
        $check = Product::where('slug', Str::slug($request->name))
            ->where('id', '<>', $id)->first();
        if ($check) {
            return back()->with('msg', 'Tên món ăn đã tồn tại, vui lòng chọn tên khác!');
        }

        // Nếu có file ảnh mới
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/'), $imageName);

            // Xóa ảnh cũ nếu có
            if ($product->image_food && file_exists(public_path($product->image_food))) {
                unlink(public_path($product->image_food));
            }

            $product->image_food = 'images/' . $imageName;
        }

        // Sau khi xử lý xong, lưu lại
        $product->save();

        // Cập nhật thông tin sản phẩm
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'status' => $request->status,
        ]);

        // // Cập nhật toppings
        $toppingIds = $request->input('toppings', []); // mảng ID topping
        $product->toppings()->sync($toppingIds);

        // $toppings = [];
        // if ($request->has('toppings')) {
        //     foreach ($request->toppings as $toppingId => $data) {
        //         if (isset($data['selected']) && $data['selected'] == 1) {
        //             $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
        //             $toppings[$toppingId] = ['quantity' => $quantity];
        //         }
        //     }
        // }
        // $product->toppings()->sync($toppings); // <- luôn sync, kể cả mảng rỗng

        // Cập nhật sizes
        $sizes = $request->sizes ?? []; // Mảng các ID size được chọn
        $product->sizes()->sync($sizes); // <- luôn sync

        return redirect()->route('admin.product', ['id' => $id])
            ->with('message', 'Cập nhật món ăn thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại!'
            ], 404);
        }

        $name = $product->name;

        // Xoá ảnh sản phẩm
        foreach ($product->image_products as $image_product) {
            if ($image_product->image && File::exists(public_path('images/' . $image_product->image))) {
                File::delete(public_path('images/' . $image_product->image));
            }
        }

        $product->forceDelete();

        return response()->json([
            'message' => "Xóa sản phẩm {$name} thành công!"
        ]);
    }

    public function filterByCategory($id)
    {
        $categories = Category::all();
        $danhSachSanPham = Product::where('category_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('admin.product.product', [
            'danhSachSanPham' => $danhSachSanPham,
            'categories' => $categories,
        ]);
    }
}
