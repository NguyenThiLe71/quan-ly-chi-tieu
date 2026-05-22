@extends('layouts.admin')

@section('content')
<style>
    /* Hiệu ứng thẻ lơ lửng */
    .stat-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -12px rgba(120, 127, 246, 0.15);
    }
    /* Banner AI lung linh */
    .banner-ai {
        background: linear-gradient(135deg, #7BD5F5 0%, #787FF6 100%);
    }
</style>

<div class="mb-10 animate-fade pt-4"> 
    <h2 class="text-4xl font-bold tracking-tight flex items-center gap-3 leading-normal">
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 via-gray-900 via-gray-900 via-gray-900 to-pink-600 py-2">
            Bảng điều khiển tổng quát
        </span>
        <span class="animate-bounce">🚀</span>
    </h2>
</div>
</div>

<!-- HÀNG 1: NGƯỜI DÙNG -->
<div class="mb-10 flex justify-center">
    <div class="stat-card bg-white p-8 rounded-[40px] shadow-sm border-t-8 border-[#7BD5F5] w-full md:w-1/3 text-center">

        <p class="text-gray-700 font-extrabold uppercase text-sm tracking-widest mb-2 text-center">
            Người dùng hệ thống
        </p>

        <h3 class="text-4xl font-black text-yellow-500 drop-shadow-sm">
            {{ number_format($totalUsers) }}
        </h3>

    </div>
</div>

<!-- HÀNG 2: CHI TIẾT CÁC MỤC NGHIỆP VỤ -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
    
    <!-- Cụm Quản lý Giao dịch -->
    <div class="stat-card bg-white p-8 rounded-[40px] shadow-md border-l-8 border-[#787FF6]">
        <h4 class="font-black text-gray-800 mb-4 flex items-center">💰 QUẢN LÝ GIAO DỊCH</h4>
        <div class="grid grid-cols-2 gap-4 border-t pt-4">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase">Tổng giao dịch</p>
                <p class="text-2xl font-black text-gray-800">{{ number_format($totalTransactions) }}</p>
            </div>
            <div>
              <p class="text-blue-500 text-[10px] font-bold uppercase">Hôm nay</p>

<p class="text-2xl font-black text-blue-600">
    +{{ number_format($interactionGiaoDichToday) }}
</p>

<p class="text-[11px] text-gray-400">
    Hôm qua: {{ number_format($interactionGiaoDichYesterday) }}
</p>
            </div>
        </div>
    </div>

    <!-- Cụm Danh mục hệ thống -->
    <div class="stat-card bg-white p-8 rounded-[40px] shadow-md border-l-8 border-[#f472b6]">
        <h4 class="font-black text-gray-800 mb-4 flex items-center">📂 QUẢN LÝ DANH MỤC</h4>
        <div class="grid grid-cols-2 gap-4 border-t pt-4">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase">Tổng danh mục</p>
                <p class="text-2xl font-black text-gray-800">{{ number_format($totalCategories) }}</p>
            </div>
            <div>
                <p class="text-pink-500 text-[10px] font-bold uppercase">Hôm nay</p>

<p class="text-2xl font-black text-pink-600">
    +{{ number_format($interactionDanhMucToday) }}
</p>

<p class="text-[11px] text-gray-400">
    Hôm qua: {{ number_format($interactionDanhMucYesterday) }}
</p>
            </div>
        </div>
    </div>

    <!-- Cụm Ngân sách -->
    <div class="stat-card bg-white p-8 rounded-[40px] shadow-md border-l-8 border-[#10b981]">
        <h4 class="font-black text-gray-800 mb-4 flex items-center">📉 NGÂN SÁCH NIÊM YẾT</h4>
        <div class="grid grid-cols-2 gap-4 border-t pt-4">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase">Tổng giới hạn</p>
                <p class="text-2xl font-black text-gray-800">{{ number_format($totalBudget) }}</p>
            </div>
            <div>
                <p class="text-green-500 text-[10px] font-bold uppercase">Hôm nay</p>

<p class="text-2xl font-black text-green-600">
    +{{ number_format($interactionNganSachToday) }}
</p>

<p class="text-[11px] text-gray-400">
    Hôm qua: {{ number_format($interactionNganSachYesterday) }}
</p>
            </div>
        </div>
    </div>

    <!-- Cụm Mục tiêu tiết kiệm -->
    <div class="stat-card bg-white p-8 rounded-[40px] shadow-md border-l-8 border-[#f59e0b]">
        <h4 class="font-black text-gray-800 mb-4 flex items-center">🎯 MỤC TIÊU TIẾT KIỆM</h4>
        <div class="grid grid-cols-2 gap-4 border-t pt-4">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase">Tổng mục tiêu</p>
                <p class="text-2xl font-black text-gray-800">{{ number_format($totalSavings) }}</p>
            </div>
            <div>
                <p class="text-orange-500 text-[10px] font-bold uppercase">Hôm nay</p>

<p class="text-2xl font-black text-orange-600">
    +{{ number_format($interactionTietKiemToday) }}
</p>

<p class="text-[11px] text-gray-400">
    Hôm qua: {{ number_format($interactionTietKiemYesterday) }}
</p>
            </div>
        </div>
    </div>
</div>

<!-- Banner AI điều hành -->
<div class="banner-ai p-12 rounded-[50px] text-white shadow-2xl flex flex-col md:flex-row justify-between items-center relative overflow-hidden group">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
    
    <div class="relative z-10">
        <h3 class="text-4xl font-bold font-itim text-white italic">Trung tâm AI Finance</h3>
        <p class="opacity-90 mt-2 max-w-md font-light italic text-sm">
            Hệ thống đã xử lý tổng cộng {{ number_format($aiQueries) }} yêu cầu tìm kiếm thông minh và phân tích dữ liệu thời gian thực.
        </p>
    </div>
    
    <button class="relative z-10 mt-6 bg-white text-[#787FF6] px-10 py-5 rounded-[30px] font-black hover:scale-110 hover:shadow-2xl transition-all duration-300 uppercase text-xs tracking-widest">
        ✨ Huấn luyện mô hình
    </button>
</div>

<div class="mt-10 h-1 w-full bg-gradient-to-r from-transparent via-blue-100 to-transparent rounded-full"></div>
<div class="mt-12 animate-fade-up">
    <div class="flex justify-between items-center mb-6 px-4">
        <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center">
    <span class="mr-3 text-indigo-600">🔔</span> Hoạt động mới nhất
</h3>
        <a href="{{ route('admin.logs.index') }}" class="bg-gray-100 hover:bg-[#787FF6] hover:text-white text-gray-500 px-6 py-2 rounded-full text-xs font-bold transition-all duration-300 uppercase tracking-widest">
            Xem tất cả
        </a>
    </div>

  <div class="space-y-4">
    @forelse($recentActivities as $activity)

        @php
            $action = strtolower($activity->action);

            switch($action) {
                case 'create':
                case 'created':
                    $icon = '➕';
                    $colorClass = 'text-green-600';
                    $bgClass = 'bg-green-50';
                    $badgeClass = 'bg-green-500';
                    $actionText = 'CREATE';
                    break;

                case 'update':
                case 'updated':
                    $icon = '📝';
                    $colorClass = 'text-orange-600';
                    $bgClass = 'bg-orange-50';
                    $badgeClass = 'bg-orange-500';
                    $actionText = 'UPDATE';
                    break;

                case 'delete':
                case 'deleted':
                    $icon = '🗑️';
                    $colorClass = 'text-red-600';
                    $bgClass = 'bg-red-50';
                    $badgeClass = 'bg-red-500';
                    $actionText = 'DELETE';
                    break;

                default:
    $icon = '🔎';
    $colorClass = 'text-purple-600';
    $bgClass = 'bg-purple-50';
    $badgeClass = 'bg-purple-500';
    $actionText = 'SEARCH';
            }
        @endphp

        <div class="stat-card bg-white p-6 rounded-[35px] shadow-sm border border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">

            <div class="flex items-center gap-5">

                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl {{ $bgClass }} {{ $colorClass }} shadow-inner">
                    {{ $icon }}
                </div>

                <div>
                    <div class="flex items-center gap-3">
                        <span class="font-black text-gray-800 text-lg">
                            {{ $activity->user->name ?? 'Người dùng' }}
                        </span>

                        <span class="text-[10px] px-3 py-1 rounded-full font-black text-white uppercase {{ $badgeClass }}">
                            {{ $actionText }}
                        </span>
                    </div>

                    <p class="text-blue-600 text-sm mt-1 font-bold italic">
                        🧩 Module: {{ ucfirst($activity->module) }}
                    </p>

                    @if($activity->keyword)
                        <p class="text-gray-400 text-[10px] mt-1 bg-gray-50 px-2 py-0.5 rounded-md inline-block">
                            🔍 "{{ $activity->keyword }}"
                        </p>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <p class="text-gray-400 text-[11px] font-bold">
                   {{ $activity->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y') }}
                </p>

              <p class="text-[11px] font-black {{ $colorClass }} italic mt-1">
    {{ $activity->created_at->timezone('Asia/Ho_Chi_Minh')->diffForHumans() }}
</p>
            </div>
        </div>

    @empty
        <div class="bg-gray-50 p-10 rounded-[40px] text-center border-2 border-dashed">
            <p class="text-gray-400 font-bold italic">
                Chưa có hoạt động nào 🍃
            </p>
        </div>
    @endforelse
</div>
</div>
@endsection