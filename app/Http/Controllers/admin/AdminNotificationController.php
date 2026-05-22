<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification; // Nhớ tạo Model nếu chưa có
use App\Models\User;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index()
    {
        // Lấy danh sách thông báo, kèm thông tin user (nếu có)
       $notifications = Notification::with('user')
    ->orderBy('created_at', 'desc')
    ->paginate(10);
        // Lấy danh sách user để chọn người nhận trong Modal
        $users = User::where('role', '!=', 'admin')->get(); 

        return view('admin.notifications.index', compact('notifications', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'user_id' => 'nullable|exists:users,id', // Nếu null thì hiểu là gửi tất cả
        ]);

        Notification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Đã gửi thông báo thành công! 🚀');
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa thông báo!');
    }
}