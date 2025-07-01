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
    }
