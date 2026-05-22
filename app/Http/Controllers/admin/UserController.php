<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Thêm Auth vào để lấy ID hiện tại

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Phân trang 10 người mỗi trang
        $users = $query->orderBy('id', 'desc')->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    // Đổi trạng thái 1 <-> 0
    public function toggleStatus($id)
    {
        // 🛑 BẢO VỆ: Kiểm tra nếu ID là tài khoản đang đăng nhập
        if (Auth::id() == $id) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình!');
        }

        $user = User::findOrFail($id);
        $user->status = ($user->status == 1) ? 0 : 1;
        $user->save();

        $msg = $user->status == 1 ? 'Đã mở khóa tài khoản!' : 'Đã khóa tài khoản!';
        return back()->with('success', $msg);
    }

    // Xóa vĩnh viễn (Xóa cả data liên quan)
    public function destroy($id)
    {
        // 🛑 BẢO VỆ: Kiểm tra nếu ID là tài khoản đang đăng nhập
        if (Auth::id() == $id) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        DB::transaction(function () use ($id) {
            // Xóa dữ liệu ở các bảng con trước để tránh lỗi ràng buộc (Foreign Key)
            DB::table('user_interactions')->where('user_id', $id)->delete();
            DB::table('notifications')->where('user_id', $id)->delete();
            
            // Cuối cùng xóa User
            User::where('id', $id)->delete();
        });

        return back()->with('success', 'Đã xóa tài khoản vĩnh viễn khỏi hệ thống!');
    }
}