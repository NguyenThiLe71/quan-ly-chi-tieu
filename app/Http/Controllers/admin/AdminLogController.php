<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\UserInteraction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
   public function index(Request $request)
{
    $query = UserInteraction::with('user')
        ->latest();

    // Lọc user
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    // Lọc action
    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }

    // Lọc module
    if ($request->filled('module')) {
        $query->where('module', $request->module);
    }

    $logs = $query->paginate(10);

    $users = User::all();

    return view('admin.logs', compact('logs', 'users'));
}
    // Hàm xóa log cũ (Nếu ông muốn thêm nút dọn dẹp hệ thống)
    public function cleanup()
    {
       UserInteraction::where('created_at', '<', now()->subMonths(1))->delete();
        return back()->with('success', 'Đã dọn dẹp nhật ký cũ hơn 30 ngày!');
    }
}