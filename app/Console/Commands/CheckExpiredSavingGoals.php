<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SavingGoal;
use App\Models\Notification;
use App\Helpers\InteractionHelper;

class CheckExpiredSavingGoals extends Command
{
    // Tên lệnh để gọi qua terminal (php artisan goals:check-expired)
    protected $signature = 'goals:check-expired';
    protected $description = 'Kiểm tra và cập nhật trạng thái các mục tiêu tiết kiệm đã hết hạn';

    public function handle()
    {
        // Lấy các mục tiêu active, có deadline và đã qua hoặc đúng ngày hôm nay nhưng chưa đủ tiền
        $expiredGoals = SavingGoal::where('status', 'active')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<=', now()) // Sử dụng <= như đã phân tích
            ->whereColumn('current_amount', '<', 'target_amount')
            ->get();

        if ($expiredGoals->isEmpty()) {
            $this->info('Không có mục tiêu nào mới hết hạn.');
            return;
        }

        foreach ($expiredGoals as $goal) {
            $goal->update(['status' => 'expired']);

            // Tạo thông báo cho người dùng
            Notification::create([
                'user_id' => $goal->user_id,
                'title' => '⚠️ Mục tiêu chưa hoàn thành',
                'message' => 'Mục tiêu "' . $goal->name . '" đã hết hạn nhưng chưa đạt số tiền mong muốn.',
            ]);

            // Ghi log hệ thống
            InteractionHelper::log(
                $goal->user_id,
                'saving_goals',
                'updated',
                $goal->name,
                '⚠️ Mục tiêu "' . $goal->name . '" đã hết hạn nhưng chưa hoàn thành'
            );
        }

        $this->info('Đã cập nhật ' . $expiredGoals->count() . ' mục tiêu hết hạn.');
    }
}