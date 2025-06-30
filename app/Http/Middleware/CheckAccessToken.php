<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Table;

class CheckAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('token');

        if (!$token) {
            return abort(403, 'Thiếu token truy cập.');
        }

        $table = Table::where('access_token', $token)->first();

        if (!$table) {
            return abort(403, 'Token không hợp lệ hoặc đã bị thay đổi.');
        }
        if (!$table || $table->token_expires_at < now()) {
            return abort(403, 'Token đã hết hạn hoặc không hợp lệ.');
        }
        $request->attributes->add(['table' => $table]);

        return $next($request);
    }
}
