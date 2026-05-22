<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSalaryRequirement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (Auth::check()) {
            
            /** 
             * 2. Kiểm tra cột balance (số dư/lương)
             * Dựa trên image_a3015d.png, balance mặc định là 0.00
             * Nếu balance <= 0, nghĩa là người dùng mới chưa thiết lập ngân sách ban đầu
             */
            if (Auth::user()->balance <= 0) {
                
                /**
                 * 3. Chặn truy cập và quay lại Dashboard
                 * Gửi kèm session 'need_salary' để hiển thị thông báo Soft UI
                 * Sửa 'home' thành 'dashboard' để khớp với routes/web.php của ông
                 */
                return redirect()->route('dashboard')->with('need_salary', 'Vui lòng nhập lương trước khi thực hiện giao dịch!');
            }
        }

        // Nếu đã có số dư (balance > 0), cho phép tiếp tục vào trang giao dịch
        return $next($request);
    }
}