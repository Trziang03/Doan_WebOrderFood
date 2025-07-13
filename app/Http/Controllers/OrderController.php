<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // public function index($order_code)
    // {
    //     $order = Order::with('orderStatus')
    //         ->where('order_code', $order_code)
    //         ->first();

    //     if (!$order) {
    //         \Log::warning("Không tìm thấy đơn hàng với mã: " . $order_code);
    //         return redirect('/404')->with('error', 'Không tìm thấy đơn hàng.');
    //     }

    //     // Kiểm tra bảo mật: Đơn hàng này có thuộc về bàn hiện tại không?
    //     $currentTableId = session('table_id');
    //     if (!$currentTableId || $order->table_id != $currentTableId) {
    //         return redirect('/404')->with('error', 'Bạn không có quyền xem đơn hàng này.');
    //     }

    //     // Phân trang các món trong đơn
    //     $orderItems = OrderItem::with(['product', 'size', 'toppings'])
    //         ->where('order_id', $order->id)
    //         ->paginate(3);

    //     return view('User.profile.payment', [
    //         'order' => $order,
    //         'orderItems' => $orderItems,
    //     ]);
    // }

    public function index($order_code)
    {
        // Tìm đơn hàng theo mã
        $order = Order::with('orderStatus')
            ->where('order_code', $order_code)
            ->first();

        if (!$order) {
            \Log::warning("Không tìm thấy đơn hàng với mã: " . $order_code);
            return redirect('/404')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Kiểm tra đơn hàng có thuộc về bàn hiện tại không
        $currentTableId = session('table_id');
        if (!$currentTableId || $order->table_id != $currentTableId) {
            return redirect('/404')->with('error', 'Bạn không có quyền xem đơn hàng này.');
        }

        // Lấy danh sách món trong đơn đang xem
        $orderItems = OrderItem::with(['product', 'size', 'toppings'])
            ->where('order_id', $order->id)
            ->paginate(4);

        return view('User.profile.paymentdetail', [
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function listOrders()
    {
        $tableId = session('table_id');

        if (!$tableId) {
            return redirect('/404')->with('error', 'Không xác định được bàn.');
        }

        // Lấy thời gian đơn hàng đầu tiên sau khi bàn được bật lại (khách mới vừa quét mã)
        $latestResetTime = Table::where('id', $tableId)->value('updated_at');

        $orders = Order::with(['orderStatus', 'orderItems'])
            ->where('table_id', $tableId)
            ->where('order_status_id', '!=', 5) // Không hiển thị đơn đã hủy
            ->where('created_at', '>=', $latestResetTime) // Chỉ lấy đơn hàng từ phiên hiện tại
            ->orderByDesc('created_at')
            ->get();

        $latestOrder = $orders->first();

        $unpaidOrders = $orders->filter(fn($o) => $o->order_status_id == 2);

        return view('User.profile.payment', [
            'orders' => $orders,
            'latestOrder' => $latestOrder,
            'unpaidOrders' => $unpaidOrders,
        ]);
    }

    public function payAll(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|in:1,2',
        ]);

        $tableId = session('table_id');

        $orders = Order::where('table_id', $tableId)
            ->where('order_status_id', 2) // Đã phục vụ
            ->get();

        foreach ($orders as $order) {
            $order->payment_method_id = $request->payment_method_id;
            $order->order_status_id = 3; // Đã chọn phương thức, chờ xác nhận
            $order->save();
        }

        return back()->with('message', 'Đã chọn phương thức thanh toán cho tất cả đơn.');
    }

    public function getOrderActionsHtml()
    {
        $tableId = session('table_id');
        if (!$tableId)
            return '';

        $latestResetTime = Table::where('id', $tableId)->value('updated_at');

        $orders = Order::with(['orderStatus', 'orderItems'])
            ->where('table_id', $tableId)
            ->where('order_status_id', '!=', 5)
            ->where('created_at', '>=', $latestResetTime)
            ->orderByDesc('created_at')
            ->get();

        $latestOrder = $orders->first();
        $unpaidOrders = $orders->filter(fn($o) => $o->order_status_id == 2);

        return view('User.partials.order_actions', [
            'order' => $latestOrder,
            'orders' => $orders,
            'unpaidOrders' => $unpaidOrders,
        ]);
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

        return redirect()->route('user.payment', ['order_code' => $order->order_code])
            ->with('message', 'Đã chọn phương thức thanh toán!');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra nếu đơn đã quá 3 phút hoặc không còn ở trạng thái có thể huỷ
        if (now()->diffInMinutes($order->created_at) >= 6 || $order->order_status_id != 0) {
            return redirect()->back()->with('error', 'Không thể huỷ đơn hàng vào lúc này.');
        }

        // Đặt lại trạng thái đơn hàng là "Đã hủy"
        $order->order_status_id = 5;
        $order->save();

        // Chuyển hướng về trang menu
        return redirect()->route('user.payment')->with('message', 'Đơn hàng đã được hủy thành công.');
    }

    public function cancelAll()
{
    $orders = Order::where('order_status_id', 0)->get();

    $cancelable = $orders->filter(function ($order) {
        $diff = now()->diffInMinutes($order->created_at);
        echo "Đơn {$order->id} - thời gian tạo: {$order->created_at}, cách hiện tại: {$diff} phút<br>";
        return $diff < 6;
    });

    if ($cancelable->isEmpty()) {
        echo "⛔ Không có đơn hàng nào hợp lệ để huỷ.<br>";
        return;
    }

    foreach ($cancelable as $order) {
        echo "✅ Huỷ đơn hàng: {$order->id}<br>";
        $order->order_status_id = 5;
        $order->save();
    }

    session()->forget('current_order_code');
    echo "<br>✔️ Đã huỷ tất cả đơn: " . $cancelable->pluck('id')->implode(', ');
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
}
