<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SavingGoal;
use App\Models\Notification;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Helpers\InteractionHelper;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 💡 1. Lệnh quét mục tiêu hết hạn
Artisan::command('goals:check-expired', function () {
    $expiredGoals = SavingGoal::where('status', 'active')
        ->whereNotNull('deadline')
        ->whereDate('deadline', '<=', now())
        ->whereColumn('current_amount', '<', 'target_amount')
        ->get();

    foreach ($expiredGoals as $goal) {
        $goal->update(['status' => 'expired']);

        Notification::create([
            'user_id' => $goal->user_id,
            'title' => '⚠️ Mục tiêu chưa hoàn thành',
            'message' => 'Mục tiêu "' . $goal->name . '" đã hết hạn nhưng chưa đạt số tiền mong muốn.',
        ]);

        InteractionHelper::log(
            $goal->user_id,
            'saving_goals',
            'updated',
            $goal->name,
            '⚠️ Mục tiêu "' . $goal->name . '" đã hết hạn nhưng chưa hoàn thành'
        );
    }

    $this->info("Đã cập nhật " . $expiredGoals->count() . " mục tiêu hết hạn.");
})->purpose('Quét và thông báo các mục tiêu tiết kiệm đã quá hạn');


// 💡 2. Lệnh quét giao dịch cố định (Nhắc dai dẳng cho đến khi nhập)
Artisan::command('transactions:check-recurring', function () {
    $today = Carbon::today();
    $currentDay = $today->day;
    $currentMonth = $today->month;
    $currentYear = $today->year;

    // Lấy tất cả lịch hẹn có ngày hẹn ĐÃ ĐẾN HOẶC ĐÃ QUA (day_of_month <= ngày hiện tại)
    $recurringItems = RecurringTransaction::where('is_active', true)
        ->where('day_of_month', '<=', $currentDay) 
        ->get();

    foreach ($recurringItems as $item) {
        // Kiểm tra xem tháng này đã có giao dịch nào cho danh mục này chưa
        $hasPaid = Transaction::where('user_id', $item->user_id)
            ->where('category_id', $item->category_id)
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->exists();

        // Nếu chưa nhập giao dịch -> Bắn thông báo nhắc nhở
        if (!$hasPaid) {
            // Kiểm tra xem hôm nay đã bắn cái thông báo nhắc nhở nào cho khoản này chưa (tránh spam trong 1 ngày)
            $alreadyNotified = Notification::where('user_id', $item->user_id)
                ->where('title', '📌 Nhắc nhở chi tiêu định kỳ')
                ->whereDate('created_at', $today)
                ->where('message', 'like', '%' . $item->name . '%')
                ->exists();

            if (!$alreadyNotified) {
                Notification::create([
                    'user_id' => $item->user_id,
                    'title'   => '📌 Nhắc nhở chi tiêu định kỳ',
                    'message' => "Đã quá hạn/đến hạn ngày " . $item->day_of_month . " nhưng chưa thấy bạn nhập khoản \"" . $item->name . "\". Đừng quên cập nhật nhé!",
                ]);
            }
        }
    }

    $this->info("Đã quét và nhắc nhở các giao dịch cố định còn thiếu.");
})->purpose('Nhắc nhở hằng ngày cho đến khi giao dịch cố định được nhập');


// ⏰ Thiết lập lịch chạy tự động hàng ngày
Schedule::command('goals:check-expired')->daily();
Schedule::command('salary:check-reminder')->daily();
Schedule::command('transactions:check-recurring')->dailyAt('07:00');