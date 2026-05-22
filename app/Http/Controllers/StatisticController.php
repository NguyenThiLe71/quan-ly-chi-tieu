<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavingGoal;

class StatisticController extends Controller
{
    // View chính
    public function index()
    {
        return view('statistics.index');
    }

    // API lấy dữ liệu (AJAX) - Đã tối ưu tốc độ xử lý câu lệnh
    public function getData(Request $request)
    {
        $user_id = Auth::id();
        
        // 🚀 TỐI ƯU 1: Ép kiểu dữ liệu đầu vào chuẩn xác để MySQL bắt Index nhanh hơn
        $year = (int) $request->input('year', date('Y'));
        $month = $request->input('month') ? (int) $request->input('month') : null;

        // 🔥 QUERY TRANSACTIONS
        $query = Transaction::where('user_id', $user_id)
            ->whereYear('transaction_date', $year);

        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        // Tối ưu select, gom nhóm dữ liệu trực tiếp dưới database 
        $transactions = $query
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->get()
            ->keyBy('month');

        $data = [];

        // 👉 Nếu chọn 1 tháng cụ thể
        if ($month) {
            $data[] = [
                'month' => $month,
                'total_income' => (float) ($transactions[$month]->total_income ?? 0),
                'total_expense' => (float) ($transactions[$month]->total_expense ?? 0),
            ];
        } 
        // 👉 Nếu lấy dữ liệu cả năm (Đổ đủ cấu trúc cho Chart.js vẽ line mượt mà)
        else {
            for ($i = 1; $i <= 12; $i++) {
                $data[] = [
                    'month' => $i,
                    'total_income' => (float) ($transactions[$i]->total_income ?? 0),
                    'total_expense' => (float) ($transactions[$i]->total_expense ?? 0),
                ];
            }
        }

        // 🔥 THÊM PHẦN SAVING (Đã bọc kiểm tra dữ liệu an toàn)
        $saving = SavingGoal::where('user_id', $user_id)
            ->where('status', 'active')
            ->select(
                DB::raw('SUM(current_amount) as saved'),
                DB::raw('SUM(target_amount) as target')
            )
            ->first();

        // 🚀 TỐI ƯU 2: Tránh trường hợp Model trả về thuộc tính null làm lỗi luồng nhận dữ liệu của Javascript
        $savedAmount = $saving ? (float) $saving->saved : 0.0;
        $targetAmount = $saving ? (float) $saving->target : 0.0;

        // 🔥 RETURN JSON SẠCH - PHẢN HỒI SIÊU TỐC
        return response()->json([
            'chartData' => $data,
            'saving' => [
                'saved' => $savedAmount,
                'target' => $targetAmount,
            ]
        ]);
    }
}