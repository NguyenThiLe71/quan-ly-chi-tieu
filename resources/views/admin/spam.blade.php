@extends('layouts.admin')

@section('content')
<div class="py-10 animate-fade">
    <div class="max-w-7xl mx-auto px-4">

{{-- THÔNG BÁO --}}
@if(session('success'))
    <div class="mb-6 animate-bounce-short">
        <div class="bg-green-500 text-white px-6 py-4 rounded-2xl shadow-lg shadow-green-200 flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-2xl mr-3">✅</span>
                <span class="font-black uppercase text-sm tracking-widest">
                    {{ session('success') }}
                </span>
            </div>

            <button onclick="this.parentElement.parentElement.remove()"
                    class="text-white/50 hover:text-white transition-colors">
                ✕
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 animate-bounce-short">
        <div class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg shadow-red-200 flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-2xl mr-3">🚫</span>
                <span class="font-black uppercase text-sm tracking-widest">
                    {{ session('error') }}
                </span>
            </div>

            <button onclick="this.parentElement.parentElement.remove()"
                    class="text-white/50 hover:text-white transition-colors">
                ✕
            </button>
        </div>
    </div>
@endif

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-8">

          <h2 class="text-3xl font-bold flex items-center gap-3 tracking-tight">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-10 w-10 text-[#2F3C7E]"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

            </svg>

            <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-[#2F3C7E]">
                Bảng theo dõi & chống Spam
            </span>

          </h2>

            <div class="text-sm text-gray-500 font-bold bg-white px-6 py-2 rounded-full shadow-sm border border-gray-50">
                Tổng cộng:
                <span class="text-red-500">{{ count($users) }}</span>
                user đang theo dõi
            </div>
        </div>

        {{-- TABLE --}}
<div class="bg-white shadow-xl rounded-[40px] overflow-hidden border-b-8 border-red-400">

    <table class="w-full table-fixed border-collapse">

        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-[11px] uppercase text-gray-400 tracking-widest font-black">

                <th class="py-5 px-4 w-[18%]">Người dùng</th>

                <th class="py-5 px-4 w-[22%]">Email</th>

                <th class="py-5 px-4 w-[25%] text-center">
                    Phân tích hành động
                </th>

                <th class="py-5 px-4 w-[12%] text-center border-l border-gray-100">
                    Mức độ
                </th>

                <th class="py-5 px-4 w-[12%] text-center">
                    Trạng thái
                </th>

                <th class="py-5 px-4 w-[11%] text-center">
                    Thao tác
                </th>

            </tr>
        </thead>

        <tbody class="divide-y divide-gray-50">

        @foreach($users as $u)

            @php

                $recentCount = $u->actions_last_minute ?? 0;

                if ($recentCount < 10) {
                    $status = 'An toàn';
                    $color = 'bg-green-100 text-green-600 border-green-200';
                } elseif ($recentCount < 20) {
                    $status = 'Cảnh báo';
                    $color = 'bg-yellow-100 text-yellow-600 border-yellow-200';
                } else {
                    $status = 'Nguy hiểm';
                    $color = 'bg-red-100 text-red-600 border-red-200';
                }

            @endphp

            <tr class="hover:bg-blue-50/30 transition-all duration-300">

                {{-- USER --}}
                <td class="py-6 px-4">

                    <div class="font-black text-gray-800 text-[15px] leading-tight break-words">
                        {{ $u->name }}
                    </div>

                    <div class="text-[10px] text-gray-400 uppercase tracking-tighter mt-1">
                        ID: #{{ $u->id }}
                    </div>

                </td>

                {{-- EMAIL --}}
                <td class="py-6 px-4">

                    <div class="text-gray-500 text-sm italic font-medium break-all">
                        {{ $u->email }}
                    </div>

                </td>

                {{-- PHÂN TÍCH --}}
                <td class="py-6 px-4">

                    <div class="flex flex-col items-center">

                        <div class="text-4xl font-black text-indigo-600 leading-none">
                            {{ $u->total_actions ?? 0 }}
                        </div>

                        <div class="text-[9px] font-black uppercase mt-2
                            {{ $recentCount > 0 ? 'text-red-400 animate-pulse' : 'text-gray-300' }}">
                            ⚡ {{ $recentCount }} req/min
                        </div>

                        <div class="grid grid-cols-2 gap-x-5 gap-y-1 pt-3 border-t border-gray-100 mt-3 text-[11px]">

                            <div class="font-black text-green-600">
                                ➕ {{ $u->total_create ?? 0 }}
                            </div>

                            <div class="font-black text-orange-500">
                                📝 {{ $u->total_update ?? 0 }}
                            </div>

                            <div class="font-black text-red-500">
                                🗑️ {{ $u->total_delete ?? 0 }}
                            </div>

                            <div class="font-black text-purple-500">
                                🔎 {{ $u->total_search ?? 0 }}
                            </div>

                        </div>

                    </div>

                </td>

                {{-- MỨC ĐỘ --}}
                <td class="py-6 px-4 text-center border-l border-gray-50">

                    <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase border shadow-sm {{ $color }}">
                        {{ $status }}
                    </span>

                </td>

                {{-- STATUS --}}
                <td class="py-6 px-4 text-center">

                    @if($u->status == 1)

                        <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-600 text-[10px] font-black uppercase border border-green-200 shadow-sm">
                            🟢 Active
                        </span>

                    @else

                        <span class="px-3 py-1.5 rounded-full bg-red-100 text-red-600 text-[10px] font-black uppercase border border-red-200 shadow-sm">
                            🔒 Locked
                        </span>

                    @endif

                </td>

                {{-- ACTION --}}
                <td class="py-6 px-4">

                    <div class="flex flex-col items-center gap-2">

                        {{-- WARN --}}
                        <form action="{{ route('admin.spam.warn', $u->id) }}"
                              method="POST"
                              class="w-full">

                            @csrf

                            <button
                                class="w-full px-3 py-2 bg-yellow-400 text-white text-[10px] font-black rounded-xl hover:scale-105 transition-all shadow-md uppercase">

                                ⚠ Warn

                            </button>

                        </form>

                        {{-- TOGGLE --}}
                        <form action="{{ route('admin.spam.toggle', $u->id) }}"
                              method="POST"
                              class="w-full">

                            @csrf

                            <button
                                class="w-full px-3 py-2 text-white text-[10px] font-black rounded-xl hover:scale-105 transition-all shadow-md uppercase

                                {{ $u->status == 1
                                    ? 'bg-red-500 hover:bg-red-600'
                                    : 'bg-green-500 hover:bg-green-600'
                                }}">

                                @if($u->status == 1)
                                    🔒 Lock
                                @else
                                    🔓 Unlock
                                @endif

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

        </div>

        {{-- FOOTER --}}
        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center px-4">

            <div class="flex items-center space-x-6">

                <div class="flex items-center">

                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>

                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        AI Scanner Active
                    </span>

                </div>

                <div class="text-[10px] font-bold text-gray-400 uppercase">
                    Tốc độ xử lý:
                    <span class="text-indigo-500">Real-time</span>
                </div>

            </div>

            <div class="text-[10px] font-bold text-gray-400 italic">
                🛡️ Spam > 20 thao tác/phút sẽ bị đánh dấu
                <span class="text-red-500 font-black underline">
                    Nguy hiểm
                </span>
            </div>

        </div>

    </div>
</div>
@endsection