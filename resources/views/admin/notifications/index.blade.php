@extends('layouts.admin')

@section('content')
<style>

.custom-pagination nav {
    display: flex;
    justify-content: center;
}

.custom-pagination .hidden {
    display: none;
}

.custom-pagination svg {
    width: 18px;
    height: 18px;
}

.custom-pagination span,
.custom-pagination a {
    transition: all 0.25s ease;
}

.custom-pagination a:hover {
    transform: translateY(-2px);
}

</style>
<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-8">

            <h2 class="text-3xl font-bold flex items-center gap-3 tracking-tight">
    <span class="text-[#2F3C7E]">📢</span>
    <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-[#2F3C7E]">
        Quản lý thông báo
    </span>
</h2>

            {{-- BUTTON OPEN MODAL --}}
            <button
                type="button"
                onclick="openNotifyModal()"
                class="bg-[#2F3C7E] text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:scale-105 transition-all duration-300"
            >
                + Tạo thông báo mới
            </button>

        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div
                id="success-alert"
                class="mb-6 p-4 border-2 border-[#2D5A27] text-[#2D5A27] rounded-2xl shadow-sm font-bold bg-white transition-all duration-500"
            >
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- TABLE --}}
        <div class="bg-white rounded-[30px] shadow-xl overflow-hidden border-b-8 border-[#787FF6]">

            <table class="w-full text-left">

                <thead class="bg-gray-50 border-b">
                    <tr class="text-[12px] uppercase text-gray-500 font-black text-center">

                        <th class="py-6 px-8 text-left">
                            Tiêu đề & Nội dung
                        </th>

                        <th class="py-6 px-6">
                            Người nhận
                        </th>

                        <th class="py-6 px-6">
                            Ngày gửi
                        </th>

                        <th class="py-6 px-8">
                            Thao tác
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">

                    @foreach($notifications as $noti)

                    <tr class="hover:bg-blue-50/40 transition-all text-center">

                        {{-- TITLE --}}
                        <td class="py-5 px-8 text-left">

                            <div class="font-black text-gray-800 flex items-center gap-2">
                                {{ $noti->title }}
                            </div>

                            <div class="text-xs text-gray-400 mt-1 italic">
                                {{ $noti->message }}
                            </div>

                        </td>

                        {{-- USER --}}
                        <td class="py-5 px-6">

                            @if($noti->user_id)

                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-600 uppercase">
                                    👤 {{ $noti->user->name ?? 'User' }}
                                </span>

                            @else

                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-600 uppercase">
                                    🌍 Hệ thống
                                </span>

                            @endif

                        </td>

                        {{-- DATE --}}
                        <td class="py-5 px-6 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($noti->created_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                        </td>

                        {{-- ACTION --}}
                        <td class="py-5 px-8">

                            <button
                                type="button"
                                onclick="confirmDeleteNotify('{{ $noti->id }}')"
                                class="text-red-500 hover:scale-125 transition-all text-xl"
                            >
                                🗑️
                            </button>

                            <form
                                id="del-noti-{{ $noti->id }}"
                                action="{{ route('admin.notifications.destroy', $noti->id) }}"
                                method="POST"
                                class="hidden"
                            >
                                @csrf
                                @method('DELETE')
                            </form>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>
{{-- PAGINATION --}}
<div class="px-6 py-5 bg-white border-t">

    <div class="flex justify-between items-center flex-wrap gap-4">

        {{-- INFO --}}
        <div class="text-sm text-gray-400 font-semibold">
            Hiển thị
            <span class="text-[#2F3C7E] font-black">
                {{ $notifications->firstItem() }}
            </span>
            -
            <span class="text-[#2F3C7E] font-black">
                {{ $notifications->lastItem() }}
            </span>
            / {{ $notifications->total() }} thông báo
        </div>

        {{-- LINKS --}}
     <div class="custom-pagination">
    {{ $notifications->onEachSide(1)->links('pagination::simple-tailwind') }}
</div>

    </div>

</div>
        </div>
    </div>
</div>

{{-- ================= MODAL ================= --}}
<div
    id="notifyModal"
    class="hidden fixed top-0 left-0 w-screen h-screen z-[99999]"
>

    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/50"
        onclick="closeNotifyModal()"
    ></div>

    {{-- CONTENT --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">

        <div
            class="bg-white w-full max-w-lg rounded-[35px] shadow-2xl p-8 relative"
        >

            {{-- CLOSE --}}
            <button
                onclick="closeNotifyModal()"
                class="absolute top-4 right-5 text-2xl text-gray-400 hover:text-red-500 transition"
            >
                ✕
            </button>

            {{-- TITLE --}}
            <h3 class="text-2xl font-black text-center mb-6 text-[#2F3C7E] font-itim">
                🚀 Soạn thông báo
            </h3>

            {{-- FORM --}}
            <form action="{{ route('admin.notifications.store') }}" method="POST">

                @csrf

                <div class="space-y-4">

                    {{-- TITLE --}}
                    <div>

                        <label class="block mb-2 text-sm font-bold text-gray-500">
                            Tiêu đề
                        </label>

                        <input
                            type="text"
                            name="title"
                            required
                            class="w-full rounded-2xl bg-gray-100 border-0 px-4 py-3 focus:ring-2 focus:ring-blue-400"
                        >

                    </div>

                    {{-- USER --}}
                    <div>

                        <label class="block mb-2 text-sm font-bold text-gray-500">
                            Gửi cho
                        </label>

                        <select
                            name="user_id"
                            class="w-full rounded-2xl bg-gray-100 border-0 px-4 py-3 focus:ring-2 focus:ring-blue-400"
                        >

                            <option value="">
                                🌍 Tất cả người dùng
                            </option>

                            @foreach($users as $user)

                                <option value="{{ $user->id }}">
                                    👤 {{ $user->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- MESSAGE --}}
                    <div>

                        <label class="block mb-2 text-sm font-bold text-gray-500">
                            Nội dung
                        </label>

                        <textarea
                            name="message"
                            rows="4"
                            required
                            class="w-full rounded-2xl bg-gray-100 border-0 px-4 py-3 focus:ring-2 focus:ring-blue-400"
                        ></textarea>

                    </div>

                </div>

                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="w-full mt-6 bg-[#2F3C7E] text-white py-4 rounded-2xl font-bold hover:bg-blue-800 transition"
                >
                    Gửi ngay 🚀
                </button>

            </form>

        </div>
    </div>
</div>

{{-- SWEET ALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    // OPEN MODAL
    function openNotifyModal() {
        document.getElementById('notifyModal').classList.remove('hidden');
    }

    // CLOSE MODAL
    function closeNotifyModal() {
        document.getElementById('notifyModal').classList.add('hidden');
    }

    // AUTO HIDE SUCCESS
    document.addEventListener('DOMContentLoaded', function () {

        const successAlert = document.getElementById('success-alert');

        if(successAlert) {

            setTimeout(() => {

                successAlert.style.opacity = '0';

                setTimeout(() => {
                    successAlert.remove();
                }, 500);

            }, 3000);
        }
    });

    // DELETE
    function confirmDeleteNotify(id) {

        Swal.fire({

            title: '<span class="font-itim text-[#2F3C7E]">Xóa thông báo?</span>',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#787FF6',

            cancelButtonColor: '#2F3C7E',

            confirmButtonText: 'Xác nhận xóa!',

            cancelButtonText: 'Hủy',

            borderRadius: '40px'

        }).then((result) => {

            if(result.isConfirmed) {
                document.getElementById('del-noti-' + id).submit();
            }

        });
    }

</script>

@endsection