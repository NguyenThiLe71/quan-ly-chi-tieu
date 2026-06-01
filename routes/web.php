<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminSpamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController; // Thêm dòng này vào

/*
|--------------------------------------------------------------------------
| 1. Trang gốc (Public/Redirect)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin');
        }
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| 2. Nhóm Route cho USER (Đã đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Giao dịch (Transactions)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Danh mục (Categories)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Ngân sách (Budgets)
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::put('/budgets/{id}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{id}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Thông báo (Notifications)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.delete');

    // Trang tĩnh / View khác

Route::middleware('auth')->group(function () {

    Route::get('/goals', [SavingGoalController::class, 'index'])->name('goals.index');

    Route::post('/goals', [SavingGoalController::class, 'store'])->name('goals.store');

    // Dòng này để xử lý nạp/rút tiền (Icon Ví)
    Route::post('/goals/{id}/add-money', [SavingGoalController::class, 'addMoney'])->name('goals.addMoney');

    // THÊM DÒNG NÀY: Để xử lý sửa thông tin (Icon Bút chì)
    Route::put('/goals/{id}', [SavingGoalController::class, 'update'])->name('goals.update');

    Route::delete('/goals/{id}', [SavingGoalController::class, 'destroy'])->name('goals.destroy');

});

Route::middleware('auth')->group(function () {
    Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
    Route::post('/insights/analyze', [InsightController::class, 'analyze']);
    
    // --- THÊM DÒNG NÀY VÀO ---
    // Sửa từ post thành get
Route::get('/insights/compare', [InsightController::class, 'compare']);
    // -------------------------
    
    Route::post('/insights/chat-proxy', [InsightController::class, 'chatProxy']);
    
    Route::get('/reports/download', [ReportController::class, 'downloadPDF'])->name('reports.download');
});
    
Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    // Hồ sơ (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

}); // <--- ĐÓNG NGOẶC CỦA NHÓM AUTH TẠI ĐÂY

/*
|--------------------------------------------------------------------------
| 3. Nhóm Route cho ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.') // Tất cả route bên trong sẽ có tiền tố "admin."
    ->group(function () {

        // 📊 Dashboard admin
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // 📜 Logs
        Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');
        Route::delete('/logs/cleanup', [AdminLogController::class, 'cleanup'])->name('logs.cleanup');

        // 🚨 SPAM CONTROL
        Route::get('/spam', [AdminSpamController::class, 'index'])->name('spam.index');
        Route::post('/spam/warn/{id}', [AdminSpamController::class, 'warn'])->name('spam.warn');
        Route::post('/spam/toggle/{userId}', [AdminSpamController::class, 'toggleStatus'])
    ->name('spam.toggle');
        // 👥 Quản lý người dùng (Sửa lại ở đây)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users/toggle/{id}', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');

       // 📂 Quản lý danh mục hệ thống
Route::get('/categories-manager', [AdminCategoryController::class, 'index'])->name('categories.index');
Route::post('/categories-manager', [AdminCategoryController::class, 'store'])->name('categories.store');
Route::put('/categories-manager/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories-manager/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
// 📢 QUẢN LÝ THÔNG BÁO HỆ THỐNG
        Route::get('/notifications-manager', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications-manager', [AdminNotificationController::class, 'store'])->name('notifications.store');
        Route::delete('/notifications-manager/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    });
/*
|--------------------------------------------------------------------------
| 4. Auth Routes mặc định
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
Route::post('/webhook/payos', [\App\Http\Controllers\WebhookController::class, 'handle']);
Route::get('/clear-everything', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    
    return "Đã xóa sạch cache!";
});