@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- BỘ LỌC HỆ THỐNG -->
    <div class="bg-white p-8 rounded-[40px] shadow-lg border border-blue-50">
        <div class="flex items-center justify-between mb-6">
          <h4 class="text-lg font-bold text-blue-700 flex items-center bg-blue-50 px-4 py-2 rounded-xl w-fit tracking-tight">
    <span class="mr-2">🔍</span> Bộ lọc tìm kiếm
</h4>
            <form action="{{ route('admin.logs.cleanup') }}" method="POST" onsubmit="return confirm('Xóa tất cả nhật ký cũ hơn 30 ngày?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-600 transition">
                    🗑️ Dọn dẹp log cũ
                </button>
            </form>
        </div>

        <form action="{{ route('admin.logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative">
                <select name="user_id" class="w-full appearance-none bg-gray-50 border-none rounded-2xl px-6 py-3.5 text-sm font-medium text-gray-600 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">Tất cả người dùng</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            👤 {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative">
                <select name="action" class="w-full appearance-none bg-gray-50 border-none rounded-2xl px-6 py-3.5 text-sm font-medium text-gray-600 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">Tất cả hành động</option>
                    <option value="CREATED" {{ request('action') == 'CREATED' ? 'selected' : '' }}>Thêm mới (CREATED)</option>
                    <option value="UPDATED" {{ request('action') == 'UPDATED' ? 'selected' : '' }}>Cập nhật (UPDATED)</option>
                    <option value="DELETED" {{ request('action') == 'DELETED' ? 'selected' : '' }}>Xóa dữ liệu (DELETED)</option>
                    <option value="SEARCHED" {{ request('action') == 'SEARCHED' ? 'selected' : '' }}>Tìm kiếm  (SEARCHED)</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-[#7BD5F5] text-white font-black py-3.5 rounded-2xl shadow-md hover:shadow-blue-200 transition uppercase text-xs tracking-widest">
                    Lọc ngay
                </button>
                <a href="{{ route('admin.logs.index') }}" class="px-6 py-3.5 bg-gray-100 text-gray-400 rounded-2xl hover:bg-gray-200 transition flex items-center justify-center">
                    <span class="text-xl">🔄</span>
                </a>
            </div>
        </form>
    </div>

    <!-- DANH SÁCH NHẬT KÝ -->
    <div class="bg-white p-10 rounded-[45px] shadow-lg border border-blue-50">
      <h2 class="text-2xl font-bold flex items-center gap-3 tracking-tight mb-8">
    <span>📜</span> 

    <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-[#2F3C7E]">
        Lịch sử hệ thống
    </span>
</h2>
        <div class="space-y-4">
            <div class="flex text-gray-400 text-xs uppercase font-black px-6 pb-2 border-b border-gray-50">
                <div class="flex-1">Chi tiết hoạt động</div>
                <div class="w-48 text-center">Trạng thái</div>
                <div class="w-32 text-right">Thời gian</div>
            </div>

            @forelse($logs as $log)
            <div class="flex items-center justify-between p-6 bg-white border border-gray-50 hover:bg-blue-50/30 transition shadow-sm rounded-[30px]">
                <div class="flex flex-col flex-1">
                   <span class="text-lg font-black text-gray-800">
    👤 {{ $log->user->name ?? 'Hệ thống' }}
</span>
                {{-- Đoạn code MỚI --}}
<span class="text-sm text-gray-500 italic mt-1 ml-6">
    @if($log->note)
        {{-- Ưu tiên hiện nội dung chi tiết từ Controller --}}
        {{ $log->note }}
    @elseif(str_contains(strtolower($log->action), 'search'))
        {{-- Nếu không có note thì mới hiện keyword dự phòng --}}
        🔍 "{{ $log->keyword }}"
    @else
        Không có chi tiết
    @endif
</span>
                    
                    <!-- 🔥 PHẦN HIỂN THỊ CHI TIẾT CHO ADMIN -->
                    @if(strtolower($log->action) == 'updated' && isset($log->old_amount) && isset($log->new_amount))
                        <div class="mt-2 ml-6 flex items-center gap-2 text-xs font-bold">
                            <span class="px-2 py-1 bg-red-50 text-red-400 rounded-lg line-through">{{ number_format($log->old_amount) }}đ</span>
                            <span class="text-gray-400">➞</span>
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded-lg">{{ number_format($log->new_amount) }}đ</span>
                        </div>
                    @elseif(strtolower($log->action) == 'deleted' && isset($log->old_amount))
                         <div class="mt-2 ml-6 text-xs font-bold">
                            <span class="text-gray-400">Giá trị đã xóa:</span>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg ml-1">{{ number_format($log->old_amount) }}đ</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-6">
                    @php
                        $badgeColor = match(strtolower($log->action)) {
                            'created', 'created_transaction', 'created_category' => 'bg-green-100 text-green-500',
                            'updated', 'updated_transaction', 'updated_category' => 'bg-orange-100 text-orange-500',
                            'deleted', 'deleted_transaction' => 'bg-red-100 text-red-500',
                            'search', 'searched' => 'bg-purple-100 text-purple-500',
                            default => 'bg-gray-100 text-gray-400',
                        };
                    @endphp
                    <div class="w-48 flex justify-center">
                        <span class="{{ $badgeColor }} px-4 py-1.5 rounded-2xl text-[10px] font-black tracking-widest uppercase shadow-sm">
                            {{ $log->action }}
                        </span>
                    </div>

                    <span class="text-xs text-gray-400 font-medium w-32 text-right">
                      {{ $log->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-20 text-gray-400 font-medium">
                🚫 Không tìm thấy hoạt động nào phù hợp!
            </div>
            @endforelse
        </div>

        @if($logs->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $logs->appends(request()->query())->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection