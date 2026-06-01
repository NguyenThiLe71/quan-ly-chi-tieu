<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insight;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InsightController extends Controller
{
    public function index()
    {
        return view('insights.index');
    }

 public function analyze(Request $request)
{
    $userId = Auth::id();
    
    $month = (int)$request->input('month', now()->month);
    $year = (int)$request->input('year', now()->year);

    $transactions = Transaction::with(['category'])
        ->where('user_id', $userId)
        ->whereMonth('transaction_date', $month)
        ->whereYear('transaction_date', $year)
        ->get();

    if ($transactions->isEmpty()) {
        return response()->json([
            'prediction' => 0,
            'health_score' => 0,
            'insights' => [[
                'type' => 'pattern',
                'content' => "Chưa có dữ liệu giao dịch trong tháng $month/$year.",
                'is_premium' => false // Thẻ thông báo không khóa
            ]],
            'charts' => [
                'trend' => ['labels' => [], 'values' => []],
                'level' => ['labels' => ["Thấp", "Trung bình", "Cao"], 'values' => [0, 0, 0]],
                'compare' => ['labels' => ["Chi TB", "AI dự đoán"], 'values' => [0, 0]]
            ],
            'month' => $month,
            'year' => $year
        ]);
    }

    try {
        $response = Http::timeout(10)->post('http://127.0.0.1:8000/analyze', [
            'transactions' => $transactions->toArray(),
            'user_id' => $userId,
            'month' => $month,
            'year' => $year
        ]);

        if (!$response->successful()) {
            Log::error('AI FAIL', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['error' => 'AI server lỗi'], 500);
        }

        $data = $response->json();

        Insight::where('user_id', $userId)->where('month', $month)->where('year', $year)->delete();

        $savedInsights = [];
        if (isset($data['insights'])) {
            foreach ($data['insights'] as $i) {
                $savedInsights[] = Insight::create([
                    'user_id' => $userId,
                    'insight_type' => $i['type'] ?? 'pattern',
                    'content' => $i['content'],
                    'data_json' => json_encode($i),
                    'month' => $month,
                    'year' => $year,
                    'is_read' => 0
                ]);
            }
        }

        return response()->json([
            'prediction' => $data['prediction'] ?? 0,
            'health_score' => $data['health_score'] ?? 0,
            'insights' => collect($savedInsights)->map(function ($i) {
                // ĐÁNH DẤU CÁC LOẠI THẺ PREMIUM Ở ĐÂY
                $isPremium = in_array($i->insight_type, ['pattern', 'recommendation']);
                return [
                    'type' => $i->insight_type, 
                    'content' => $i->content,
                    'is_premium' => $isPremium
                ];
            })->values()->toArray(),
            'charts' => $data['charts'] ?? [],
            'month' => $month,
            'year' => $year
        ]);

    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['error' => 'Không thể kết nối server AI'], 500);
    }
}

// --- THÊM PHƯƠNG THỨC NÀY VÀO SAU HÀM ANALYZE ---
   public function compare(Request $request)
{
    // Kiểm tra Premium ngay tại đây
    if (!Auth::user()->is_premium) {
        return response()->json([
            'error' => 'Tính năng này chỉ dành cho tài khoản Premium.'
        ], 403);
    }

    $userId = Auth::id();

    // Lấy tháng/năm từ request (ví dụ: month1=2026-05)
    $m1 = $request->input('month1'); 
    $m2 = $request->input('month2');

    $data1 = $this->getMonthData($userId, $m1);
    $data2 = $this->getMonthData($userId, $m2);

    // Gọi tiếp đến FastAPI để lấy đoạn văn so sánh (AI Insight)
    try {
        $aiResponse = Http::post('http://127.0.0.1:8000/compare-insight', [
            'month1' => $data1,
            'month2' => $data2,
            'm1_label' => $m1,
            'm2_label' => $m2
        ]);
        
        $insight = $aiResponse->json()['insight'] ?? 'Hiện chưa có so sánh chi tiết.';
        
        return response()->json([
            'month1' => $data1,
            'month2' => $data2,
            'insight' => $insight
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Server AI bận'], 500);
    }
}

    // Hàm bổ trợ để lấy dữ liệu chi tiêu theo danh mục
    private function getMonthData($userId, $date)
    {
        list($year, $month) = explode('-', $date);

        $transactions = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->where('type', 'expense')
            ->with('category')
            ->get();

        // Gom nhóm theo danh mục
        return $transactions->groupBy(function($item) {
            return $item->category ? $item->category->name : 'Khác';
        })->map(function($items) {
            return $items->sum('amount');
        });
    }
    // --- KẾT THÚC PHẦN MỚI ---
    public function chatProxy(Request $request)
{
    try {
        // Lấy ID user hiện tại
        $userId = Auth::id();
        // Lấy lại đối tượng user mới nhất từ Database để đảm bảo có phương thức save()
        $user = \App\Models\User::find($userId);
        
        $today = now()->toDateString();
        $currentUserName = $user->name ?? $user->username ?? 'Người dùng';

        // 1. Kiểm tra giới hạn lượt chat cho người dùng không phải Premium
        if (!$user->is_premium) {
            // Reset nếu sang ngày mới
            if ($user->last_chat_date !== $today) {
                $user->daily_chat_count = 0;
                $user->last_chat_date = $today;
            }

            // Chặn nếu đã dùng quá 3 câu
            if ($user->daily_chat_count >= 3) {
                return response()->json([
                    'answer' => ' Bạn đã dùng hết 3 lượt hỏi miễn phí hôm nay. Hãy nâng cấp Premium để chat thả ga và lưu lại lịch sử nhé!'
                ], 403);
            }
        }

        // 2. Gọi sang AI Server
        $response = Http::post('http://127.0.0.1:8000/chat', [
            'message' => $request->message,
            'context' => $request->context,
            'user_name' => $currentUserName,
            'user_id' => $user->id
        ]);

        if (!$response->successful()) {
            throw new \Exception("AI Server Error");
        }

        $data = $response->json();
        $aiAnswer = $data['answer'] ?? 'Xin lỗi, trợ lý không có câu trả lời cho câu này.';

        // 3. Tăng số đếm (nếu là User thường)
        if (!$user->is_premium) {
            $user->daily_chat_count += 1;
            $user->last_chat_date = $today;
            $user->save(); // Bây giờ chắc chắn sẽ hoạt động!
        }

        // 4. Lưu lịch sử (nếu là Premium)
        if ($user->is_premium) {
            \App\Models\ChatHistory::create([
                'user_id' => $user->id,
                'message' => $request->message,
                'answer' => $aiAnswer
            ]);
        }

        return response()->json(['answer' => $aiAnswer]);

    } catch (\Exception $e) {
        Log::error("Chat Error: " . $e->getMessage()); // Log để dễ debug
        return response()->json([
            'answer' => 'Rất tiếc, trợ lý đang bận xử lý dữ liệu.'
        ], 500);
    }
}
}