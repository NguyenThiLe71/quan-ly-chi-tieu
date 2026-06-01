<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #444; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { color: #d81b60; font-size: 24px; font-weight: bold; }
        
        .summary-box { 
            background-color: #fff5f8; padding: 20px; border-radius: 10px; 
            border: 1px solid #f8bbd0; margin-bottom: 20px;
        }
        
        .table-custom { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-custom th { 
            background-color: #fce4ec; color: #880e4f; padding: 12px; 
            text-align: left; border-bottom: 2px solid #f8bbd0;
        }
        .table-custom td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        .highlight { font-weight: bold; color: #d81b60; }
        
        /* Cảnh báo đỏ */
        .text-danger { color: #d32f2f; font-weight: bold; }

        /* Footer & Paging */
        .footer {
            position: fixed; bottom: 0px; left: 0px; right: 0px; height: 50px; 
            text-align: center; border-top: 1px solid #eee;
            font-size: 10px; color: #777; padding-top: 10px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">BÁO CÁO TÀI CHÍNH</div>
        <p>Tháng {{ $month }}/{{ $year }}</p>
    </div>

    <div class="summary-box">
        <table style="width: 100%;">
            <tr>
                <td>Tổng thu nhập:</td>
                <td class="text-right">{{ number_format($totalIncome) }} đ</td>
            </tr>
            <tr>
                <td>Tổng chi tiêu:</td>
                <td class="text-right highlight">{{ number_format($totalExpense) }} đ</td>
            </tr>
            <tr>
                <td><strong>Số dư:</strong></td>
                <td class="text-right"><strong>{{ number_format($totalIncome - $totalExpense) }} đ</strong></td>
            </tr>
        </table>
    </div>

    <h3>Chi tiết chi tiêu theo danh mục</h3>
    <table class="table-custom">
    <thead>
        <tr>
            <th>Danh mục</th>
            <th class="text-right">Số tiền</th>
            <th class="text-right">% Tổng chi</th>
            <th class="text-right">So với T-1</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupedByCategory as $categoryName => $items)
        @php 
            $catTotal = $items->sum('amount');
            $percent = ($totalExpense > 0) ? ($catTotal / $totalExpense) * 100 : 0;
            
            // Tính % tháng trước
            $prevCatTotal = isset($prevGrouped[$categoryName]) ? $prevGrouped[$categoryName]->sum('amount') : 0;
            $prevPercent = ($prevTotalExpense > 0) ? ($prevCatTotal / $prevTotalExpense) * 100 : 0;
            $diff = $percent - $prevPercent;
        @endphp
        <tr>
            <td>{{ $categoryName }}</td>
            <td class="text-right">{{ number_format($catTotal) }} đ</td>
            <td class="text-right {{ $percent > 50 ? 'text-danger' : '' }}">
                {{ number_format($percent, 1) }}%
            </td>
            <td class="text-right {{ $diff > 0 ? 'text-danger' : 'text-success' }}">
                {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 1) }}%
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

    <h3>Các giao dịch lớn trong tháng</h3>
    <table class="table-custom">
        @foreach($transactions->where('type', 'expense')->sortByDesc('amount')->take(5) as $t)
        <tr>
            <td>{{ $t->description }} ({{ $t->category->name ?? 'Khác' }})</td>
            <td class="text-right">{{ number_format($t->amount) }} đ</td>
        </tr>
        @endforeach
    </table>

    <div class="footer">
        Báo cáo được xuất từ hệ thống quản lý chi tiêu - Ngày: {{ date('d/m/Y') }}
        <div style="float: right;">Trang <span class="page-number"></span></div>
    </div>

</body>
</html>