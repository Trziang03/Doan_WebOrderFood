<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class AdminStaffController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $staffs = Staff::where('role', 'NV')
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%");
                });
            })
            ->get();

        return view('admin.pages.staff', ['staffs' => $staffs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'phone' => 'nullable|string|max:10',
            'email' => [
                'required',
                'email',
                'regex:/^[\w.+\-]+@gmail\.com$/i',
                'unique:users,email'
            ],
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:QL,NV',
            'status' => 'required|boolean',
        ], [
            'email.regex' => 'Email phải có định dạng đúng và thuộc @gmail.com',
        ]);

        Staff::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Thêm nhân viên thành công!');
    }

    public function edit($id)
    {
        return response()->json(Staff::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'full_name' => 'required',
            'gender' => 'required|in:Nam,Nữ',
            'date_of_birth' => 'required|date|before:2010-01-01',
            'phone' => 'required|regex:/^0[0-9]{9}$/|unique:users,phone,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:QL,NV',
            'status' => 'required|in:1,0',
        ]);

        $user = Staff::findOrFail($id);
        $user->update($request->except('_token'));

        return response()->json(['success' => true]);
    }
}



