<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
public function downloadPDF(Request $request)
{
    $month = $request->query('month');
    $year = $request->query('year');

    // Tính toán tháng và năm trước đó
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear = $month == 1 ? $year - 1 : $year;

    // 1. Truy vấn dữ liệu tháng hiện tại
    $transactions = Transaction::where('user_id', Auth::id())
        ->with('category')
        ->whereMonth('transaction_date', $month)
        ->whereYear('transaction_date', $year)
        ->get();

    // 2. Truy vấn dữ liệu tháng trước đó để so sánh
    $prevTransactions = Transaction::where('user_id', Auth::id())
        ->whereMonth('transaction_date', $prevMonth)
        ->whereYear('transaction_date', $prevYear)
        ->get();

    $totalExpense = $transactions->where('type', 'expense')->sum('amount');
    $totalIncome = $transactions->where('type', 'income')->sum('amount');
    
    $prevTotalExpense = $prevTransactions->where('type', 'expense')->sum('amount');

    // 3. Gom nhóm theo danh mục
    $groupedByCategory = $transactions->where('type', 'expense')
        ->groupBy(function($item) {
            return $item->category ? $item->category->name : 'Khác';
        });

    // Gom nhóm tháng trước để lấy dữ liệu so sánh
    $prevGrouped = $prevTransactions->where('type', 'expense')
        ->groupBy(function($item) {
            return $item->category ? $item->category->name : 'Khác';
        });

    $data = [
        'month' => $month,
        'year' => $year,
        'totalExpense' => $totalExpense,
        'totalIncome' => $totalIncome,
        'groupedByCategory' => $groupedByCategory,
        'prevGrouped' => $prevGrouped, // Truyền dữ liệu tháng trước qua view
        'prevTotalExpense' => $prevTotalExpense,
        'transactions' => $transactions
    ];

    $pdf = Pdf::loadView('reports.monthly_pdf', $data);
    return $pdf->stream("Bao_cao_Tai_chinh_{$month}_{$year}.pdf");
}
}