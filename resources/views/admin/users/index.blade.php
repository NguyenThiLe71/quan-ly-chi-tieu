@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        
        {{-- Tiêu đề & Tìm kiếm --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h2 class="text-3xl font-bold flex items-center gap-3 tracking-tight">
                <span class="text-[#2F3C7E]">👥</span> 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-[#2F3C7E]">
                    Quản lý người dùng
                </span>
            </h2>

            <form action="{{ route('admin.users.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm tên hoặc email..." 
                    class="pl-12 pr-6 py-3 bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-400 w-80 transition-all">
                <span class="absolute left-4 top-3.5">🔎</span>
            </form>
        </div>

        {{-- Thông báo Alert --}}
        @if(session('success'))
            <div id="success-alert" class="mb-4 p-4 bg-green-500 text-white rounded-xl shadow-lg font-bold transition-all duration-500">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div id="error-alert" class="mb-4 p-4 bg-red-500 text-white rounded-xl shadow-lg font-bold transition-all duration-500">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Bảng danh sách --}}
        <div class="bg-white rounded-[30px] shadow-xl overflow-hidden border-b-8 border-blue-400">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-[10px] uppercase text-gray-400 tracking-widest font-black">
                        <th class="py-6 px-8">Thông tin User</th>
                        <th class="py-6 px-6">Vai trò</th>
                        <th class="py-6 px-6 text-center">Trạng thái</th>
                        <th class="py-6 px-8 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-blue-50/40 transition-all">
                        <td class="py-5 px-8">
                            <div class="font-black text-gray-800 flex items-center gap-2">
                                {{ $user->name }}
                                @if(Auth::id() == $user->id)
                                    <span class="text-[9px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-md uppercase">Bạn</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">ID: #{{ $user->id }} | {{ $user->email }}</div>
                        </td>
                        <td class="py-5 px-6">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-5 px-6 text-center">
                            @if($user->status == 1)
                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-[10px] font-black uppercase border border-green-200">Hoạt động</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-[10px] font-black uppercase border border-red-200">Đã khóa</span>
                            @endif
                        </td>
                        <td class="py-5 px-8 text-right space-x-1">
                            @if(Auth::id() !== $user->id)
                                {{-- Nút Khóa --}}
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 {{ $user->status == 1 ? 'bg-orange-400' : 'bg-green-500' }} text-white text-[10px] font-black rounded-xl shadow-md uppercase transition-transform hover:scale-105">
                                        {{ $user->status == 1 ? '🔒 Khóa' : '🔓 Mở' }}
                                    </button>
                                </form>

                                {{-- Nút Xóa Luxury --}}
                                <button type="button" onclick="confirmDeleteUser('{{ $user->id }}')" class="px-4 py-2 bg-red-600 text-white text-[10px] font-black rounded-xl shadow-md uppercase transition-transform hover:scale-105">
                                    🗑️ Xóa
                                </button>
                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            @else
                                <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 text-[10px] font-black rounded-xl shadow-none uppercase cursor-not-allowed">🔒 Khóa</button>
                                <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 text-[10px] font-black rounded-xl shadow-none uppercase cursor-not-allowed">🗑️ Xóa</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    // Hàm hiển thị thông báo xóa kiểu Luxury chuẩn ảnh mẫu
    function confirmDeleteUser(id) {
    Swal.fire({
        title: '<span class="text-[#2F3C7E] font-bold">Xác nhận xóa?</span>',
        html: '<p class="text-sm text-gray-500 font-bold">Người dùng này sẽ bị xóa vĩnh viễn khỏi hệ thống!</p>',
        icon: 'warning',
        iconColor: '#f8bb86', // Màu cam nhạt chuẩn icon
        showCancelButton: true,
        confirmButtonColor: '#922a0a', // Màu đỏ gạch chuẩn ảnh m thích
        cancelButtonColor: '#1c2b73',  // Màu xanh Navy chuẩn menu
        confirmButtonText: 'Xác nhận xóa!',
        cancelButtonText: 'Hủy bỏ',
        background: '#ffffff',
        borderRadius: '40px', // Giảm xuống 40px cho cân đối như trang Danh mục
        width: 'auto', // Để nó tự co giãn theo nội dung, không bị thô
        padding: '2.5rem',
        customClass: {
            popup: 'rounded-[40px] shadow-2xl border-none',
            title: 'text-2xl pt-4', // Chỉnh size tiêu đề vừa phải
            confirmButton: 'rounded-xl font-bold px-6 py-3 mx-2 text-sm',
            cancelButton: 'rounded-xl font-bold px-6 py-3 mx-2 text-sm'
        },
        buttonsStyling: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}

    // Tự động ẩn Alert
    document.addEventListener('DOMContentLoaded', function() {
        ['success-alert', 'error-alert'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-20px)';
                    setTimeout(() => el.remove(), 500);
                }, 3000);
            }
        });
    });
</script>
@endsection