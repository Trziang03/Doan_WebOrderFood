<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Blog;
use App\Models\ProductUser;
use App\Models\Table;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;

class UserController extends Controller
{

    public function index()
    {

        $danhSachMonAn = ProductUser::LayThongTinSanPham('Món ăn');
        $danhSachDoUong = ProductUser::LayThongTinSanPham('Đồ uống');
        return view('User.pages.index')->with([
            "danhSachMonAn" => $danhSachMonAn,
            "danhSachDoUong" => $danhSachDoUong,
        ]);
    }

    public function ChiTietSanPham($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product || $product->status != 1) {
            return view('user.pages.404');
        }

        $thongTinSanPham = ProductUser::ThongTinSanPham($slug);
        $danhSachTopping = ProductUser::DanhSachTopping($slug);
        $danhSachSize = ProductUser::DanhSachSize($slug);

        if (!$thongTinSanPham) {
            return view('user.pages.404');
        }

        return view('user.pages.detail')->with([
            'slug' => $slug,
            'danhSachTopping' => $danhSachTopping,
            'danhSachSize' => $danhSachSize,
            'thongTinSanPham' => $thongTinSanPham,

        ]);
    }

    public function showmenu(Request $request)
    {
        $tableId = $request->input('id');
        $token = $request->input('token');

        if ($tableId && $token) {
            // Truy cập lần đầu từ mã QR
            $table = Table::where('id', $tableId)->where('token', $token)->first();
            if (!$table) {
                abort(403, 'Mã QR không hợp lệ hoặc đã hết hạn.');
            }

            // Lưu vào cookie + session
            Cookie::queue('table_id', $table->id, 30); // cookie 30 phút
            session(['table_id' => $table->id]);
        }

        // Nếu không có id/token, lấy từ session hoặc cookie
        $table_id = session('table_id') ?? $request->cookie('table_id');
        if (!$table_id) {
            return redirect('/404')->with('error', 'Không xác định được bàn hoặc bàn không hợp lệ.');
        }

        // Lấy lại thông tin bàn
        $table = Table::find($table_id);

        // Lấy toàn bộ sản phẩm
        $layTatCaSanPham = ProductUser::HienThiTatCaSanPham();

        return view('user.pages.menu', [
            'table' => $table,
            'layTatCaSanPham' => $layTatCaSanPham
        ]);
    }

    public function timKiemToanBo(Request $request)
    {
        $keyword = $request->input('keyword');


        // Tìm trong tất cả sản phẩm (không phụ thuộc danh mục)
        $layTatCaSanPham = Product::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->get();

        return view('User.pages.menu', [
            'layTatCaSanPham' => $layTatCaSanPham,
            'danhSachDanhMuc' => Category::where('status', 1)->get(),
            'keyword' => $keyword,
        ]);
    }
    public function timKiemSanPhamTheoDanhMuc($slug)
    {
        // Tìm danh mục theo slug
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return redirect()->back()->with('error', 'Không tìm thấy danh mục.');
        }

        // Lấy các sản phẩm thuộc danh mục
        $layTatCaSanPham = Product::where('category_id', $category->id)->where('status', 1)->get();

        // Gửi dữ liệu ra view
        return view('User.pages.menu', [
            'layTatCaSanPham' => $layTatCaSanPham,
            'danhSachDanhMuc' => Category::where('status', 1)->get(),
        ]);
    }
    //Trang Giới Thiệu
    public function GioiThieu()
    {
        $danhSachBaiViet = Blog::layTatCaBaiViet();
        return view('user.pages.blog')->with('danhSachBaiViet', $danhSachBaiViet);
    }

    //Trang Liên Hệ
    public function LienHe()
    {
        return view('user.pages.contact');
    }

    //Trang Đăng Ký
    public function DangKy(Request $request)
    {
        $request->validate(
            [
                'username' => 'required|string|max:50|unique:users,username',
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone',
                'email_register' => 'required|email|max:255|unique:users,email',
                'password_register' => 'required|string
                |regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
                |regex:/^[a-zA-Z0-9@$!%*?&]+$/'
            ],
            [
                'username.required' => 'Vui lòng nhập username',
                'username.max' => 'Username không được quá 50 ký tự',
                'username.unique' => 'Username đã tồn tại',
                'full_name.required' => 'Vui lòng nhập họ và tên',
                'full_name.max' => 'Họ và tên không được quá 255 ký tự',
                'email_register.required' => 'Vui lòng nhập email',
                'email_register.email' => 'Vui lòng nhập đúng định đạng email',
                'email_register.max' => 'Email không được quá 255 ký tự',
                'email_register.unique' => 'Email đã được sử dụng',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.unique' => 'Số điện thoại đã được sử dụng',
                'phone.regex' => 'Vui lòng nhập ký tự số ( 0 đến 9 ) không quá 10 kí tự',
                'password_register.required' => 'Vui lòng nhập password',
                'password_register.regex' => 'Password không chứa dấu phải có tối thiểu 8 kí tự bao gồm chữ hoa, chữ thường, kí tự số và kí tự đặt biệt'
            ]
        );
        DB::table('users')->insert([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'gender' => 'Nam',
            'date_of_birth' => now(),
            'image' => '',
            'phone' => $request->phone,
            'email' => $request->email_register,
            'password' => Hash::make($request->password_register),
            'status' => 1
        ]);
        return response()->json(['message' => 'Đăng ký thành công']);
    }
    public function DangNhap(Request $request)
    {
        $request->validate(
            [
                'email_login' => 'required|email|string|max:255|exists:users,email',
                'password_login' => 'required|string'
            ],
            [
                'email_login.required' => 'Vui lòng nhập email',
                'email_login.exists' => 'Email chưa được đăng ký',
                'email_login.email' => 'Bạn chưa nhập đúng định đạng email',
                'email_login.max' => 'Email không được quá 255 ký tự',
                'password_login.required' => 'Vui lòng nhập password',
            ]
        );
        if (Auth::attempt(['email' => $request->email_login, 'password' => $request->password_login])) {
            if (Auth::user()->role == "NV" || Auth::user()->role == "QL")
                return redirect()->route('admin.index');
            return response()->json(['message' => 'Đăng nhập thành công']);
        } else {
            return response()->json(['msg_error' => 'Password chưa chính xác!' . '<br>' . ' Vui lòng nhập lại password'], 401);
        }
    }
    public function Logout()
    {
        Auth::logout();
        return redirect()->back();
    }


}
