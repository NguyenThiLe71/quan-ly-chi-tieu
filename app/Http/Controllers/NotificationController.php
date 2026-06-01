<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Danh sách thông báo (Đã sửa tích hợp phân trang)
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

        // Sắp xếp thông báo mới nhất lên đầu và áp dụng phân trang (10 mục/trang)
        // Thay vì ->get(), ta đổi thành ->paginate(10) và giữ lại các tham số lọc trên URL (nếu có)
        $notifications = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead($id)
    {
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
            ->where('is_read', 0)
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