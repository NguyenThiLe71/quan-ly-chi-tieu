<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserInteraction;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Thêm Auth để kiểm tra ID

class AdminSpamController extends Controller
{
    public function index()
    {
        $oneMinuteAgo = Carbon::now()->subMinute();

        $users = DB::table('users')
            ->where('users.role', 'user') 
            ->leftJoin('user_interactions', 'users.id', '=', 'user_interactions.user_id')
            ->select(
                'users.id', 
                'users.name', 
                'users.email',
                'users.status' // Lấy thêm status để hiển thị nút Mở/Khóa
            )
            ->selectRaw("
                COUNT(user_interactions.id) as total_actions,
                SUM(CASE WHEN user_interactions.created_at >= ? THEN 1 ELSE 0 END) as actions_last_minute,
                SUM(CASE WHEN LOWER(user_interactions.action) LIKE '%create%' THEN 1 ELSE 0 END) as total_create,
                SUM(CASE WHEN LOWER(user_interactions.action) LIKE '%update%' THEN 1 ELSE 0 END) as total_update,
                SUM(CASE WHEN LOWER(user_interactions.action) LIKE '%delete%' THEN 1 ELSE 0 END) as total_delete,
                SUM(CASE WHEN LOWER(user_interactions.action) LIKE '%search%' THEN 1 ELSE 0 END) as total_search
            ", [$oneMinuteAgo])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.status')
            ->orderByDesc('actions_last_minute')
            ->get();

        return view('admin.spam', compact('users'));
    }

    public function warn($userId)
    {
        Notification::create([
            'user_id' => $userId,
            'title' => '⚠️ Cảnh báo hành vi',
            'message' => 'Hệ thống ghi nhận lưu lượng truy cập bất thường từ tài khoản này. Vui lòng tuân thủ điều khoản sử dụng để tránh bị gián đoạn.',
        ]);

        return back()->with('success', 'Đã gửi cảnh báo!');
    }

    // Đổi tên từ lock thành toggleStatus cho đúng bản chất khôi phục được
    public function toggleStatus($userId)
    {
        // 🛑 Chặn Admin tự xử chính mình
        if (Auth::id() == $userId) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của mình tại đây!');
        }

        $user = User::findOrFail($userId);
        
        // Đảo trạng thái: nếu là 1 (active) thì về 0 (lock) và ngược lại
        $user->status = ($user->status == 1) ? 0 : 1;
        $user->save();

        if ($user->status == 0) {
            Notification::create([
                'user_id' => $userId,
                'title' => '🔒 Tài khoản bị khóa',
                'message' => 'Bạn đã bị khóa tài khoản do cố tình vi phạm quy định bảo mật hệ thống.',
            ]);
            $msg = 'Đã khóa tài khoản thành công!';
        } else {
            Notification::create([
                'user_id' => $userId,
                'title' => '🔓 Tài khoản đã mở',
                'message' => 'Tài khoản của bạn đã được quản trị viên khôi phục.',
            ]);
            $msg = 'Đã khôi phục tài khoản thành công!';
        }

        return back()->with('success', $msg);
    }
}