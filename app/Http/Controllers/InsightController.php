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
        
        // 🛠️ Ép kiểu số nguyên giúp MySQL tối ưu hóa luồng quét Index trên cột Date nhanh hơn
        $month = (int)$request->input('month', now()->month);
        $year = (int)$request->input('year', now()->year);

        // 🛠️ Thêm with(['category']) để nạp dữ liệu liên kết nhanh, giảm tải IO cho CPU
        $transactions = Transaction::with(['category'])
            ->where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'prediction' => 0,
                'insights' => [[
                    'type' => 'pattern',
                    'content' => "Chưa có dữ liệu giao dịch trong tháng $month/$year."
                ]],
                'month' => $month,
                'year' => $year
            ]);
        }

        try {
            // Thiết lập timeout hợp lý tránh treo tiến trình PHP quá lâu làm Lighthouse sụt điểm
            $response = Http::timeout(10)->post('http://127.0.0.1:8000/analyze', [
                'transactions' => $transactions->toArray(),
                'user_id' => $userId,
                'month' => $month,
                'year' => $year
            ]);

            if (!$response->successful()) {
                Log::error('AI FAIL', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'AI server lỗi'], 500);
            }

            $data = $response->json();

            Insight::where('user_id', $userId)
                ->where('month', $month)
                ->where('year', $year)
                ->delete();

            $savedInsights = [];

            if (isset($data['insights'])) {
                foreach ($data['insights'] as $i) {
                    $insight = Insight::create([
                        'user_id' => $userId,
                        'insight_type' => $i['type'] ?? 'pattern',
                        'content' => $i['content'],
                        'data_json' => json_encode($i),
                        'month' => $month,
                        'year' => $year,
                        'is_read' => 0
                    ]);
                    $savedInsights[] = $insight;
                }
            }

            return response()->json([
                'prediction' => $data['prediction'] ?? 0,
                'insights' => collect($savedInsights)
                    ->map(function ($i) {
                        return [
                            'type' => $i->insight_type,
                            'content' => $i->content
                        ];
                    })
                    ->values()
                    ->toArray(),
                'charts' => $data['charts'] ?? [],
                'month' => $month,
                'year' => $year
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Không thể kết nối server AI'], 500);
        }
    }

    public function chatProxy(Request $request)
    {
        try {
            $user = Auth::user();
            
            // 🛠️ Lấy động theo tên tài khoản đăng nhập để hiển thị chính xác trong đoạn hội thoại chat
            $currentUserName = $user->name ?? $user->username ?? 'Người dùng';

            $response = Http::post('http://127.0.0.1:8000/chat', [
                'message' => $request->message,
                'context' => $request->context,
                'user_name' => $currentUserName
            ]);

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'answer' => 'Rất tiếc, trợ lý đang bận xử lý dữ liệu.'
            ], 500);
        }
    }
}