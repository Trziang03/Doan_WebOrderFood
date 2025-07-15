<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\Support\Responsable;

class RevenueExport implements FromCollection, WithHeadings
{
    protected $fromDate;

    public function __construct($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    public function collection()
    {
        return Order::whereDate('created_at', $this->fromDate)
            ->where('order_status_id', 4)
            ->select('id','order_code', 'total_price', 'created_at')
            ->get()
            ->map(function ($order) {
                return [
                    'ID đơn' => $order->id,
                    'Mã khách' => $order->order_code,
                    'Tổng tiền' => $order->total_price,
                    'Thời gian' => $order->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return ['ID đơn', 'Mã đơn', 'Tổng tiền', 'Thời gian'];
    }
}

