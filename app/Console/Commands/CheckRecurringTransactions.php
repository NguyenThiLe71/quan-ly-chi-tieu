<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\Notification;
use Carbon\Carbon;

class CheckRecurringTransactions extends Command
{
    // Lệnh kích hoạt thủ công bằng tay nếu muốn test
    protected $signature = 'transactions:check-recurring';
    protected $description = 'Tự động quét và thông báo nhắc nhở nếu tới ngày mà người dùng chưa nhập giao dịch cố định';

    public function handle()
    {
        $today = Carbon::today();
        $currentDay = $today->day;     // Hôm nay là ngày mấy (Ví dụ: ngày 11)
        $currentMonth = $today->month; // Tháng này là tháng mấy (Ví dụ: tháng 5)
        $currentYear = $today->year;   // Năm hiện tại

        // 1. Lấy tất cả các lịch nhắc cố định đang bật (is_active = 1) và trùng với ngày hôm nay (day_of_month)
        $recurringItems = RecurringTransaction::where('is_active', true)
            ->where('day_of_month', $currentDay)
            ->get();

        foreach ($recurringItems as $item) {
            // 2. Kiểm tra xem trong THÁNG NÀY, người dùng đã tự tay nhập khoản này vào bảng transactions chưa
            $hasPaid = Transaction::where('user_id', $item->user_id)
                ->where('category_id', $item->category_id)
                ->whereMonth('transaction_date', $currentMonth)
                ->whereYear('transaction_date', $currentYear)
                ->exists();

            // 3. Nếu ĐẾN NGÀY HẸN mà CHƯA THẤY nhập (hasPaid == false) -> Tiến hành bắn thông báo
            if (!$hasPaid) {
                // Kiểm tra xem hôm nay đã bắn thông báo nhắc nhở khoản này chưa (tránh một ngày bắn đi bắn lại nhiều lần)
                $alreadyNotified = Notification::where('user_id', $item->user_id)
                    ->where('title', '📌 Nhắc nhở chi tiêu định kỳ')
                    ->whereDate('created_at', $today)
                    ->where('message', 'like', '%' . $item->name . '%')
                    ->exists();

                if (!$alreadyNotified) {
                    Notification::create([
                        'user_id' => $item->user_id,
                        'title'   => '📌 Nhắc nhở chi tiêu định kỳ',
                        'message' => "Hôm nay là ngày " . $currentDay . ", bạn ơi! Đừng quên kiểm tra và nhập khoản \"" . $item->name . "\" với số tiền khoảng " . number_format($item->amount) . " đ nhé!",
                    ]);
                }
            }
        }

        $this->info('Quét giao dịch cố định thành công!');
    }
}