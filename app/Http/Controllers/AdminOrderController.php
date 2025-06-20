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

        $orders = Order::with(['table', 'paymentMethod', 'status', 'orderItems.product', 'orderItems.size'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
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
                            // Month only
                            $monthCarbon = Carbon::createFromDate(now()->year, $num, 1);
                            $monthOnly = [
                                $monthCarbon->startOfMonth()->format('Y-m-d'),
                                $monthCarbon->endOfMonth()->format('Y-m-d'),
                            ];
                        } elseif ($num >= 1 && $num <= 31) {
                            // Day only (rare use case)
                            $dayOnly = $num;
                        }
                    }

                    // Các điều kiện tìm kiếm chính
                    $q->where('order_code', 'like', "%$keyword%")
                        ->orWhere('total_price', 'like', "%$keyword%")
                        ->orWhereHas('table', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%$keyword%");
                    })
                        ->orWhereHas('status', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%$keyword%");
                    })
                        ->orWhereHas('orderItems.product', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%$keyword%");
                    });

                    // Các điều kiện về ngày
                    if ($dateExact) {
                        $q->orWhereDate('created_at', $dateExact);
                    }

                    if ($dayMonth) {
                        $q->orWhereBetween('created_at', $dayMonth);
                    }

                    if ($monthYear) {
                        $q->orWhereBetween('created_at', $monthYear);
                    }

                    if ($yearOnly) {
                        $q->orWhereBetween('created_at', $yearOnly);
                    }

                    if ($monthOnly) {
                        $q->orWhereBetween('created_at', $monthOnly);
                    }

                    if ($dayOnly) {
                        $q->orWhereRaw('DAY(created_at) = ?', [$dayOnly]);
                    }
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.pages.order', ['orders' => $orders]);
    }


    public function changeStatus($id)
    {
        $order = Order::findOrFail($id);

        if ($order->order_status_id < 3) {
            $order->order_status_id += 1;
            $order->save();
        }

        return redirect()->route('admin.order');
    }

    public function ajaxDetail($id)
    {
        $order = Order::with([
            'table',
            'paymentMethod',
            'status',
            'orderItems.product',
            'orderItems.size',
            'orderItems.toppings',
            'orderItems.orderItemToppings'
        ])->findOrFail($id);

        return view('admin.pages.orderdetail', compact('order'));
    }
    public function update(Request $request, $id)
    {

    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code' => Order::generateTimestamp(),
                'table_id' => $request->table_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'total_price' => 0,
                'payment_method_id' => $request->payment_method_id,
                'user_id' => $request->user_id ?? null,
                'voucher_id' => $request->voucher_id ?? null,
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
                        'topping_price' => $topping->price,
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
