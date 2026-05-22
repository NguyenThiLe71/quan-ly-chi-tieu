<nav x-data="{ open: false }" class="bg-white border-b border-pink-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-pink-500" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" 
        :active="request()->routeIs('dashboard')" 
        :class="request()->routeIs('dashboard') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        Home
    </x-nav-link>

    <x-nav-link :href="route('transactions.index')" 
        :active="request()->routeIs('transactions.*')" 
        :class="request()->routeIs('transactions.*') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        Giao dịch
    </x-nav-link>

    <x-nav-link :href="route('categories.index')" 
        :active="request()->routeIs('categories.*')" 
        :class="request()->routeIs('categories.*') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        Danh mục
    </x-nav-link>

    <x-nav-link :href="route('budgets.index')" 
        :active="request()->routeIs('budgets.*')" 
        :class="request()->routeIs('budgets.*') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        Ngân sách
    </x-nav-link>

    <x-nav-link :href="route('goals.index')" 
        :active="request()->routeIs('goals.*')" 
        :class="request()->routeIs('goals.*') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        Mục tiêu tiết kiệm
    </x-nav-link>

    <x-nav-link :href="route('statistics.index')" 
        :active="request()->routeIs('statistics.*')" 
        :class="request()->routeIs('statistics.*') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        Thống kê
    </x-nav-link>

    <x-nav-link :href="route('insights.index')" 
        :active="request()->routeIs('insights.*')" 
        :class="request()->routeIs('insights.*') ? 'text-pink-600 border-pink-500' : 'hover:text-pink-500'">
        AI
    </x-nav-link>
</div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">

                <x-dropdown align="center" width="80">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-500 hover:text-pink-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                            </svg>

                            @if($unreadCount > 0)
                                <span class="absolute top-1 right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-[10px] flex items-center justify-center font-bold">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-white rounded-xl overflow-hidden">
                            <div class="px-4 py-3 border-b flex justify-between items-center bg-pink-50">
                                <span class="text-sm font-semibold text-gray-800">🔔 Thông báo</span>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-pink-500 hover:underline">
                                    Xem tất cả
                                </a>
                            </div>

                            <div class="max-h-[400px] overflow-y-auto">
                                @forelse($latestNotifications as $n)
                                    <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-3 border-b hover:bg-gray-50 transition flex gap-3
                                            {{ !$n->is_read ? 'bg-pink-50' : '' }}">

                                            <div class="w-8 h-8 flex-none flex items-center justify-center rounded-full bg-pink-100">
                                                🔔
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start">
                                                    <span class="text-sm font-semibold text-gray-800 truncate pr-2">
                                                        {{ $n->title }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 whitespace-nowrap">
                                                        {{ $n->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                                    {{ $n->message }}
                                                </p>
                                            </div>
                                        </button>
                                    </form>
                                @empty
                                    <div class="p-5 text-center text-gray-400 text-sm">
                                        Không có thông báo
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="32">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-orange-500 transition">
                            {{ Auth::user()->name }}
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="whitespace-nowrap">
                            Hồ sơ
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                class="whitespace-nowrap"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Đăng xuất
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

            </div>
        </div>
    </div>
</nav>