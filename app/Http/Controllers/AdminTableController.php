<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class AdminTableController extends Controller
{
    public function index()
    {
        $tables = Table::with('status')->get();
        $statuses = TableStatus::all();

        return view('admin.pages.table', [
            'tables' => $tables,
            'statuses' => $statuses,
        ]);
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name',
            'table_status_id' => 'required|exists:table_status,id',
        ]);

        // Tạo bàn ăn mới (chưa có QR)
        $table = new Table();
        $table->name = $request->name;
        $table->table_status_id = $request->table_status_id;
        $table->save(); // để có id bàn

        // 2. Tạo link cần encode QR
        $url = route('order.table', ['id' => $table->id]); // hoặc URL tuỳ bạn

        // 3. Tạo QR code bằng cách dùng new Builder
        $builder = new Builder(
            writer: new PngWriter(),
            data: $url,
            size: 300,
            margin: 10
        );

        // 4. Tạo mã QR
        $result = $builder->build();

        // 5. Lưu file ảnh QR vào thư mục storage/app/public/qr-codes
        $filename = 'qr_table_' . $table->id . '.png';
        Storage::disk('public')->put('qr-codes/' . $filename, $result->getString());

        // 6. Lưu tên file QR (nếu có cột qr_code trong bảng)
        $table->qr_code = $filename;
        $table->save();

        return redirect()->back()->with('message', 'Thêm bàn mới thành công');
    }

    public function update(Request $request, $id)
    {
        // 1. Lấy bàn cần sửa
        $table = Table::findOrFail($id);

        // 2. Validate dữ liệu gửi lên
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $id,
            'table_status_id' => 'required|exists:table_status,id',
        ]);

        // 3. Cập nhật thông tin bàn
        $table->name = $request->name;
        $table->table_status_id = $request->table_status_id;

        // 4. Nếu chọn "Đổi mã QR"
        if ($request->has('regen_qr')) {
            // Tạo lại đường dẫn QR mới
            $url = route('order.table', ['id' => $table->id]);

            // Tạo QR mới
            $builder = new Builder(
                writer: new PngWriter(),
                data: $url,
                size: 300,
                margin: 10
            );

            $result = $builder->build();

            // Tạo tên file mới (hoặc giữ nguyên tên cũ)
            $filename = 'qr_table_' . $table->id . '_' . Str::random(5) . '.png';

            // Xoá file QR cũ nếu có
            if ($table->qr_code && Storage::disk('public')->exists('qr-codes/' . $table->qr_code)) {
                Storage::disk('public')->delete('qr-codes/' . $table->qr_code);
            }

            // Lưu QR mới
            Storage::disk('public')->put('qr-codes/' . $filename, $result->getString());

            // Cập nhật tên file QR
            $table->qr_code = $filename;
        }

        // 5. Lưu thay đổi
        $table->save();

        return redirect()->back()->with('message', 'Cập nhật bàn thành công');
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

}
