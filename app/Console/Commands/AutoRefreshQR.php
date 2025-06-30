<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Table;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AutoRefreshQR extends Command
{
    protected $signature = 'qr:auto-refresh';
    protected $description = 'Tự động làm mới QR cho các bàn đang sử dụng';

    public function handle()
    {
        \Log::info('[QR Scheduler] Lệnh qr:auto-refresh chạy lúc: ' . now());
        $now = Carbon::now();
        $refreshAfter = config('qr.refresh_after_seconds', 30);
        $tables = Table::where('table_status_id', 2)->get();

        foreach ($tables as $table) {
            $last = $table->qr_refreshed_at ?? $table->updated_at;

            if ($now->diffInSeconds($last) >= $refreshAfter) {
                $this->info("Đang làm mới QR cho bàn {$table->id}");

                // Sinh token mới + QR mới
                $token = Str::random(32);
                $url = route('order.table', ['id' => $table->id]);

                $builder = new Builder(
                    writer: new PngWriter(),
                    data: $url,
                    size: 300,
                    margin: 10
                );

                $result = $builder->build();

                // Xoá QR cũ nếu có
                if ($table->qr_code && Storage::disk('public')->exists('qr-codes/' . $table->qr_code)) {
                    Storage::disk('public')->delete('qr-codes/' . $table->qr_code);
                }

                $filename = 'qr_table_' . $table->id . '_' . Str::random(5) . '.png';
                Storage::disk('public')->put('qr-codes/' . $filename, $result->getString());

                $table->qr_code = $filename;
                $table->token = $token;
                $table->qr_refreshed_at = $now;
                $table->access_count = 0;
                $table->save();
            }
        }

        $this->info("Hoàn tất làm mới QR.");
        return 0;
    }
}

