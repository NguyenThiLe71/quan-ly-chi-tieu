<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\SavingGoal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Thống kê số dư, thu, chi
        $income = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $expense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // 2. Dữ liệu biểu đồ (Doughnut)
        $chartData = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $labels = $chartData->map(fn($item) => $item->category->name ?? 'Khác')->toArray();
        $totals = $chartData->map(fn($item) => (float)$item->total)->toArray();

        // 3. Top 3 giao dịch mới nhất
        $recentTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->with('category')
            ->latest('transaction_date')
            ->latest('created_at')
            ->take(3)
            ->get();

        // 4. LOGIC HEO ĐẤT (Tính toán tại đây để View sạch)
        $goals = SavingGoal::where('user_id', $userId)->get();
        $topGoal = null;
        $percent = 0;

        if ($goals->count() > 0) {
            // Sắp xếp tìm mục tiêu có tiến độ % cao nhất
            $topGoal = $goals->map(function($goal) {
                $goal->progress_ratio = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) : 0;
                return $goal;
            })->sortByDesc('progress_ratio')->first();

            if ($topGoal && $topGoal->target_amount > 0) {
                $percent = round(($topGoal->current_amount / $topGoal->target_amount) * 100);
            }
        }

        // Đóng gói tất cả biến gửi sang View
        return view('dashboard', compact(
            'balance', 'income', 'expense', 
            'labels', 'totals', 'recentTransactions',
            'topGoal', 'percent'
        ));
    }
}