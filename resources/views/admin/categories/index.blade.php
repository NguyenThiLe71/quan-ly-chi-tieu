@extends('layouts.admin')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold flex items-center gap-3 tracking-tight">
    <span class="text-[#2F3C7E]">📂</span> 
    <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-[#2F3C7E]">
        Danh mục hệ thống
    </span>
</h2>
            <button onclick="openModal('add')" class="bg-[#2F3C7E] text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:scale-105 transition-all">
                + Thêm danh mục
            </button>
        </div>

        @if(session('success'))
            <div id="success-alert" class="mb-4 p-4 bg-green-500 text-white rounded-xl shadow-lg font-bold transition-all duration-500">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[30px] shadow-xl overflow-hidden border-b-8 border-[#5D4037]">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-[10px] uppercase text-gray-400 font-black">
                        <th class="py-6 px-8">Tên danh mục</th>
                        <th class="py-6 px-6">Loại</th>
                        <th class="py-6 px-6">Ngày tạo</th>
                        <th class="py-6 px-8 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-blue-50/40 transition-all">
                        <td class="py-5 px-8 font-black text-gray-800">{{ $cat->name }}</td>
                       <td class="py-5 px-6">
    @if($cat->type == 'income')
        <span class="text-[12px] font-black uppercase text-[#2D5A27] flex items-center gap-1">
            💰 Income
        </span>
    @else
        <span class="text-[12px] font-black uppercase text-[#E91E63] flex items-center gap-1">
            💸 Expense
        </span>
    @endif
</td>
                       <td class="py-5 px-6 text-sm text-gray-600">
    @if($cat->created_at)
        {{ \Carbon\Carbon::parse($cat->created_at)->format('d/m/Y H:i') }}
    @else
        <span class="text-gray-300 italic">Chưa cập nhật</span>
    @endif
</td>
                        <td class="py-5 px-8 text-right space-x-2">
                            <button onclick="openModal('edit', '{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->type }}')" class="text-blue-500 hover:scale-110 transition-all font-bold">✏️ Sửa</button>
                            
                            <button type="button" onclick="confirmDelete('{{ $cat->id }}')" class="text-red-500 hover:scale-110 transition-all font-bold">🗑️ Xóa</button>
                            
                            <form id="delete-form-{{ $cat->id }}" action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Thêm/Sửa --}}
<div id="catModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] w-full max-w-md p-10 shadow-2xl relative animate-fade">
        <h3 id="modalTitle" class="text-2xl font-black font-itim text-[#2F3C7E] mb-6">Thêm danh mục</h3>
        <form id="catForm" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-2">Tên danh mục</label>
                    <input type="text" name="name" id="catName" required class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#5D4037]">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-2">Loại giao dịch</label>
                    <select name="type" id="catType" class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#5D4037] font-bold">
                        <option value="income">💰 Income (Thu nhập)</option>
                        <option value="expense">💸 Expense (Chi tiêu)</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 font-bold text-gray-400">Hủy</button>
                <button type="submit" class="flex-1 py-3 bg-[#2F3C7E] text-white rounded-2xl font-bold shadow-lg hover:bg-[#5D4037] transition-all">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Hàm hiển thị thông báo xóa kiểu Luxury
    function confirmDelete(id) {
        Swal.fire({
           title: '<span class="text-2xl font-bold text-[#2F3C7E]">Xác nhận xóa?</span>',
            html: '<p class="text-sm text-gray-500 font-bold">Danh mục này sẽ bị xóa vĩnh viễn khỏi hệ thống!</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#922a0a', // Màu Nâu m chọn
            cancelButtonColor: '#1c2b73',  // Màu Xanh Lam
            confirmButtonText: 'Xác nhận xóa!',
            cancelButtonText: 'Hủy bỏ',
            background: '#fff',
            borderRadius: '40px', // Bo góc tròn theo style UI m
            customClass: {
                title: 'font-black',
                popup: 'rounded-[40px]'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    function openModal(mode, id = '', name = '', type = '') {
        const modal = document.getElementById('catModal');
        const form = document.getElementById('catForm');
        const title = document.getElementById('modalTitle');
        const method = document.getElementById('formMethod');
        
        modal.classList.remove('hidden');
        if(mode === 'edit') {
            title.innerText = 'Cập nhật danh mục';
            form.action = `/admin/categories-manager/${id}`;
            method.value = 'PUT';
            document.getElementById('catName').value = name;
            document.getElementById('catType').value = type;
        } else {
            title.innerText = 'Thêm danh mục';
            form.action = "{{ route('admin.categories.store') }}";
            method.value = 'POST';
            form.reset();
        }
    }

    function closeModal() { document.getElementById('catModal').classList.add('hidden'); }
    // Tự động ẩn thông báo success sau 3 giây
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.getElementById('success-alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500); // Đợi hiệu ứng mờ dần rồi xóa hẳn
        }, 3000);
    }
});
</script>
@endsection