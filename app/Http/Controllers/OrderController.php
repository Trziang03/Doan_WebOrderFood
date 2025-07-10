<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;

class OrderController extends Controller
{
    public function index($order_code)
    {
        $order = Order::with('orderStatus')
            ->where('order_code', $order_code)
            ->first();

        if (!$order) {
            return redirect('/404')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // 🔒 Kiểm tra bảo mật: Đơn hàng này có thuộc về bàn hiện tại không?
        $currentTableId = session('table_id');
        if (!$currentTableId || $order->table_id != $currentTableId) {
            return redirect('/404')->with('error', 'Bạn không có quyền xem đơn hàng này.');
        }

        // Phân trang các món trong đơn
        $orderItems = OrderItem::with(['product', 'size', 'toppings'])
            ->where('order_id', $order->id)
            ->paginate(3);

        return view('User.profile.payment', [
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }


    public function showQR()
    {
        $tableId = session('table_id');

        if (!$tableId) {
            return redirect()->back()->with('error', 'Chưa quét mã QR.');
        }

        $table = Table::find($tableId);

        if (!$table) {
            return redirect()->back()->with('error', 'Bàn không tồn tại.');
        }

        return view('User.pages.qr_info', ['table' => $table]);
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method_id' => 'required|in:1,2', // 1 = Tiền mặt, 2 = Chuyển khoản
        ]);

        $order = Order::findOrFail($id);
        $order->payment_method_id = $request->payment_method_id;

        // Luôn chuyển trạng thái đơn về 3 (Chờ thanh toán)
        $order->order_status_id = 3;

        $order->save();

        return redirect()->back()->with('success', 'Đã chọn phương thức thanh toán!');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra nếu đơn đã quá 3 phút hoặc không còn ở trạng thái có thể huỷ
        if (now()->diffInMinutes($order->created_at) >= 3 || $order->order_status_id != 0) {
            return redirect()->back()->with('error', 'Không thể huỷ đơn hàng vào lúc này.');
        }

        // Đặt lại trạng thái đơn hàng là "Đã hủy"
        $order->order_status_id = 5;
        $order->save();

        // Xoá session đơn hàng hiện tại
        session()->forget('current_order_code');

        // Chuyển hướng về trang menu
        return redirect()->route('user.menu')->with('success', 'Đơn hàng đã được hủy thành công.');
    }
}
