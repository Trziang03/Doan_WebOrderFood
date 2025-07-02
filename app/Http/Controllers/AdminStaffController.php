<?php

    namespace App\Http\Controllers;

    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use App\Models\About;
    use App\Models\Staff;

    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\Rule;


class AdminStaffController extends Controller
{
    public function index()
    {
        //lọc nhân viên có role là 'NV'
        $staffs = Staff::where('role', 'NV')->get();

        return view('admin.pages.staff', compact('staffs'));
    }
    public function Profile($id)
    {
        $staff = Staff::findOrFail($id);
        return view('admin.pages.editstaff',compact('staff'))->with('user', Auth::user());
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'phone')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'gender' => 'required|string',
            'birthday' => 'required|date|before_or_equal:today',
            'role' => ['required', Rule::in(['NV', 'QL'])], // ví dụ dùng NV, QL
        ], [
            // thông báo lỗi giống như bạn đã viết
            'username.required' => 'Vui lòng nhập username',
            'username.unique' => 'Username đã tồn tại',
            'phone.unique' => 'Số điện thoại đã được sử dụng',
            'email.unique' => 'Email đã được sử dụng',
            //...
        ]);

        // ✅ Lấy user theo id nhân viên
        $user = Staff::findOrFail($id);

        // ✅ Cập nhật thông tin
        $user->username = $validated['username'];
        $user->full_name = $validated['fullname'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->gender = $validated['gender'] == 'male' ? 'Nam' : 'Nữ';
        $user->date_of_birth = $validated['birthday'];
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('admin.staff')->with('success', 'Cập nhật nhân viên thành công!');
    }
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:staff,username',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|digits_between:9,11',
            'password' => 'required|confirmed|min:6',
        ]);

        $staff = new User();
        $staff->username = $request->username;
        $staff->full_name = $request->fullname;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        $staff->role = 'NV'; // mặc định là nhân viên
        $staff->gender = 'Nam'; // hoặc null nếu không nhập
        $staff->date_of_birth = null;
        $staff->password = Hash::make($request->password);
        $staff->image = 'default-avatar.png'; // hoặc null

        $staff->save();

        return redirect()->route('admin.staff')->with('success', 'Thêm nhân viên thành công!');
    }
    public function ajaxStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'username' => 'required|unique:staff,username',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|digits_between:9,11',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $staff = new Staff();
        $staff->username = $request->username;
        $staff->full_name = $request->fullname;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        $staff->role = 'NV';
        $staff->gender = 'Nam';
        $staff->date_of_birth = null;
        $staff->password = \Hash::make($request->password);
        $staff->image = 'default-avatar.png';

        $staff->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm nhân viên thành công!'
        ]);
    }

}
