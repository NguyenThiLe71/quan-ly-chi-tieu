<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full" style="min-height: 50px;">
            <div class="flex-shrink-0">
                <h2 class="dashboard-title m-0 text-2xl font-black text-gray-800 flex items-center gap-2">
                    👋 Xin chào, {{ Auth::user()->name }}
                </h2>
                <p class="text-muted m-0 text-sm opacity-70">
                    Tổng quan tài chính của bạn hôm nay
                </p>
            </div>

            @if(isset($topGoal))
                <a href="{{ route('goals.index') }}" class="no-underline group ml-auto flex-shrink-0" style="text-decoration: none !important;">
                    <div class="flex items-center rounded-full border-2 border-pink-200
                                bg-gradient-to-r from-pink-100 via-pink-50 to-white
                                shadow-[0_0_20px_5px_rgba(236,72,153,0.25)] 
                                transition-all duration-300" 
                         style="padding: 8px 20px; display: flex; align-items: center; gap: 15px;">
                        
                        <div style="font-size: 24px;">🐷</div>

                        <div style="display: flex; align-items: center; gap: 20px; min-width: 200px;">
                            <span style="font-size: 14px; font-weight: 800; color: #374151; white-space: nowrap;">
                                {{ $topGoal->name }}
                            </span>
                            
                            <div style="background-color: #ec4899; color: white; font-size: 11px; font-weight: 900; 
                                        padding: 2px 10px; border-radius: 9999px; flex-shrink: 0;
                                        box-shadow: 0 2px 10px rgba(236, 72, 153, 0.4); display: block !important;">
                                {{ $percent }}%
                            </div>
                        </div>

                        <div style="color: #ec4899; display: flex; align-items: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div id="toast-success" class="toast-success">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById("toast-success");
                if(toast) toast.style.display="none";
            }, 3000);
        </script>
    @endif

    <style>
        .dashboard-title {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(90deg, #ec4899, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeSlide 0.6s ease;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-hover { transition: all 0.25s ease; }
        .card-hover:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }
        .toast-success {
            position: fixed; top: 20px; right: 20px;
            background: linear-gradient(90deg,#ec4899,#a855f7);
            color: white; padding: 14px 22px; border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2); z-index: 9999;
        }
        .card-rose-white {
            background-color: #fffafb !important; 
            border: 1px solid #fce7f3 !important;
            border-radius: 0.75rem !important; 
            transition: all 0.3s ease;
        }
        .marker-highlight {
            position: relative;
            display: inline-block;
            color: #bd0606 !important;
            font-weight: 900 !important;
            z-index: 1;
            padding: 0 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .marker-highlight::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 3px; 
            width: 100%;
            height: 45%; 
            background-color: rgba(244, 114, 182, 0.35);
            z-index: -1; 
            transform: rotate(-1deg);
            border-radius: 2px;
        }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Thống kê nhanh --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="card-hover p-6 rounded-xl flex justify-between items-center" style="background: linear-gradient(135deg,#fce7f3,#ffffff);">
                    <div>
                        <p class="text-gray-500 text-sm">Số dư</p>
                        <p class="text-3xl font-bold text-pink-500">{{ number_format($balance ?? 0,0,',','.') }} đ</p>
                    </div>
                    <div class="bg-pink-100 p-3 rounded-full">💰</div>
                </div>
                <div class="card-hover p-6 rounded-xl flex justify-between items-center" style="background: linear-gradient(135deg,#ede9fe,#ffffff);">
                    <div>
                        <p class="text-gray-500 text-sm">Tổng thu</p>
                        <p class="text-3xl font-bold text-purple-500">{{ number_format($income ?? 0,0,',','.') }} đ</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">📈</div>
                </div>
                <div class="card-hover p-6 rounded-xl flex justify-between items-center" style="background: linear-gradient(135deg,#ffe4e6,#ffffff);">
                    <div>
                        <p class="text-gray-500 text-sm">Tổng chi</p>
                        <p class="text-3xl font-bold text-red-500">{{ number_format($expense ?? 0,0,',','.') }} đ</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">📉</div>
                </div>
            </div>

            {{-- Biểu đồ --}}
            <div class="card-rose-white p-6 shadow-md mb-8 card-hover">
                <h3 class="fw-bold mb-4 flex items-center gap-2">
                    <span>📊</span> <span class="marker-highlight">Phân tích chi tiêu</span>
                </h3>
                <div class="flex justify-center">
                    <div style="width: 400px; height: 400px; position: relative;">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Bảng giao dịch --}}
            <div class="card-rose-white p-6 shadow-md card-hover">
                <h3 class="fw-bold mb-4 flex items-center gap-2">
                    <span>🧾</span> <span class="marker-highlight">Top 3 chi tiêu mới nhất</span>
                </h3>
                <table class="w-full border-separate border-spacing-y-3">
                    <thead>
                        <tr>
                            <th style="color: #03717f !important; font-weight: 900 !important;" class="uppercase text-xs tracking-wider pb-2 text-left pl-8">Danh mục</th>
                            <th style="color: #03717f !important; font-weight: 900 !important;" class="uppercase text-xs tracking-wider pb-2 text-center">Số tiền</th>
                            <th style="color: #03717f !important; font-weight: 900 !important;" class="uppercase text-xs tracking-wider pb-2 text-center">Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tran)
                            <tr class="odd:bg-white even:bg-[#fff1f2] shadow-sm transition-all duration-300 hover:shadow-md">
                                <td class="font-bold text-pink-500 py-4 pl-8 rounded-l-2xl">{{ $tran->category->name ?? 'Khác' }}</td>
                                <td class="text-red-500 font-black text-center py-4">-{{ number_format($tran->amount, 0, ',', '.') }} đ</td>
                                <td class="text-gray-400 text-sm text-center italic py-4 px-4 rounded-r-2xl">
                                    {{ \Carbon\Carbon::parse($tran->transaction_date)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-pink-200 py-10 italic">Không có dữ liệu chi tiêu ✨</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
    // 1. Dùng dấu nháy đơn bao quanh để VS Code hiểu đây là string, sau đó parse sang JSON
    const chartLabels = JSON.parse('{!! json_encode($labels ?? []) !!}');
    const chartData = JSON.parse('{!! json_encode($totals ?? []) !!}');
    
    const luxuryPalette = ['#ff4d94', '#a855f7', '#4ade80', '#3b82f6', '#fbbf24', '#f43f5e', '#06b6d4', '#8b5cf6', '#f97316', '#14b8a6'];

    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels: chartLabels, 
            datasets: [{
                // Dùng biến JS đã khai báo ở trên thay vì gọi trực tiếp Blade vào đây
                data: chartLabels.length > 0 ? chartData : [1],
                backgroundColor: chartLabels.length > 0 
                    ? chartLabels.map((_, i) => luxuryPalette[i % luxuryPalette.length]) 
                    : ['#fce7f3'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true, 
                        pointStyle: 'rectRounded',
                        padding: 15,
                        font: { family: 'Itim, sans-serif', size: 13, weight: '600' },
                        color: '#4b5563'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#fce7f3',
                    borderWidth: 1
                }
            }
        }
    });
</script>
</x-app-layout>