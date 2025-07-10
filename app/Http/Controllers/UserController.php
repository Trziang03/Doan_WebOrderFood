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
        // 1. Nếu có 'id' và 'token' trên URL (quét QR lần đầu)
        if ($request->has(['id', 'token'])) {
            $tableId = $request->input('id');
            $token = $request->input('token');

            // 2. Tìm bàn có id và token khớp
            $table = Table::where('id', $tableId)
                ->where('token', $token)
                ->first();

            // 3. Không tìm thấy bàn => mã QR sai hoặc hết hạn
            if (!$table) {
                return redirect('/404')->with('error', 'Mã QR không hợp lệ hoặc đã hết hạn.');
            }

            // 4. Nếu bàn đang được dọn => chặn và xóa session + cookie
            if ($table->table_status_id == 3) {
                session()->forget(['table_id', 'qr_token']);
                Cookie::queue(Cookie::forget('table_id'));
                return redirect('/404')->with('error', 'Bàn đang được dọn dẹp.');
            }

            // 5. Nếu bàn đang trống (status = 1) => chuyển sang phục vụ (status = 2)
            if ($table->table_status_id == 1) {
                $table->table_status_id = 2;
                $table->save();
            }

            // 6. Lưu session + cookie (1 phút)
            session([
                'table_id' => $table->id,
                'qr_token' => $token
            ]);

            session()->forget('current_order_code'); // Thêm dòng này

            Cookie::queue('table_id', encrypt($table->id), 1);
        }

        // 7. Nếu không có id/token => quay lại từ trình duyệt (dùng session hoặc cookie)
        $tableId = session('table_id');

        if (!$tableId && $request->hasCookie('table_id')) {
            try {
                $tableId = decrypt($request->cookie('table_id'));
                $table = Table::find($tableId);

                // 8. Nếu bàn không hợp lệ hoặc đang dọn
                if (!$table || $table->table_status_id == 3) {
                    session()->forget(['table_id', 'qr_token']);
                    Cookie::queue(Cookie::forget('table_id'));
                    return redirect('/404')->with('error', 'Bàn không hợp lệ hoặc đang được dọn dẹp.');
                }

                // 9. Nếu bàn vẫn đang trống (1) => tự động chuyển thành đang phục vụ (2)
                if ($table->table_status_id == 1) {
                    $table->table_status_id = 2;
                    $table->save();
                }

                // 10. Lưu lại session
                session([
                    'table_id' => $table->id,
                    'qr_token' => $table->token
                ]);
                session()->forget('current_order_code'); // thêm tại đây nếu cần
            } catch (\Exception $e) {
                return redirect('/404')->with('error', 'Cookie bàn không hợp lệ hoặc đã bị chỉnh sửa.');
            }
        }

        // 11. Không có bàn sau tất cả => chặn
        if (!$tableId) {
            return redirect('/404')->with('error', 'Không xác định được bàn.');
        }

        // 12. Lấy bàn lần cuối và kiểm tra trạng thái
        $table = Table::find($tableId);
        if (!$table) {
            session()->forget(['table_id', 'qr_token']);
            Cookie::queue(Cookie::forget('table_id'));
            return redirect('/404')->with('error', 'Không tìm thấy bàn.');
        }

        if ($table->table_status_id == 3) {
            session()->forget(['table_id', 'qr_token']);
            Cookie::queue(Cookie::forget('table_id'));
            return redirect('/404')->with('error', 'Bàn đang được dọn dẹp.');
        }

        // 13. Lấy danh sách sản phẩm
        $layTatCaSanPham = ProductUser::HienThiTatCaSanPham();

        $layTatCaSanPham = Product::with('sizes')->get();

        // 14. Trả về giao diện menu
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

        // Lấy thông tin bàn từ session hoặc cookie (giống như showmenu)
        $table_id = session('table_id');
        if (!$table_id && $request->hasCookie('table_id')) {
            try {
                $table_id = decrypt($request->cookie('table_id'));
            } catch (\Exception $e) {
                $table_id = null;
            }
        }
        $table = $table_id ? Table::find($table_id) : null;

        return view('User.pages.menu', [
            'layTatCaSanPham' => $layTatCaSanPham,
            'danhSachDanhMuc' => Category::where('status', 1)->get(),
            'keyword' => $keyword,
            'table' => $table,
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

        // Lấy thông tin bàn từ session hoặc cookie (giống như showmenu)
        $table_id = session('table_id');
        if (!$table_id && request()->hasCookie('table_id')) {
            try {
                $table_id = decrypt(request()->cookie('table_id'));
            } catch (\Exception $e) {
                $table_id = null;
            }
        }
        $table = $table_id ? Table::find($table_id) : null;

        // Gửi dữ liệu ra view
        return view('User.pages.menu', [
            'layTatCaSanPham' => $layTatCaSanPham,
            'danhSachDanhMuc' => Category::where('status', 1)->get(),
            'table' => $table,
        ]);
    }
    //Trang Giới Thiệu
    public function GioiThieu()
    {
        return view('user.pages.blog');
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
