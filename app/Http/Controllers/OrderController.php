<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\Table;
use App\Models\VoucherUser;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $table_id = session('table_id');

        $order = Order::with('orderStatus') // chỉ lấy status ở đây
            ->where('table_id', $table_id)
            ->latest()
            ->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Tách phân trang cho orderItems riêng
        $orderItems = OrderItem::with(['product', 'size', 'toppings'])
            ->where('order_id', $order->id)
            ->paginate(3);

        return view('User.profile.payment', [
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function orderByTable($id)
    {
        $table = Table::with('status')->find($id);

        if (!$table) {
            abort(404, 'Bàn không tồn tại');
        }

        // Gửi thông tin bàn đến view gọi món
        return view('client.order', [
            'table' => $table,

        ]);
    }

}
