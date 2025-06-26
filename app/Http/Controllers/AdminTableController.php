<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Table;
use App\Models\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

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
            'access_limit' => 'required|integer|min:1',
        ]);
       // 1. Tạo token ngẫu nhiên
        $token = Str::random(32);


        // Tạo bàn ăn mới (chưa có QR)
        $table = new Table();
        $table->name = $request->name;
        $table->table_status_id = $request->table_status_id;
        $table->token = $token;
        $table->access_limit = $request->access_limit;
        $table->access_count = 0;
        $table->qr_refreshed_at = Carbon::now();
        $table->save(); // để lấy ID trước khi đặt tên file QR


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
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tables,name,' . $id,
                'regex:/^[a-zA-Z0-9\s]+$/',], 
            'table_status_id' => 'required|exists:table_status,id',
            'access_limit' => 'required|integer|min:1',
        ],[
            'name.regex' => 'Tên bàn không được chứa ký tự đặc biệt.',
        ]);


        // 3. Cập nhật thông tin bàn
        $table->name = $request->name;
        $table->table_status_id = $request->table_status_id;
        $table->access_limit = $request->access_limit;

        $now = Carbon::now();//lấy 
        $shouldRefreshQR = false;

        // Trường hợp tự động cập nhật nếu trạng thái = 2 và đã quá 15 phút
        if ((int)$request->table_status_id === 2 // đang sử dụng
        ) {
            $last = $table->qr_refreshed_at ?? $table->updated_at;
            //if ($now->diffInSeconds($last) >= 30) ////refresh trong 30 giây
            //if ($now->diffInMinutes($last) >= 15) ////refresh sau 15 phút
            if ($now->diffInSeconds($last) >= 10) {
                $shouldRefreshQR = true;
            }
        }

        //làm mới QR nếu đủ điều kiện trạng thái đang sử dụng (id=2)
        if ($shouldRefreshQR){

            $token = Str::random(32);
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


             // Xoá file QR cũ nếu có
             if ($table->qr_code && Storage::disk('public')->exists('qr-codes/' . $table->qr_code)) {
                Storage::disk('public')->delete('qr-codes/' . $table->qr_code);
            }

            // Lưu QR mới
            $filename = 'qr_table_' . $table->id . '_' . Str::random(5) . '.png';
            Storage::disk('public')->put('qr-codes/' . $filename, $result->getString());


            // Cập nhật DB
            $table->qr_code = $filename;
            $table->token = $token;
            $table->access_count = 0;
            $table->qr_refreshed_at = $now;
            $table->save();
        }

        // Nếu trạng thái là "Trống" (id = 1) thì tạo lại QR và token
        if ((int)$request->table_status_id === 1) {

            // Reset access_count nếu chuyển về trạng thái Trống (id = 1)
            $table->access_count = 0;

            $token = Str::random(32);
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
            $table->token = $token;
            $table->access_count = 0; // reset lượt truy cập
            $table->qr_refreshed_at = $now;
        }
        // 5. Lưu thay đổi
        $table->save();

        return redirect()->back()->with('message', 'Cập nhật bàn thành công');
    }


    public function checkin(Request $request)
    {
        $token = $request->query('token');
        $table = Table::where('token', $token)->first();

        if (!$table) {
            return abort(404, 'Không tìm thấy bàn');
        }

        // Nếu bàn đang dọn dẹp → block
        if ($table->table_status_id == 3) {
            return response()->view('table.blocked');
        }

        // Nếu đã quá số lượt truy cập
        if ($table->access_count >= $table->access_limit) {
            return response()->view('table.too-many', ['table' => $table]);
        }
        
        // Tăng lượt truy cập
        $table->access_count += 1;
        $table->save();

        // Tiếp tục truy cập menu gọi món
        return view('order.menu', ['table' => $table]);
    }


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