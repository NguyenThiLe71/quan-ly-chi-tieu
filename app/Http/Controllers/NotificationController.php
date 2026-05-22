<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Danh sách thông báo
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // 🔍 Lọc: đã đọc / chưa đọc
        if ($request->filled('status')) {
            if ($request->status == 'read') {
                $query->where('is_read', 1);
            } elseif ($request->status == 'unread') {
                $query->where('is_read', 0);
            }
        }

        // Sắp xếp thông báo mới nhất lên đầu
       
$notifications = $query->orderBy('id', 'desc')->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        // Sử dụng findOrFail để tự động trả về lỗi 404 nếu không tìm thấy hoặc không thuộc quyền sở hữu
        $noti = Notification::where('user_id', Auth::id())->findOrFail($id);
        $noti->update(['is_read' => 1]);

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    /**
     * Đánh dấu tất cả thông báo của user này là đã đọc
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', 0) // Chỉ cập nhật những cái chưa đọc để tối ưu hiệu suất
            ->update(['is_read' => 1]);

        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc.');
    }

    /**
     * Xóa 1 thông báo
     */
    public function destroy($id)
    {
        $noti = Notification::where('user_id', Auth::id())->findOrFail($id);
        $noti->delete();

        return back()->with('success', 'Đã xóa thông báo.');
    }
}