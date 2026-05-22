<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl p-6">
                
                {{-- THANH TIÊU ĐỀ --}}
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold" style="background: linear-gradient(90deg, #ec4899, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        📜 Nhật ký thay đổi giao dịch
                    </h2>
                    
                    <a href="{{ route('transactions.index') }}" 
                       class="btn-back-gradient flex items-center gap-2 px-4 py-2 text-white rounded-xl transition font-bold text-sm shadow-md">
                        ⬅️ Quay lại trang giao dịch
                    </a>
                </div>

                {{-- BẢNG --}}
                <div class="w-full">
                    <table class="w-full text-left border-collapse table-auto text-base">

                        <thead>
                            <tr class="border-b text-gray-400 uppercase text-sm tracking-wider">
                                <th class="py-3 px-2 w-1">HÀNH&nbsp;ĐỘNG</th>
                                <th class="py-3 px-4">GIAO&nbsp;DỊCH</th>
                                <th class="py-3 px-2 text-right w-1">CŨ</th>
                                <th class="py-3 px-2 text-right w-1">MỚI</th>
                                <th class="py-3 px-2 text-right w-1">THỜI&nbsp;GIAN</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($logs as $log)

                        <tr class="border-b hover:bg-pink-50 transition">

                            {{-- 1. HÀNH ĐỘNG --}}
                            <td class="py-4 px-2">
                                @php 
                                    $action = strtolower($log->action); 
                                @endphp

                                @if(str_contains($action, 'search'))
                                    <span class="inline-block px-2 py-1 rounded-lg text-xs font-bold uppercase whitespace-nowrap border" 
                                          style="background-color: #fef9c3; color: #ca8a04; border-color: #fef08a;">
                                        Tìm kiếm
                                    </span>

                                @elseif($action == 'updated' || $action == 'chỉnh sửa')
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-bold uppercase whitespace-nowrap">
                                        Chỉnh sửa
                                    </span>

                                @elseif($action == 'created')
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-600 rounded-lg text-xs font-bold uppercase whitespace-nowrap">
                                        Thêm mới
                                    </span>

                                @elseif($action == 'deleted')
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-bold uppercase whitespace-nowrap">
                                        Đã xóa
                                    </span>

                                @else
                                    <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold uppercase whitespace-nowrap">
                                        {{ $log->action }}
                                    </span>
                                @endif
                            </td>

                            {{-- 2. GIAO DỊCH --}}
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-700 text-base break-words leading-snug">
                                    {{ $log->description ?? '---' }}
                                </div>
                            </td>

                            {{-- 3. CŨ --}}
                            <td class="py-4 px-2 text-gray-400 italic text-right whitespace-nowrap text-base">
                                {{ $log->old_amount ? number_format($log->old_amount, 0, ',', '.') : '-' }}
                            </td>

                            {{-- 4. MỚI --}}
                            <td class="py-4 px-2 font-bold text-purple-600 text-right whitespace-nowrap text-base">
                                @if(str_contains($action, 'delete'))
                                    <span class="text-gray-300 font-normal">-</span>
                                @else
                                    {{ $log->new_amount ? number_format($log->new_amount, 0, ',', '.') : '-' }}
                                @endif
                            </td>

                            {{-- 5. THỜI GIAN --}}
                            <td class="py-4 px-2 text-sm text-gray-400 text-right whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($log->changed_at)->diffForHumans() }}
                            </td>

                        </tr>

                        @endforeach
                        </tbody>

                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-back-gradient {
            background: linear-gradient(45deg, #f43f5e, #fb8c00); 
            border: none;
            transition: all 0.3s ease;
        }
        .btn-back-gradient:hover {
            background: linear-gradient(45deg, #fb7185, #ffa726);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.4);
            color: white;
        }
    </style>
</x-app-layout>