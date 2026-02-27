<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        $user = Auth::user();

        // Kiểm tra tài khoản bị khóa
        if ($user->status == 0) {
            Auth::logout();
            session()->flush();
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa!');
        }

        // Kiểm tra role
        if ($user->role !== $role) {
            abort(403, 'Bạn không có quyền truy cập trang này!');
        }

        return $next($request);
    }
}