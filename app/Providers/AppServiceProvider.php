<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Thêm 2 dòng này để Laravel hiểu Model và Observer là gì
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
   Paginator::useTailwind();
    Transaction::observe(TransactionObserver::class);

    view()->composer('*', function ($view) {
        // Thay auth()->check() bằng Auth::check()
        if (Auth::check()) {
            // Thay auth()->id() bằng Auth::id()
            $userId = Auth::id(); 

            $notifications = \App\Models\Notification::where('user_id', $userId)
    ->orderBy('id', 'desc') // Ép lấy theo ID mới nhất lên đầu 100%
    ->take(10)
    ->get();

            $unreadCount = \App\Models\Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            $view->with([
                'latestNotifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
        }
    });
}
}