<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use App\Models\Product;
use App\Models\Size;
use App\Models\Topping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminOrderController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $pendingCount = Order::whereHas('orderStatus', function ($q) {
            $q->where('name', 'Xác nhận');
        })->count();

        $orders = null;
        $ordersByStatus = [];
        $statusCounts = [];

        if ($keyword) {
            // Tìm kiếm theo keyword hoặc ngày/tháng
            $parsed = $this->parseKeywordDateRanges($keyword);

            $orders = Order::with(['table', 'paymentMethod', 'orderItems.product', 'orderItems.size'])
                ->where(function ($q) use ($keyword, $parsed) {
                    $q->where('order_code', 'like', "%$keyword%")
                        ->orWhere('total_price', 'like', "%$keyword%")
                        ->orWhereHas('table', fn($q2) => $q2->where('name', 'like', "%$keyword%"))
                        ->orWhereHas('orderItems.product', fn($q2) => $q2->where('name', 'like', "%$keyword%"));

                    if ($parsed['dateExact']) {
                        $q->orWhereDate('created_at', $parsed['dateExact']);
                    }

                    foreach (['monthYear', 'dayMonth', 'yearOnly', 'monthOnly'] as $rangeKey) {
                        if ($parsed[$rangeKey]) {
                            $q->orWhereBetween('created_at', $parsed[$rangeKey]);
                        }
                    }

                    if ($parsed['dayOnly']) {
                        $q->orWhereRaw('DAY(created_at) = ?', [$parsed['dayOnly']]);
                    }
                })
                ->orderByDesc('id')
                ->paginate(7)
                ->appends(['keyword' => $keyword]); // giữ lại keyword trên thanh phân trang
        } else {
            // Khi không tìm kiếm, xử lý hiển thị theo tabs trạng thái
            $orderTabs = [
                'xacnhan' => 0,
                'dangchuanbi' => 1,
                'daphucvu' => 2,
                'chothanhtoan' => 3,
                'dathanhtoan' => 4,
                'dahuy' => 5,
            ];

            foreach ($orderTabs as $key => $statusId) {
                $ordersByStatus[$key] = Order::with(['table', 'orderStatus', 'orderItems.product'])
                    ->where('order_status_id', $statusId)
                    ->orderByDesc('updated_at')
                    ->paginate(7, ['*'], $key); // mỗi tab có phân trang riêng (ví dụ: ?xacnhan=2)

                $statusCounts[$key] = $ordersByStatus[$key]->total();
            }
        }

        return view('admin.pages.order', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'statusCounts' => $statusCounts,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }

    /**
     * Phân tích từ khóa ngày tháng để tìm kiếm linh hoạt
     */
    private function parseKeywordDateRanges($keyword)
    {
        $dateExact = null;
        $monthYear = null;
        $yearOnly = null;
        $dayMonth = null;
        $monthOnly = null;
        $dayOnly = null;

        try {
            $dateExact = Carbon::createFromFormat('d/m/Y', $keyword)->format('Y-m-d');
        } catch (\Exception $e) {
        }

        try {
            $monthYearCarbon = Carbon::createFromFormat('m/Y', $keyword);
            $monthYear = [
                $monthYearCarbon->copy()->startOfMonth()->format('Y-m-d'),
                $monthYearCarbon->copy()->endOfMonth()->format('Y-m-d'),
            ];
        } catch (\Exception $e) {
        }

        if (preg_match('/^\d{4}$/', $keyword)) {
            $yearOnly = [
                Carbon::createFromDate($keyword, 1, 1)->format('Y-m-d'),
                Carbon::createFromDate($keyword, 12, 31)->format('Y-m-d'),
            ];
        }

        try {
            $dayMonthCarbon = Carbon::createFromFormat('d/m', $keyword);
            $dayMonth = [
                Carbon::createFromDate(now()->year, $dayMonthCarbon->month, $dayMonthCarbon->day)->startOfDay()->format('Y-m-d'),
                Carbon::createFromDate(now()->year, $dayMonthCarbon->month, $dayMonthCarbon->day)->endOfDay()->format('Y-m-d'),
            ];
        } catch (\Exception $e) {
        }

        if (preg_match('/^\d{1,2}$/', $keyword)) {
            $num = intval($keyword);
            if ($num >= 1 && $num <= 12) {
                $monthCarbon = Carbon::createFromDate(now()->year, $num, 1);
                $monthOnly = [
                    $monthCarbon->startOfMonth()->format('Y-m-d'),
                    $monthCarbon->endOfMonth()->format('Y-m-d'),
                ];
            } elseif ($num >= 1 && $num <= 31) {
                $dayOnly = $num;
            }
        }

        return compact('dateExact', 'monthYear', 'yearOnly', 'dayMonth', 'monthOnly', 'dayOnly');
    }

    /**
     * Phân tích từ khóa ngày tháng để tìm kiếm linh hoạt
     */
    public function changeStatus($id)
    {
        $order = Order::findOrFail($id);

        if ($order->order_status_id < 4) {
            $order->order_status_id += 1;
            $order->save();
        }

        $tab = request('tab', 'xacnhan');
        return redirect()->back()->with('tab', $tab);
    }

    public function ajaxDetail($id)
    {
        try {
            $order = Order::with([
                'table',
                'paymentMethod',
                'orderStatus',
                'orderItems.product',
                'orderItems.size',
                'orderItems.toppings',
                'orderItems.orderItemToppings'
            ])->findOrFail($id);

            return view('admin.pages.orderdetail', compact('order'));

        } catch (\Exception $e) {
            return response('<b>Lỗi:</b> ' . $e->getMessage(), 500);
        }
    }
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        foreach ($order->orderItems as $item) {
            // Xoá các topping của từng item
            $item->orderItemToppings()->delete();
        }

        // Xoá các item của đơn
        $order->orderItems()->delete();

        // Xoá đơn hàng
        $order->delete();

        return redirect()->back()->with('success', 'Đơn hàng đã được xoá thành công.');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code' => Order::generateTimestamp(),
                'table_id' => $request->table_id,
                'total_price' => 0,
                'payment_method_id' => $request->payment_method_id,
                'order_status_id' => $request->order_status_id,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['food_id']);
                $quantity = $item['quantity'];
                $sizeId = $item['size_id'] ?? null;
                $note = $item['note'] ?? null;
                $toppingDataList = $item['toppings'] ?? [];

                $price = $product->price;

                if ($sizeId) {
                    $size = Size::findOrFail($sizeId);
                    $price += $size->price;
                }

                $toppingTotal = 0;

                foreach ($toppingDataList as $toppingData) {
                    $topping = Topping::findOrFail($toppingData['topping_id']);
                    $toppingTotal += $topping->price * ($toppingData['quantity'] ?? 1);
                }

                $itemTotal = ($price + $toppingTotal) * $quantity;
                $total += $itemTotal;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'size_id' => $sizeId,
                    'total_price' => $itemTotal,
                    'note' => $note,
                ]);

                foreach ($toppingDataList as $toppingData) {
                    $topping = Topping::findOrFail($toppingData['topping_id']);
                    OrderItemTopping::create([
                        'order_item_id' => $orderItem->id,
                        'topping_id' => $topping->id,
                        'quantity' => $toppingData['quantity'] ?? 1,
                        'price' => $topping->price,
                        'note' => $toppingData['note'] ?? null,
                    ]);
                }
            }

            $order->update(['total_price' => $total]);

            DB::commit();

            return response()->json([
                'message' => 'Tạo đơn hàng thành công!',
                'order_id' => $order->id,
                'order_code' => $order->order_code
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}
