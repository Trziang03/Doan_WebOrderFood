<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Table;
use App\Models\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Encoding\Encoding;
use App\Models\CartItem;
use Illuminate\Support\Facades\Cookie;

class AdminTableController extends Controller
{
    public function index()
    {
        $tables = Table::with('status')->get();
        $statuses = TableStatus::all();

        // Gắn URL QR cho từng bàn
        foreach ($tables as $table) {
            $path = route('user.menu', ['id' => $table->id], false); // /menu?id=1
            $table->qr_url = url($path . '&token=' . $table->token); // domain hiện tại + token
        }

        return view('admin.pages.table', [
            'tables' => $tables,
            'statuses' => $statuses,
        ]);
    }

    private function generateQrForTable(&$table, Carbon $now)
    {
        // Tạo token ngẫu nhiên dài 40 ký tự
        $token = Str::random(40);

        // Tạo đường dẫn URL với ID và token
        $path = route('user.menu', ['id' => $table->id], false);
        $fullUrl = 'https://98cd3a6c9c6a.ngrok-free.app' . $path . '&token=' . $token;

        // Tạo ảnh QR mới
        $builder = new Builder(
            writer: new PngWriter(),
            data: $fullUrl,
            encoding: new Encoding('UTF-8'),
            size: 300,
            margin: 10,
        );

        $filename = 'qr_table_' . $token . '.png';
        $result = $builder->build();
        Storage::disk('public')->put('qr-codes/' . $filename, $result->getString());

        // Xoá ảnh QR cũ nếu có
        if (!empty($table->qr_code) && Storage::disk('public')->exists('qr-codes/' . $table->qr_code)) {
            Storage::disk('public')->delete('qr-codes/' . $table->qr_code);
        }

        // Gán mới vào model
        $table->qr_code = $filename;
        $table->token = $token;
    }

    public function update(Request $request, $id)
    {
        // 1. Lấy bàn cần sửa
        $table = Table::findOrFail($id);

        // 2. Validate
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:10',
                'unique:tables,name,' . $id,
                'regex:/^[a-zA-Z0-9\s]+$/',
            ],
            'table_status_id' => 'required|exists:table_status,id',
        ], [
            'name.required' => 'Tên bàn không được để trống.',
            'name.string' => 'Tên bàn phải là chuỗi.',
            'name.max' => 'Tên bàn không được vượt quá 10 ký tự.',
            'name.unique' => 'Tên bàn đã tồn tại.',
            'name.regex' => 'Tên bàn chỉ được chứa chữ, số và khoảng trắng.',

            'table_status_id.required' => 'Vui lòng chọn trạng thái bàn.',
            'table_status_id.exists' => 'Trạng thái bàn không hợp lệ.',
        ]);

        $now = Carbon::now();

        // 3. Cập nhật dữ liệu cơ bản
        $table->name = $request->name;
        $table->table_status_id = $request->table_status_id;

        // 4. Nếu trạng thái là "Trống" (ID = 1) tạo QR mới
        if ((int) $request->table_status_id === 1) {
            $this->generateQrForTable($table, $now);
        }

        // 5. Nếu admin check vào "Đổi mã QR" → tạo mã mới
        if ($request->has('regen_qr')) {
            $this->generateQrForTable($table, $now);
        }

        // 6. Nếu trạng thái là "Đang dọn bàn" (ID = 3)     tạo token mới
        if ((int) $request->table_status_id === 3) {
            $table->token = Str::random(40);
        }

        if ((int) $request->table_status_id === 1 || (int) $request->table_status_id === 3) {
            // Xoá mã đơn hàng khỏi session
            session()->forget('current_order_code');
        }

        $table->save();

        return redirect()->back()->with('message', 'Cập nhật bàn thành công');
    }

    public function getStatuses()
    {
        $tables = Table::with('status')->get();

        // Tạo URL có kèm token
        $url = route('user.menu', ['id' => $table->id]) . ',token=' . $token;

        $result = $tables->map(function ($table) {
            return [
                'id' => $table->id,
                'status_name' => $table->status->name,
                'status_id' => $table->status_id
            ];
        });

        return response()->json($result);
    }

    public function checkStatus(Request $request)
    {
        $tableId = session('table_id');

        if (!$tableId) {
            return response()->json([
                'status' => 'blocked',
                'message' => 'Không tìm thấy bàn.'
            ]);
        }

        $table = Table::find($tableId);

        if (!$table || $table->table_status_id == 3) {
            $cartItems = CartItem::with('toppings')->where('table_id', $tableId)->get();

            foreach ($cartItems as $item) {
                $item->toppings()->delete(); // Xoá topping
                $item->delete();             // Xoá món chính
            }

            session()->forget(['table_id', 'qr_token', 'current_order_code']);
            Cookie::queue(Cookie::forget('table_id'));

            \Log::info('Session hiện tại sau khi xoá:', session()->all());

            return response()->json([
                'status' => 'blocked',
                'message' => 'Bàn đang được dọn. Giỏ hàng đã bị xoá.'
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name',
            'table_status_id' => 'required|exists:table_status,id',
        ]);

        // 2. Tạo table mới
        $table = new Table();
        $table->name = $request->name;
        $table->table_status_id = $request->table_status_id;
        $table->token = Str::random(40);
        $table->save(); // Lưu trước để có ID

        // 3. Gọi hàm generate QR
        $this->generateQrForTable($table, now());

        // 4. Lưu lại sau khi thêm qr_code và token
        $table->save();

        return redirect()->back()->with('message', 'Thêm bàn mới thành công');
    }


    public function handleQr(Request $request)
    {
        $tableId = $request->query('table_id');

        if (!$tableId || !Table::find($tableId)) {
            return abort(404, 'Bàn không tồn tại');
        }

        // Lưu table_id vào session
        session(['table_id' => $tableId]);

        // Chuyển hướng về trang gọi món (hoặc trang chính)
        return redirect('/'); // có thể đổi thành route của trang thực đơn
    }
    // public function handleQr($id, Request $request)
    // {
    //     $token = $request->query('token');

    //     // Kiểm tra table tồn tại và token đúng
    //     $table = Table::findOrFail($id);

    //     if ($table->token !== $token) {
    //         abort(403, 'Token không hợp lệ.');
    //     }

    //     // Redirect sang route user.menu, truyền theo table_id và token
    //     return redirect()->route('user.menu', [
    //         'table_id' => $id,
    //         'token' => $token
    //     ]);
    // }


    // public function update(Request $request, $id)
    // {
    //     // 1. Lấy bàn cần sửa
    //     $table = Table::findOrFail($id);

    //     // 2. Validate dữ liệu gửi lên
    //     $request->validate([
    //         'name' => 'required|string|max:255|unique:tables,name,' . $id,
    //         'table_status_id' => 'required|exists:table_status,id',
    //     ]);

    //     // 3. Cập nhật thông tin bàn
    //     $table->name = $request->name;
    //     $table->table_status_id = $request->table_status_id;

    //     // 4. Nếu chọn "Đổi mã QR"
    //     if ($request->has('regen_qr')) {
    //         // Tạo lại đường dẫn QR mới
    //         $url = route('order.table', ['id' => $table->id]);

    //         // Tạo QR mới
    //         $builder = new Builder(
    //             writer: new PngWriter(),
    //             data: $url,
    //             size: 300,
    //             margin: 10
    //         );

    //         $result = $builder->build();

    //         // Tạo tên file mới (hoặc giữ nguyên tên cũ)
    //         $filename = 'qr_table_' . $table->id . '_' . Str::random(5) . '.png';

    //         // Xoá file QR cũ nếu có
    //         if ($table->qr_code && Storage::disk('public')->exists('qr-codes/' . $table->qr_code)) {
    //             Storage::disk('public')->delete('qr-codes/' . $table->qr_code);
    //         }

    //         // Lưu QR mới
    //         Storage::disk('public')->put('qr-codes/' . $filename, $result->getString());

    //         // Cập nhật tên file QR
    //         $table->qr_code = $filename;
    //     }

    //     // 5. Lưu thay đổi
    //     $table->save();

    //     return redirect()->back()->with('message', 'Cập nhật bàn thành công');
    // }
}
