<?php

namespace App\Http\Controllers;

use App\Helpers\InteractionHelper;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Models\Notification;
use App\Models\Budget;
use Carbon\Carbon;
use App\Models\TransactionLog;
use App\Models\RecurringTransaction; 

class TransactionController extends Controller
{
    // 🔥 FORMAT TIỀN (QUAN TRỌNG)
    private function formatAmount($amount)
    {
        return (float) str_replace('.', '', $amount);
    }

    // LIST + SEARCH
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id());

        // 1. Logic TÌM KIẾM (Search)
        if ($request->filled('search')) {
            $search = $request->search;

            TransactionLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'searched',
                'description' => "Người dùng tìm kiếm từ khóa: \"$search\"",
                'changed_at'  => now(),
            ]);

            InteractionHelper::log(
                Auth::id(),
                'transactions',
                'search',
                $search,
                '🔍 Tìm kiếm từ khóa "' . $search . '"'
            );

            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('amount', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 2. Logic LỌC THÁNG/NĂM
        $filterDate = $request->input('filter_date'); 

        if ($request->filled('filter_date')) {
            $dateParts = explode('-', $filterDate);

            if (count($dateParts) == 2) {
                $year = $dateParts[0];
                $month = $dateParts[1];

                InteractionHelper::log(
                    Auth::id(),
                    'transactions',
                    'search',
                    "Tháng $month/$year",
                    "Lọc giao dịch theo tháng $month năm $year"
                );

                $query->whereYear('transaction_date', $year)
                      ->whereMonth('transaction_date', $month);
            }
        }

        // 3. Lấy dữ liệu danh sách giao dịch
        $transactions = $query->orderByDesc('transaction_date')
                             ->orderByDesc('id')
                             ->get();

        $categories = Category::where(function($q) {
            $q->where('user_id', Auth::id())->orWhere('is_default', 1);
        })->get();

        $totalIncome = Transaction::where('user_id', Auth::id())->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', Auth::id())->where('type', 'expense')->sum('amount');
        $userBalance = $totalIncome - $totalExpense;

        return view('transactions.index', compact('transactions', 'categories', 'filterDate', 'userBalance'));
    }

    /**
     * 🔥 XEM LỊCH SỬ THAY ĐỔI
     */
    public function history()
    {
        $logs = DB::table('transaction_logs')
            ->where('user_id', Auth::id()) 
            ->select('action', 'old_amount', 'new_amount', 'changed_at', 'description')
            ->latest('changed_at')
            ->paginate(10);

        return view('transactions.history', compact('logs'));
    }

    // CREATE + TỰ ĐỘNG BẮT BÀI GIAO DỊCH CỐ ĐỊNH
    public function store(Request $request)
    {
        try {
            $amount = $this->formatAmount($request->amount);
            $categoryName = Category::find($request->category_id)?->name ?? 'Danh mục';

            // 1. Tạo giao dịch thực tế
            Transaction::create([
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'amount' => $amount,
                'type' => $request->type,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date,
                'is_saving' => 0
            ]);

            InteractionHelper::log(
                Auth::id(),
                'transactions',
                'created',
                null,
                '💰 Thêm giao dịch "' . $categoryName . '" | ' 
                . number_format($amount) . ' VNĐ | 📝 ' 
                . ($request->description ?: 'Không có mô tả')
            );

            // TỰ ĐỘNG BẮT BÀI: Nếu chọn "Đặt làm giao dịch cố định hằng tháng"
            if ($request->filled('is_recurring') && $request->is_recurring == 1) {
                $dayOfMonth = Carbon::parse($request->transaction_date)->day;

                RecurringTransaction::create([
                    'user_id'       => Auth::id(),
                    'name'          => $request->description ?: ($categoryName . ' cố định'), 
                    'amount'        => $amount, 
                    'type'          => $request->type,
                    'category_id'   => $request->category_id,
                    'day_of_month'  => $dayOfMonth, 
                    'is_active'     => true,
                ]);
            }

            // 2. Gửi thông báo giao dịch thông thường trước
            Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Giao dịch mới',
                'message' => 'Bạn vừa chi ' . number_format($amount) . ' VNĐ cho mục "' . $categoryName . '"',
            ]);

            // 3. Check Ngân sách để cảnh báo phụ thuộc theo phần trăm
            if ($request->type == 'expense') {
                $month = Carbon::parse($request->transaction_date)->month;
                $year = Carbon::parse($request->transaction_date)->year;

                $budget = Budget::where('user_id', Auth::id())
                    ->where('category_id', $request->category_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($budget) {
                    $totalSpent = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $request->category_id)
                        ->where('type', 'expense')
                        ->whereMonth('transaction_date', $month)
                        ->whereYear('transaction_date', $year)
                        ->sum('amount');

                    // Kiểm tra các ngưỡng cảnh báo riêng biệt
                    if ($totalSpent > $budget->amount_limit) {
                        Notification::create([
                            'user_id' => Auth::id(),
                            'title' => '🚨 Vượt ngân sách ' . $categoryName,
                            'message' => "Bạn đã chi " . number_format($totalSpent) . " VNĐ, vượt hạn mức " . number_format($budget->amount_limit) . " VNĐ của mục $categoryName",
                        ]);
                    } elseif ($totalSpent >= ($budget->amount_limit * 0.8)) {
                        Notification::create([
                            'user_id' => Auth::id(),
                            'title' => '⚠️ Sắp hết ngân sách ' . $categoryName,
                            'message' => "Mục $categoryName đã tiêu thụ " . number_format($totalSpent) . " VNĐ (Đạt " . round(($totalSpent / $budget->amount_limit) * 100, 1) . "% hạn mức)",
                        ]);
                    }
                }
            }

            return back()->with('success', 'Thêm giao dịch thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Thêm thất bại: ' . $e->getMessage());
        }
    }

    // UPDATE
    // UPDATE + ĐỒNG BỘ/BẬT/TẮT MỤC CỐ ĐỊNH
    public function update(Request $request, $id)
    {
        try {
            $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
            $amount = (float) str_replace('.', '', $request->amount);

            $oldDescription = $transaction->description;
            $oldAmount = (float) $transaction->amount;
            $oldCategoryId = $transaction->category_id; 

            // 1. Cập nhật thông tin giao dịch chính trước
            $transaction->update([
                'amount' => $amount,
                'category_id' => $request->category_id,
                'transaction_date' => $request->transaction_date,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            // Lấy tên danh mục mới sau khi update để làm dữ liệu lưu
            $categoryName = Category::find($request->category_id)?->name ?? 'Danh mục';

            // 2. 🔥 XỬ LÝ ĐỒNG BỘ & BẬT/TẮT NÚT GẠT CỐ ĐỊNH KHI SỬA
            if ($request->filled('is_recurring') && $request->is_recurring == 1) {
                // Trường hợp người dùng TÍCH CHỌN hoặc GIỮ NGUYÊN cố định: Dùng updateOrCreate để xử lý
                $newDayOfMonth = Carbon::parse($request->transaction_date)->day;

                // Xóa cấu hình cũ của danh mục cũ (nếu người dùng đổi cả danh mục lẫn giữ cố định)
                if ($oldCategoryId != $request->category_id) {
                    RecurringTransaction::where('user_id', Auth::id())
                        ->where('category_id', $oldCategoryId)
                        ->delete();
                }

                // Cập nhật hoặc tạo mới cấu hình cho danh mục hiện tại
                RecurringTransaction::updateOrCreate(
                    [
                        'user_id'     => Auth::id(),
                        'category_id' => $request->category_id,
                    ],
                    [
                        'name'         => $request->description ?: ($categoryName . ' cố định'),
                        'amount'       => $amount,
                        'type'         => $request->type,
                        'day_of_month' => $newDayOfMonth,
                        'is_active'    => true,
                    ]
                );
            } else {
                // Trường hợp người dùng KHÔNG TÍCH (Bỏ chọn): Xóa sạch cấu hình cố định cũ và mới của mục này
                RecurringTransaction::where('user_id', Auth::id())
                    ->whereIn('category_id', [$oldCategoryId, $request->category_id])
                    ->delete();
            }

            // --- HỆ THỐNG LOGS VÀ THÔNG BÁO (GIỮ NGUYÊN HOÀN TOÀN CỦA ÔNG) ---
            $note = '';
            $desc = $request->description ?? $oldDescription ?? 'Không có mô tả';

            if ($oldDescription != $request->description) {
                $note = '📝 Sửa mô tả giao dịch "' . $categoryName . '" từ "' . $oldDescription . '" thành "' . $request->description . '"';
            } elseif ($oldAmount != $amount) {
                $note = '💰 Sửa giá giao dịch "' . $categoryName . '" | ' . $desc . ' | từ "' . number_format($oldAmount) . ' VNĐ" thành "' . number_format($amount) . ' VNĐ"';
            } else {
                $note = '🔄 Cập nhật giao dịch "' . $categoryName . '"';
            }

            InteractionHelper::log(Auth::id(), 'transactions', 'updated', null, $note);

            $msg = "";
            if ($oldDescription != $request->description) {
                $msg = "Bạn vừa sửa mô tả mục $categoryName thành: \"" . $request->description . "\"";
            } elseif ($oldAmount != $amount) {
                $msg = "Bạn vừa sửa số tiền mục $categoryName thành " . number_format($amount) . " VNĐ";
            } else {
                $msg = "Bạn đã cập nhật thông tin giao dịch mục $categoryName";
            }

            Notification::create([
                'user_id' => Auth::id(),
                'title' => '🔄 Đã cập nhật giao dịch',
                'message' => $msg,
            ]);

            // LOGIC CHECK NGÂN SÁCH CỦA ÔNG
            if ($request->type == 'expense') {
                $month = Carbon::parse($request->transaction_date)->month;
                $year = Carbon::parse($request->transaction_date)->year;

                $budget = Budget::where('user_id', Auth::id())
                    ->where('category_id', $request->category_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($budget) {
                    $totalSpent = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $request->category_id)
                        ->where('type', 'expense')
                        ->whereMonth('transaction_date', $month)
                        ->whereYear('transaction_date', $year)
                        ->sum('amount');

                    if ($totalSpent > $budget->amount_limit) {
                        Notification::create([
                            'user_id' => Auth::id(),
                            'title' => '🚨 Vượt ngân sách ' . $categoryName,
                            'message' => "Sau khi sửa, mục $categoryName đã chi " . number_format($totalSpent) . " VNĐ (Vượt hạn mức " . number_format($budget->amount_limit) . " VNĐ)",
                        ]);
                    } elseif ($totalSpent >= ($budget->amount_limit * 0.8)) {
                        Notification::create([
                            'user_id' => Auth::id(),
                            'title' => '⚠️ Sắp hết ngân sách ' . $categoryName,
                            'message' => "Sau khi sửa, mục $categoryName đã chi " . number_format($totalSpent) . " VNĐ (Đạt " . round(($totalSpent / $budget->amount_limit) * 100, 1) . "% hạn mức)",
                        ]);
                    }
                }
            }

            return back()->with('success', 'Cập nhật thành công! ✨');

        } catch (\Exception $e) {
            return back()->with('error', 'Cập nhật thất bại: ' . $e->getMessage());
        }
    }

    // DELETE
   public function destroy($id)
{
    try {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $amount = $transaction->amount;
        $description = $transaction->description ?? 'Giao dịch không có mô tả';
        $categoryName = $transaction->category?->name ?? 'Danh mục';

        TransactionLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'deleted',
            'old_amount'  => $amount,
            'new_amount'  => null,
            'description' => $description, 
            'changed_at'  => now(),
        ]);

        InteractionHelper::log(
            Auth::id(),
            'transactions',
            'deleted',
            null,
            '🗑️ Xóa giao dịch "' . $categoryName . '" | ' . number_format($amount) . ' VNĐ'
        );

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Đã xóa giao dịch',
            'message' => 'Bạn đã xóa khoản chi ' . number_format($amount) . ' VNĐ (' . $description . ')',
        ]);

       \App\Models\RecurringTransaction::where('user_id', Auth::id())
            ->where('category_id', $transaction->category_id)
            ->delete();

        // Xóa giao dịch chính
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Xóa giao dịch thành công');

    } catch (\Exception $e) {
        return redirect()->route('transactions.index')->with('error', 'Xóa thất bại');
    }
}
}