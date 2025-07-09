<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Nếu không đăng nhập
        if (!Session::has('user_id')) {
            // Chỉ cho phép truy cập trang home
            if ($request->routeIs('home')) {
                return $next($request);
            }
            // Chuyển hướng về trang đăng nhập cho các trang khác
            return redirect()->route('login');
        }

        $userRole = Session::get('role');
        
        // Kiểm tra vai trò của user
        if ($userRole) {
            // Nếu không có role được chỉ định hoặc user có role phù hợp
            if (empty($roles) || in_array($userRole, $roles)) {
                return $next($request);
            }
        }

        // Nếu không có quyền, chuyển hướng về trang chủ
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
    }
}
