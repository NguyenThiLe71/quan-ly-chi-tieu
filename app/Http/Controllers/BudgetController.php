<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Notification; 
use App\Models\Transaction; // 📑 Đã thêm dòng này để tối ưu tính toán số tiền đã chi
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware; 
use Illuminate\Routing\Controllers\Middleware;    
use App\Helpers\InteractionHelper;

class BudgetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    // 📌 Danh sách + filter theo tháng/năm (YYYY-MM)
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Budget::with('category')
            ->where('user_id', $userId);

        // 🔍 LOG + FILTER THÁNG/NĂM
        if ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);

            // 🔥 LOG
            InteractionHelper::log(
                Auth::id(),
                'budgets',
                'search',
                null,
                "🔍 Lọc ngân sách tháng $month/$year"
            );

            // 🔥 FILTER
            $query->where('month', (int)$month)
                  ->where('year', (int)$year);

        } else {
            // 📅 Mặc định tháng hiện tại
            $month = now()->month;
            $year = now()->year;
            $query->where('month', $month)
                  ->where('year', $year);
        }

        // 📄 Lấy dữ liệu phân trang (6 bản ghi/trang)
        $budgets = $query->orderByDesc('year')
                         ->orderByDesc('month')
                         ->paginate(6);

        // 🚀 TỐI ƯU HÓA TẦNG CONTROLLER: Tính số tiền đã chi trực tiếp tại đây để nâng điểm Performance
        foreach ($budgets as $b) {
            $b->spent_amount = Transaction::where('user_id', $userId)
                ->where('category_id', $b->category_id)
                ->whereMonth('transaction_date', $b->month)
                ->whereYear('transaction_date', $b->year)
                ->where('type', 'expense')
                ->sum('amount');
        }

        // 📂 Danh mục tài khoản
        $categories = Category::where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_default', true);
        })->get();

        return view('budgets.index', compact('budgets', 'categories'));
    }

    // 📌 Thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount_limit' => 'required|regex:/^[0-9.]+$/',
            'month_year' => 'required|date_format:Y-m',
        ]);

        $userId = Auth::id();
        [$year, $month] = explode('-', $request->month_year);

        $exists = Budget::where('user_id', $userId)
            ->where('category_id', $request->category_id)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ngân sách tháng này đã tồn tại!');
        }

        $cleanAmount = str_replace('.', '', $request->amount_limit);

        $budget = Budget::create([
            'user_id' => $userId,
            'category_id' => $request->category_id,
            'amount_limit' => $cleanAmount,
            'month' => $month,
            'year' => $year,
        ]);

        $categoryName = Category::find($request->category_id)?->name ?? 'Danh mục';

        // 🔔 THÔNG BÁO
        Notification::create([
            'user_id' => $userId,
            'title' => 'Ngân sách mới',
            'message' => 'Bạn vừa tạo ngân sách mục "' . $categoryName . '" cho tháng ' . $month . '/' . $year,
        ]);

        InteractionHelper::log(
            Auth::id(),
            'budgets',
            'CREATED',
            $categoryName,
            '💰 Tạo ngân sách mục "' . $categoryName . '": ' 
            . number_format($cleanAmount) . ' VNĐ | 📅 ' . $month . '/' . $year
        );

        return redirect()->route('budgets.index', [
            'month_year' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT)
        ])->with('success', 'Thêm ngân sách thành công!');
    }

    // 📌 Cập nhật
    public function update(Request $request, $id)
    {
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);
        $oldAmount = $budget->amount_limit;
        $request->validate([
            'amount_limit' => 'required|regex:/^[0-9.]+$/',
            'month_year' => 'nullable|date_format:Y-m',
        ]);

        if ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);
        } else {
            $month = $budget->month;
            $year = $budget->year;
        }

        $categoryId = $request->category_id ?? $budget->category_id;

        $exists = Budget::where('user_id', Auth::id())
            ->where('category_id', $categoryId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ngân sách đã tồn tại!');
        }

        $cleanAmount = str_replace('.', '', $request->amount_limit);

        $budget->update([
            'category_id' => $categoryId,
            'amount_limit' => $cleanAmount,
            'month' => $month,
            'year' => $year,
        ]);

        $categoryName = Category::find($categoryId)?->name ?? 'Danh mục';

        // 🔔 THÔNG BÁO
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Cập nhật ngân sách',
            'message' => 'Bạn vừa cập nhật hạn mức ngân sách mục "' . $categoryName . '" cho tháng ' . $month . '/' . $year,
        ]);

        $note = '';
        if ($oldAmount != $cleanAmount) {
            $note = '📝 Sửa ngân sách mục "' . $categoryName . '" từ "' 
                . number_format($oldAmount) . '" thành "' 
                . number_format($cleanAmount) . ' VNĐ"';
        } else {
            $note = '🔄 Cập nhật ngân sách mục "' . $categoryName . '"';
        }

        InteractionHelper::log(
            Auth::id(),
            'budgets',
            'UPDATED',
            $categoryName,
            $note . ' | 📅 ' . $month . '/' . $year
        );

        return redirect()->route('budgets.index', [
            'month_year' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT)
        ])->with('success', 'Cập nhật thành công!');
    }

    // 📌 Xóa
    public function destroy($id)
    {
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);
        $categoryName = $budget->category?->name ?? 'Danh mục';
        $amount = $budget->amount_limit;

        // 🔔 THÔNG BÁO
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Xóa ngân sách',
            'message' => 'Bạn vừa xóa ngân sách mục "' . $categoryName . '" của tháng ' . $budget->month . '/' . $budget->year,
        ]);

        $budget->delete();
        
        InteractionHelper::log(
            Auth::id(),
            'budgets',
            'DELETED',
            $categoryName,
            '🗑️ Xóa ngân sách mục "' . $categoryName . '": ' 
            . number_format($amount) . ' VNĐ | 📅 ' 
            . $budget->month . '/' . $budget->year
        );

        return back()->with('success', 'Đã xoá ngân sách!');
    }
}