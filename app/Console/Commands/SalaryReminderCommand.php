<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Notification;
use Carbon\Carbon;

class SalaryReminderCommand extends Command
{
    // Tên lệnh để chạy bằng artisan
    protected $signature = 'salary:check-reminder';

    // Mô tả lệnh
    protected $description = 'Tự động quét thói quen và nhắc nhở nhập lương mỗi ngày';

    public function handle()
    {
        // 1. Lấy tất cả người dùng trong hệ thống
        $users = User::all();

        foreach ($users as $user) {
            // 2. Tìm giao dịch "Lương" đầu tiên để xác định "Ngày nhập lương định kỳ"
            $firstSalary = Transaction::where('user_id', $user->id)
                ->whereHas('category', function($q) {
                    $q->where('name', 'like', '%Lương%');
                })
                ->orderBy('transaction_date', 'asc')
                ->first();

            if ($firstSalary) {
                $salaryDay = Carbon::parse($firstSalary->transaction_date)->day; 
                $today = Carbon::now();

                // 3. Kiểm tra tháng này người dùng đã nhập lương chưa
                $hasSalaryThisMonth = Transaction::where('user_id', $user->id)
                    ->whereMonth('transaction_date', $today->month)
                    ->whereYear('transaction_date', $today->year)
                    ->whereHas('category', function($q) {
                        $q->where('name', 'like', '%Lương%');
                    })
                    ->exists();

                // 4. LOGIC: Nếu ĐẾN NGÀY mà THÁNG NÀY CHƯA CÓ LƯƠNG
                if (!$hasSalaryThisMonth && $today->day >= $salaryDay) {
                    
                    // Kiểm tra xem đã tạo thông báo nhắc nhở tháng này chưa (tránh tạo trùng)
                    $alreadyNotified = Notification::where('user_id', $user->id)
                        ->where('type', 'salary_reminder')
                        ->whereMonth('created_at', $today->month)
                        ->whereYear('created_at', $today->year)
                        ->exists();

                    if (!$alreadyNotified) {
                        // TẠO THÔNG BÁO VÀO CSDL
                        Notification::create([
                            'user_id' => $user->id,
                            'type'    => 'salary_reminder',
                            'title'   => '🔔 Nhắc nhở tài chính',
                            'message' => "Hôm nay là ngày {$today->day}, theo thói quen bạn thường nhập lương vào ngày $salaryDay hàng tháng. Hãy cập nhật thu nhập nhé!",
                            'is_read' => false
                        ]);
                        
                        $this->info("Đã gửi thông báo cho User ID: {$user->id}");
                    }
                }
            }
        }
        $this->info('Quá trình quét hoàn tất!');
    }
}