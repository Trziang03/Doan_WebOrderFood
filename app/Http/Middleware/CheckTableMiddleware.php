<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTableMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {   // 1. Ưu tiên lấy table_id từ session, nếu không có thì lấy từ cookie
        $table_id = session('table_id') ?? $request->cookie('table_id');

        // 2. Nếu không có table_id => redirect về trang quét mã
        if (!$table_id) {
            abort(404, 'Mã QR không hợp lệ hoặc đã hết hạn.');
        }

        // 3. Nếu có trong cookie mà chưa có trong session => gán vào session
        if (!session()->has('table_id')) {
            session(['table_id' => $table_id]);
        }

        // 4. Cho phép tiếp tục xử lý request
        return $next($request);
    }
}
