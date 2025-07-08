<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminStaticController extends Controller
{
    public function index()
    {
        $sum = [];
        $count = [];
        $year = Carbon::now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $sum[$month - 1] = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('order_status_id', 0) //nhớ đổi status đơn hàng thành đã thanh toán
                ->sum('total_price');

            $count[$month - 1] = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('order_status_id', 0)
                ->count();
        }

        return view('admin.pages.statistical', ['sum' => $sum, 'count' => $count]);
    }

    public static function listData($fromDate)
    {
        return DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                DB::raw('DATE(orders.created_at) AS created_at'),
                DB::raw('SUM(orders.total_price) AS total_price'),
                DB::raw('SUM(order_items.quantity) AS total_quantity_product'),
                DB::raw('COUNT(DISTINCT orders.id) AS total_quantity_revenue')
            )
            ->where('orders.order_status_id', 0)
            ->whereDate('orders.created_at', '>=', $fromDate)
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->get();
    }

    public function statistics(Request $request)
    {
        $type = $request->statistic_type;
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $dateRanges = [
            '7ngay' => $now->copy()->subDays(7)->toDateString(),
            'thangnay' => $now->copy()->startOfMonth()->toDateString(),
            'thangtruoc' => $now->copy()->subMonth()->startOfMonth()->toDateString(),
            '365ngay' => $now->copy()->subDays(365)->toDateString()
        ];

        $fromDate = $dateRanges[$type] ?? $dateRanges['365ngay'];

        $orders = self::listData($fromDate);

        $chart_data = [];
        $total_profit = 0;
        $total_orders = 0;

        foreach ($orders as $order) {
            $chart_data[] = [
                'created_at' => $order->created_at,
                'total_quantity_revenue' => $order->total_quantity_revenue,
                'total_price' => $order->total_price,
                'total_quantity_product' => $order->total_quantity_product,
            ];

            $total_profit += $order->total_price;
            $total_orders += $order->total_quantity_revenue;
        }

        return response()->json(['chart_data' => $chart_data]);
    }
}
