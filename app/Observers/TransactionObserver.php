<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TransactionObserver
{
    /**
     * Hàm phụ để xử lý tăng/giảm số dư
     */
    private function updateBalance(Transaction $transaction, $amount, $isAdding = true)
    {
        $user = User::find($transaction->user_id);
        if (!$user) return;

        // Dựa vào cấu trúc enum của bạn: income là cộng, expense là trừ
        $isIncome = ($transaction->type === 'income'); 

        if ($isAdding) {
            // Khi tạo mới: thu thì cộng, chi thì trừ
            $isIncome ? $user->increment('balance', $amount) : $user->decrement('balance', $amount);
        } else {
            // Khi xóa hoặc hoàn tác: thu thì trừ lại, chi thì cộng lại
            $isIncome ? $user->decrement('balance', $amount) : $user->increment('balance', $amount);
        }
    }

    public function created(Transaction $transaction)
    {
        // 1. Cập nhật số dư người dùng
        $this->updateBalance($transaction, $transaction->amount, true);

        // 2. Ghi Log
        TransactionLog::create([
            'user_id'        => Auth::id() ?? $transaction->user_id,
            'transaction_id' => $transaction->id,
            'action'         => 'created',
            'description'    => $transaction->description,
            'note'           => 'Thêm mới giao dịch: ' . $transaction->description,
            'old_amount'     => null,
            'new_amount'     => $transaction->amount,
            'changed_at'     => now(),
        ]);
    }

    public function updating(Transaction $transaction)
    {
        // Nếu số tiền thay đổi, cần tính toán lại balance
        if ($transaction->isDirty('amount')) {
            $oldAmount = $transaction->getOriginal('amount');
            $newAmount = $transaction->amount;

            // Trả lại số tiền cũ về trạng thái trước đó
            $this->updateBalance($transaction, $oldAmount, false);
            // Áp dụng số tiền mới
            $this->updateBalance($transaction, $newAmount, true);
        }

        // Logic ghi log giữ nguyên như bạn đã viết
        if ($transaction->isDirty(['amount', 'description', 'category_id'])) {
            $changes = [];
            if ($transaction->isDirty('amount')) {
                $changes[] = "Tiền: " . number_format($transaction->getOriginal('amount')) . " -> " . number_format($transaction->amount);
            }
            if ($transaction->isDirty('description')) {
                $changes[] = "Mô tả: '" . $transaction->getOriginal('description') . "' -> '" . $transaction->description . "'";
            }

            TransactionLog::create([
                'user_id'        => Auth::id() ?? $transaction->user_id,
                'transaction_id' => $transaction->id,
                'action'         => 'updated',
                'description'    => $transaction->description,
                'note'           => 'Sửa: ' . implode(' | ', $changes),
                'old_amount'     => $transaction->getOriginal('amount'),
                'new_amount'     => $transaction->amount,
                'changed_at'     => now(),
            ]);
        }
    }

    public function deleted(Transaction $transaction)
    {
        // Khi xóa giao dịch, phải hoàn tác lại số tiền trong balance
        $this->updateBalance($transaction, $transaction->amount, false);

        // Ghi Log
        TransactionLog::create([
            'user_id'        => Auth::id() ?? $transaction->user_id,
            'transaction_id' => $transaction->id,
            'action'         => 'deleted',
            'description'    => $transaction->description,
            'note'           => 'Xóa giao dịch: ' . $transaction->description,
            'old_amount'     => $transaction->amount,
            'new_amount'     => null,
            'changed_at'     => now(),
        ]);
    }
}