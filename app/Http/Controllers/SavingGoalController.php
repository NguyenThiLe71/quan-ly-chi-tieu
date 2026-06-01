<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingGoal;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Helpers\InteractionHelper;

class SavingGoalController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 🚀 TỐI ƯU HIỆU NĂNG: Chỉ định select rõ ràng để giảm tải bộ nhớ RAM cho Server
        $query = SavingGoal::select('id', 'user_id', 'name', 'target_amount', 'current_amount', 'deadline', 'status', 'created_at')
            ->where('user_id', $userId);

        // 🔍 Search - Tối ưu hóa tìm kiếm text
        if ($request->filled('search')) {
            $searchKeyword = trim($request->search);
            $query->where('name', 'like', '%' . $searchKeyword . '%');

            InteractionHelper::log(
                $userId,
                'saving_goals',
                'search',
                $searchKeyword,
                '🔍 Tìm kiếm mục tiêu: ' . $searchKeyword
            );
        }

        // 📅 Filter month - Tối ưu hóa truy vấn thời gian bằng ép kiểu nguyên bản (Integer)
        elseif (
            $request->filled('month_year') &&
            str_contains($request->month_year, '-')
        ) {
            [$year, $month] = explode('-', $request->month_year);

            $query->whereMonth('deadline', (int)$month)
                  ->whereYear('deadline', (int)$year);

            InteractionHelper::log(
                $userId,
                'saving_goals',
                'search',
                null,
                '🔍 Lọc mục tiêu tháng ' . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . $year
            );
        }

        // 📄 TỐI ƯU SẮP XẾP: Thay latest() bằng orderBy cụ thể để tận dụng tối đa Primary Index
        $goals = $query->orderBy('id', 'desc')
                       ->paginate(4);

        return view('goals.index', compact('goals'));
    }

    // 🆕 CREATE
    public function store(Request $request)
    {
        // 🛠️ ĐÃ CẬP NHẬT: Thêm after_or_equal:today để chặn ngày quá khứ
       $request->validate([
    'name' => 'required|max:150',
    'target_amount' => 'required|numeric|min:1',
    'deadline' => 'nullable|date|after_or_equal:today',
], [
    // 🔥 Đã sửa đúng theo đặc tả 1.0.3
    'deadline.after_or_equal' => 'Ngày hết hạn phải từ ngày hôm nay trở đi.' 
]);

        $goal = SavingGoal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'current_amount' => 0,
            'deadline' => $request->deadline,
            'status' => 'active'
        ]);

        InteractionHelper::log(
            Auth::id(),
            'saving_goals',
            'created',
            $goal->name,
            '🎯 Tạo mục tiêu "' . $goal->name . '" | '
            . number_format($goal->target_amount) . ' VNĐ'
            . (
                $goal->deadline
                ? ' | ⏳ Hạn đến ngày '
                    . \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y')
                : ''
            )
        );

        Notification::create([
            'user_id' => Auth::id(),
            'title' => '🎯 Mục tiêu mới',
            'message' => 'Bạn đã tạo mục tiêu "' . $goal->name . '"',
        ]);

        return back()->with('success', 'Thêm thành công!');
    }

    // 💰 ADD / REMOVE MONEY
    public function addMoney(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|not_in:0'
        ]);

        $goal = SavingGoal::where('user_id', Auth::id())
            ->findOrFail($id);

        $amount = (float) $request->amount;

        // ❌ Không cho rút quá số dư
        if ($amount < 0 && abs($amount) > $goal->current_amount) {
            return back()->with('error', 'Không thể trừ quá số tiền hiện có!');
        }

        $oldStatus = $goal->status;
        $oldAmount = $goal->current_amount;

        // update money
        $goal->current_amount += $amount;

        if ($goal->current_amount < 0) {
            $goal->current_amount = 0;
        }

        $newAmount = $goal->current_amount;

        // progress
        $target = max($goal->target_amount, 1);
        $progress = $newAmount / $target;

        // 🔥 UPDATE STATUS
        if ($goal->current_amount >= $goal->target_amount) {

            $goal->status = 'completed';

            // chỉ notify khi mới completed
            if ($oldStatus !== 'completed') {

                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => '🎉 Hoàn thành mục tiêu!',
                    'message' => 'Bạn đã đạt mục tiêu "' . $goal->name . '"',
                ]);
            }
        }
        else {

            // nếu chưa đủ tiền nữa -> reset lại
            if (
                $goal->deadline &&
                now()->gt($goal->deadline)
            ) {

                $goal->status = 'expired';
            }
            else {

                $goal->status = 'active';
            }
        }

        // 🔥 gần đạt
        if (
            $progress >= 0.8 &&
            $progress < 1 &&
            $goal->status === 'active'
        ) {

            Notification::create([
                'user_id' => Auth::id(),
                'title' => '🔥 Sắp đạt mục tiêu!',
                'message' => 'Bạn đã đạt ' . round($progress * 100) . '%',
            ]);
        }

        Notification::create([
            'user_id' => Auth::id(),
            'title' => '💰 Cập nhật số dư mục tiêu',
            'message' =>
                ($amount > 0 ? 'Nạp thêm ' : 'Đã rút ')
                . number_format(abs($amount))
                . ' VNĐ vào "' . $goal->name . '"',
        ]);

        // 📝 LOG
        if ($amount > 0) {

            $note =
                '💰 Nạp tiền mục tiêu "' . $goal->name .
                '" từ "' . number_format($oldAmount) .
                ' VNĐ" thành "' .
                number_format($newAmount) . ' VNĐ"';
        }
        else {

            $note =
                '💸 Rút tiền mục tiêu "' . $goal->name .
                '" từ "' . number_format($oldAmount) .
                ' VNĐ" thành "' .
                number_format($newAmount) . ' VNĐ"';
        }

        InteractionHelper::log(
            Auth::id(),
            'saving_goals',
            'updated',
            $goal->name,
            $note
        );

        $goal->save();

        return back()->with('success', 'Đã cập nhật tiền!');
    }

    // ✏️ UPDATE GOAL
    public function update(Request $request, $id)
    {
        // 🛠️ ĐÃ CẬP NHẬT: Thêm after_or_equal:today để chặn ngày quá khứ khi cập nhật
       $request->validate([
    'name' => 'required|max:150',
    'target_amount' => 'required|numeric|min:1',
    'deadline' => 'nullable|date|after_or_equal:today',
], [
    // 🔥 Đã sửa đúng theo đặc tả 1.0.3
    'deadline.after_or_equal' => 'Ngày hết hạn phải từ ngày hôm nay trở đi.' 
]);

        $goal = SavingGoal::where('user_id', Auth::id())
            ->findOrFail($id);

        // old data
        $oldName = $goal->name;
        $oldTarget = (float)$goal->target_amount;
        $oldDeadline = $goal->deadline
            ? \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d')
            : null;

        // update info
        $goal->name = $request->name;
        $goal->target_amount = $request->target_amount;
        $goal->deadline = $request->deadline;

        // 🔥 lưu status cũ
        $oldStatus = $goal->status;

        // 🔥 UPDATE STATUS SAU KHI SỬA TARGET
        if ($goal->current_amount >= $goal->target_amount) {

            $goal->status = 'completed';

            // 🎉 chỉ thông báo khi mới hoàn thành
            if ($oldStatus !== 'completed') {

                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => '🎉 Hoàn thành mục tiêu!',
                    'message' => 'Bạn đã đạt mục tiêu "' . $goal->name . '"',
                ]);
            }
        }
        else {

            if (
                $goal->deadline &&
                now()->gt($goal->deadline)
            ) {

                $goal->status = 'expired';
            }
            else {

                $goal->status = 'active';
            }
        }

        $goal->save();
        
        // 📝 LOG
        $logMessages = [];

        if ($oldName !== $request->name) {

            $logMessages[] =
                'Sửa tên mục tiêu từ "' . $oldName .
                '" thành "' . $request->name . '"';
        }

        if ($oldTarget !== (float)$request->target_amount) {

            $logMessages[] =
                'Sửa giới hạn tiền của mục tiêu "' . $goal->name .
                '" từ "' . number_format($oldTarget) .
                ' VNĐ" thành "' .
                number_format($request->target_amount) .
                ' VNĐ"';
        }

        $newDeadline = $request->deadline
            ? \Carbon\Carbon::parse($request->deadline)->format('Y-m-d')
            : null;

        if ($oldDeadline !== $newDeadline) {

            $formatOld = $oldDeadline
                ? \Carbon\Carbon::parse($oldDeadline)->format('d/m/Y')
                : 'Chưa có';

            $formatNew = $newDeadline
                ? \Carbon\Carbon::parse($newDeadline)->format('d/m/Y')
                : 'Vô thời hạn';

            $logMessages[] =
                'Sửa ngày hết hạn của mục tiêu "' . $goal->name .
                '" từ ngày "' . $formatOld .
                '" thành "' . $formatNew . '"';
        }

        if (count($logMessages) > 0) {

            $finalNote =
                count($logMessages) === 1
                ? '✏️ ' . $logMessages[0]
                : '✏️ Chỉnh sửa mục tiêu "' . $goal->name .
                    '": ' . implode('; ', $logMessages);

            InteractionHelper::log(
                Auth::id(),
                'saving_goals',
                'updated',
                $goal->name,
                $finalNote
            );
        }

        return back()->with('success', 'Cập nhật mục tiêu thành công!');
    }

    // ❌ DELETE
    public function destroy($id)
    {
        try {

            $goal = SavingGoal::where('user_id', Auth::id())
                ->findOrFail($id);

            $name = $goal->name;

            $goal->delete();

            Notification::create([
                'user_id' => Auth::id(),
                'title' => '✂️ Đã xóa mục tiêu',
                'message' => 'Mục tiêu "' . $name . '" đã được gỡ bỏ.',
            ]);

            InteractionHelper::log(
                Auth::id(),
                'saving_goals',
                'deleted',
                $name,
                '🗑️ Xóa mục tiêu "' . $name . '"'
            );

            return back()->with('success', 'Đã xóa mục tiêu!');
        }
        catch (\Exception $e) {

            return back()->with('error', 'Xóa thất bại');
        }
    }
}