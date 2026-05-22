<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'sans-serif'; 
            background-color: #f8fbff; 
            overflow-x: hidden; 
            overflow-y: auto;
        }
        .font-itim { font-family: 'Itim', cursive; }
        
        /* Gradient chuẩn của Quân */
        .bg-luxury { background: linear-gradient(135deg, #7BD5F5 0%, #787FF6 100%); }
        
        /* Hiệu ứng Sidebar Active mượt mà */
        .sidebar-active { 
            background: linear-gradient(135deg, #73e6f5, #792fe9); 
            color: white !important; 
            box-shadow: 0 15px 25px -5px rgba(120, 127, 246, 0.4);
            transform: translateX(10px);
        }

        .text-gradient { 
            background: linear-gradient(90deg, #7BD5F5, #787FF6); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
        }

        /* Hiệu ứng Hover cho các nút */
        .nav-item { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-item:hover:not(.sidebar-active) {
            background-color: #f0f7ff;
            transform: translateX(5px);
            color: #787FF6;
        }

        /* Bo góc đặc biệt và bóng đổ Luxury */
        .luxury-card {
            border-radius: 45px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }

        /* Tùy chỉnh thanh cuộn cho Sidebar */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Hiệu ứng hiện trang (Fade In) */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade { animation: fadeIn 0.6s ease-out forwards; }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-80 h-screen bg-white shadow-[20px_0_60px_-15px_rgba(0,0,0,0.03)] rounded-r-[60px] flex flex-col p-10 z-20 relative overflow-y-auto scrollbar-hide">
            <div class="mb-14 text-center group cursor-pointer">
                <h1 class="text-4xl font-black font-itim text-gradient group-hover:scale-105 transition-transform">Expense Management</h1>
                <div class="h-1.5 w-16 bg-luxury mx-auto mt-3 rounded-full shadow-sm"></div>
            </div>
            
            <nav class="space-y-4 py-4">
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center space-x-4 p-5 rounded-[28px] {{ Request::is('admin') ? 'sidebar-active' : 'text-gray-400' }}">
                    <span class="text-2xl">📊</span> <span class="font-bold tracking-tight">Tổng quan</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" 
                    class="nav-item flex items-center space-x-4 p-5 rounded-[28px] {{ request()->routeIs('admin.users.*') ? 'sidebar-active' : 'text-gray-400' }}">
                    <span class="text-2xl">👥</span> <span class="font-bold tracking-tight">Người dùng</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" 
                    class="nav-item flex items-center space-x-4 p-5 rounded-[28px] {{ request()->routeIs('admin.categories.*') ? 'sidebar-active' : 'text-gray-400' }}">
                    <span class="text-2xl">📂</span> <span class="font-bold tracking-tight">Danh mục hệ thống</span>
                </a>

                <a href="{{ route('admin.logs.index') }}" 
                    class="nav-item flex items-center space-x-4 p-5 rounded-[28px] {{ Request::is('admin/logs') ? 'sidebar-active' : 'text-gray-400' }}">
                    <span class="text-2xl">📜</span> <span class="font-bold tracking-tight">Nhật ký hệ thống</span>
                </a>

                <a href="{{ route('admin.spam.index') }}"
                    class="nav-item flex items-center space-x-4 p-5 rounded-[28px] {{ Request::is('admin/spam*') ? 'sidebar-active' : 'text-gray-400' }}">
                    <span class="text-2xl">🚨</span> <span class="font-bold tracking-tight">Bảo mật hệ thống</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                    class="nav-item flex items-center space-x-4 p-5 rounded-[28px] {{ request()->routeIs('admin.notifications.*') ? 'sidebar-active' : 'text-gray-400' }}">
                    <span class="text-2xl">📢</span> <span class="font-bold tracking-tight">Thông báo hệ thống</span>
                </a>
            </nav>

            <div class="pt-8 border-t border-blue-50 space-y-3 mt-auto">
                <a href="{{ route('dashboard') }}" class="nav-item flex items-center space-x-4 p-4 rounded-[25px] text-gray-400 group">
                    <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-luxury group-hover:text-white transition-all">
                        <span class="text-lg">👤</span>
                    </div>
                    <span class="font-bold text-sm">Trang cá nhân</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full nav-item flex items-center space-x-4 p-4 rounded-[25px] text-red-400 hover:bg-red-50 group">
                        <div class="w-10 h-10 bg-red-50 rounded-2xl flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
                            <span class="text-lg">🚪</span>
                        </div>
                        <span class="font-bold text-sm">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-12 bg-[#f8fbff] relative">
            <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-blue-100 rounded-full blur-[120px] opacity-40"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-80 h-80 bg-purple-100 rounded-full blur-[100px] opacity-30"></div>
            
            <div class="animate-fade relative z-10">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>