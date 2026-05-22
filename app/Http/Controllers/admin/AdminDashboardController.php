<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\SavingGoal;
use App\Models\UserInteraction;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
       $today = now()->startOfDay();

$yesterday = now()->subDay()->startOfDay();
$yesterdayEnd = now()->subDay()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | 1. NGƯỜI DÙNG
        |--------------------------------------------------------------------------
        */
        $totalUsers = User::count();

        /*
        |--------------------------------------------------------------------------
        | 2. GIAO DỊCH
        |--------------------------------------------------------------------------
        */
        $totalTransactions = Transaction::count();
$interactionGiaoDichToday = UserInteraction::where('module', 'transactions')
    ->where('created_at', '>=', $today)
    ->count();

$interactionGiaoDichYesterday = UserInteraction::where('module', 'transactions')
    ->whereBetween('created_at', [$yesterday, $yesterdayEnd])
    ->count();

        /*
        |--------------------------------------------------------------------------
        | 3. DANH MỤC
        |--------------------------------------------------------------------------
        */
        $totalCategories = Category::count();

       $interactionDanhMucToday = UserInteraction::where('module', 'categories')
    ->where('created_at', '>=', $today)
    ->count();

$interactionDanhMucYesterday = UserInteraction::where('module', 'categories')
    ->whereBetween('created_at', [$yesterday, $yesterdayEnd])
    ->count();

        /*
        |--------------------------------------------------------------------------
        | 4. NGÂN SÁCH
        |--------------------------------------------------------------------------
        */
        $totalBudget = Budget::sum('amount_limit');

        $interactionNganSachToday = UserInteraction::where('module', 'budgets')
    ->where('created_at', '>=', $today)
    ->count();

$interactionNganSachYesterday = UserInteraction::where('module', 'budgets')
    ->whereBetween('created_at', [$yesterday, $yesterdayEnd])
    ->count();

      /*
|--------------------------------------------------------------------------
| 5. MỤC TIÊU TIẾT KIỆM
|--------------------------------------------------------------------------
*/
$totalSavings = SavingGoal::count();

// Đổi 'goals' thành 'saving_goals'
$interactionTietKiemToday = UserInteraction::where('module', 'saving_goals') 
    ->where('created_at', '>=', $today)
    ->count();

$interactionTietKiemYesterday = UserInteraction::where('module', 'saving_goals') 
    ->whereBetween('created_at', [$yesterday, $yesterdayEnd])
    ->count();

        /*
        |--------------------------------------------------------------------------
        | 6. TỔNG SỐ LẦN SEARCH
        |--------------------------------------------------------------------------
        */
        $aiQueries = UserInteraction::where('action', 'search')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 7. HOẠT ĐỘNG GẦN ĐÂY
        |--------------------------------------------------------------------------
        */
        $recentActivities = UserInteraction::with('user')
            ->latest()
            ->take(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. USER NGHI NGỜ SPAM
        |--------------------------------------------------------------------------
        */
        $spamUsers = UserInteraction::select('user_id')
          ->where('created_at', '>=', now()->subDay())
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 100')
            ->pluck('user_id');

        return view('admin.dashboard', compact(
            'totalUsers',

            'totalTransactions',
            'interactionGiaoDichToday',
'interactionGiaoDichYesterday',


            'totalCategories',
           'interactionDanhMucToday',
'interactionDanhMucYesterday',

            'totalBudget',
           'interactionNganSachToday',
'interactionNganSachYesterday',

            'totalSavings',
          'interactionTietKiemToday',
'interactionTietKiemYesterday',

            'aiQueries',

            'recentActivities',

            'spamUsers'
        ));
    }
}