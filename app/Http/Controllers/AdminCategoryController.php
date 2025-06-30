<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();
        $query->where('status', 1);
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = strtolower(trim($request->keyword));

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');

                // Tìm theo trạng thái
                if (in_array($keyword, ['hiện', 'hien', '1'])) {
                    $q->orWhere('status', 1);
                } elseif (in_array($keyword, ['ẩn', 'an', '0'])) {
                    $q->orWhere('status', 0);
                }
            });
        }

        $danhSachDanhMuc = $query->paginate(5)->withQueryString();

        return view('admin.category.category', ['danhSachDanhMuc' => $danhSachDanhMuc]);
    }
    //chuyển hướng trang thêm danh mục
    public function addCategory()
    {
        return view('admin.category.addcategory');
    }
    // lưu trữ danh mục
    public function storeCategory(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|unique:categories,name|max:255','regex:/^[\p{L}0-9\s\-]+$/u',
            'description' => 'required|max:255',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'name.max' => 'Tên danh mục không vượt quá 255 ký tự',
            'name.regex' => 'Tên danh mục chỉ được chứa chữ, số, dấu cách và gạch ngang.',
            'description' => 'Vui lòng nhập mô tả',
            'description.max' => 'Mô tả không vượt quá 255 ký tự',
        ]);

        $slug = $request->input('slug') ?: Str::slug($request->input('name'));

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.category')
            ->with('message', 'Thêm danh mục thành công');
    }

    //tìm id và chuyển trang sửa danh mục
    public function editCategory(string $id)
    {
        $danhMucTimKiem = Category::findOrFail($id);

        return view('admin.category.editcategory')
            ->with('danhMucTimKiem', $danhMucTimKiem);
    }

    //cập nhật danh mục
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('categories')->ignore($id),
            ],
            'description' => 'required|max:255',
            'status' => 'required|in:0,1'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'name.max' => 'Tên danh mục không vượt quá 255 ký tự',
            'description.required' => 'Vui lòng nhập mô tả',
        ]);

        $slug = Str::slug($request->input('name'));

        Category::where('id', $id)->update([
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'updated_at' => now(), // Optional: nếu muốn cập nhật thời gian
        ]);

        return redirect()->route('admin.category', ['id' => $id])
            ->with('message', 'Cập nhật danh mục thành công');
    }

    //lọc theo tên danh mục
    public function filterCategory(Request $request)
    {
        $categoryId = $request->input('categoryFilter', 'all');

        if ($categoryId == 'all') {
            $danhSachDanhMuc = Category::where('status', 1)->get();
        } else {
            $danhSachDanhMuc = Category::where('id', $categoryId)->get();
        }

        return response()->json($danhSachDanhMuc);
    }

    //xóa(ẩn) danh mục
    public function deleteCategory(Request $request, $id)
    {
        if (mb_strtoupper($request->input('confirm'), 'UTF-8') !== 'XÓA') {
            return response()->json(['message' => 'Bạn phải nhập "XÓA" để xác nhận.'], 400);
        }

        $danhMucTimKiem = Category::find($id);
        if (!$danhMucTimKiem) {
            return response()->json(['message' => 'Danh mục không tồn tại.'], 404);
        }

        $danhMucTimKiem->update(['status' => 0]);
        return response()->json(['message' => 'Ẩn danh mục thành công.']);
    }

}
