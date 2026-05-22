<x-app-layout>

<div class="container py-4">

    {{-- 🔥 TIÊU ĐỀ --}}
    <div class="w-full flex justify-center mb-8 px-4">
    <h3 class="flex items-center gap-3 text-center" 
        style="font-family: 'Itim', cursive; font-size: 2.2rem; display: flex; align-items: center;">
        
        <span class="flex-shrink-0" style="filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.3));">
            📖
        </span>

        <span class="title-gradient" 
              style="font-weight: 900; 
                     letter-spacing: 0.5px;
                    /* Tạo độ dày cho nét chữ Itim để nhìn chuyên nghiệp */
                    text-shadow: 0.5px 0px 0px #e8117d, -0.5px 0px 0px #f756a6;
                     display: flex; align-items: center;">
            Danh sách chi tiêu
        </span>
    </h3>
</div>
    {{-- 🔥 THÔNG BÁO --}}
    @if(session('success'))
        <div id="toast-success" class="alert alert-success shadow-sm border-0 text-center mb-4">
            {{ session('success') }}
        </div>
    @endif

   {{-- 🔥 CỤM ĐIỀU KHIỂN: CHỈ SỬA MÀU KHUNG, GIỮ NGUYÊN LOGIC --}}
<div class="d-flex justify-content-center align-items-center gap-3 mb-4 flex-nowrap" style="width: 100%; max-width: 1200px; margin: 0 auto;">
    
    {{-- Đổi class sang main-control-wrapper để lấy màu tím nhạt --}}
    <div class="main-control-wrapper shadow-none" style="flex: 1; min-width: 0;">
        <div class="p-1">
            {{-- HÀNG 1: TÌM KIẾM --}}
            <div class="d-flex align-items-center gap-2">
               <form method="GET" action="{{ route('transactions.index') }}" class="d-flex gap-2 flex-grow-1 align-items-center">
    {{-- Ô tìm kiếm giữ nguyên --}}
    <div class="inner-white-box" style="flex: 2;">
        <input type="text" name="search" value="{{ request('search') }}" 
               class="form-control border-0 bg-transparent" placeholder="Tìm kiếm..." style="height: 45px;">
    </div>

    {{-- Ô CHỌN THÁNG: Thay $filterDate thành request('filter_date') --}}
    <div class="inner-white-box" style="flex: 1; min-width: 160px;">
        <input type="month" name="filter_date" value="{{ request('filter_date') }}" 
               class="form-control border-0 bg-transparent" style="height: 45px;">
    </div>
    
    <button type="submit" class="btn btn-search-flat">🔍</button>

    {{-- NÚT XÓA LỌC (Hiện tất cả) --}}
    @if(request('search') || request('filter_date'))
        <a href="{{ route('transactions.index') }}" 
           class="btn btn-light d-flex align-items-center justify-content-center border" 
           style="width: 45px; height: 45px; border-radius: 12px; background: #ffffff; flex-shrink: 0; text-decoration: none; color: #64748b;"
           title="Xóa lọc, hiện tất cả">
            ✕
        </a>
    @endif
</form>

               <button class="btn btn-add-flat"
        type="button"
        id="toggleFormBtn">
    <span class="fs-4">+</span>
</button>
            </div>

            {{-- HÀNG 2: FORM THÊM MỚI --}}
           <div id="collapseTranForm"
     @if($errors->any())
         style="display:block;"
     @else
         style="display:none;"
     @endif>
                <div class="pt-3 mt-3 border-top border-danger border-opacity-10">
                    <form method="POST" action="{{ route('transactions.store') }}" class="d-flex align-items-center gap-2 flex-nowrap">
                        @csrf
                        <div class="inner-white-box" style="flex: 1.2;">
                            <input type="text" name="amount" oninput="formatMoney(this)" class="form-control border-0 bg-transparent fw-bold text-primary" placeholder="Số tiền..." required>
                        </div>

                        <div class="inner-white-box" style="flex: 1.5;">
                            <select name="category_id" class="form-select border-0 bg-transparent text-dark">
                                <option value="" disabled selected>📁 Danh mục...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="inner-white-box" style="flex: 1;">
                            <select name="type" class="form-select border-0 bg-transparent">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>

                        <div class="inner-white-box" style="flex: 2;">
                            <input type="date" name="transaction_date" class="form-control border-0 bg-transparent" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="inner-white-box" style="flex: 2;">
                            <input type="text" name="description" class="form-control border-0 bg-transparent" placeholder="Ghi chú...">
                        </div>

                        {{-- 🔥 BỔ SUNG: Nút gạt chọn đặt làm lịch cố định hằng tháng --}}
                        <div class="d-flex align-items-center justify-content-center px-2" style="flex: 0.8; min-width: 90px;" title="Tự động nhắc lại vào ngày này tháng sau">
                            <label class="soft-switch" style="font-family: 'Itim', cursive;">
                                <input type="checkbox" name="is_recurring" value="1">
                                <span class="slider round"></span>
                                <span class="switch-label">Cố định</span>
                            </label>
                        </div>

                        <div style="flex: 0.8; min-width: 80px;">
                            <button class="btn btn-pink-blue fw-bold w-100" style="height: 45px; border-radius: 12px;">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- NÚT NHẬT KÝ --}}
    <a href="{{ route('transactions.history') }}" class="btn-diary-gold d-flex align-items-center gap-2 fw-bold shadow-sm" style="height: 71px; padding: 0 25px; white-space: nowrap;">
        <span style="font-size: 20px;">📜</span> Nhật ký giao dịch
    </a>
</div>
    {{-- 🔥 DANH SÁCH GIAO DỊCH --}}
    <div class="timeline-container">
        @php $currentDate = null; $currentMonth = null; @endphp

        @foreach($transactions as $tran)
    @php 
        $month = \Carbon\Carbon::parse($tran->transaction_date)->format('m/Y'); 
    @endphp

    @if($currentMonth != $month)
        @php $currentMonth = $month; @endphp
        <div class="month-divider mt-5 mb-2 text-center">
            <span class="fw-bold px-4 py-1 rounded-pill" style="background: #fff1f2; color: #db2777; border: 1px solid #fecaca;">
                📆 Tháng {{ $month }}
            </span>
        </div>
    @endif

    @if($currentDate != $tran->transaction_date)
        @php $currentDate = $tran->transaction_date; @endphp
        <div class="date-divider mt-4 mb-3">
            <span class="badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 border-pink-gold">
                📅 {{ \Carbon\Carbon::parse($tran->transaction_date)->format('d/m/Y') }}
            </span>
        </div>
    @endif

    {{-- THẺ GIAO DỊCH --}}
    <div class="transaction-card shadow-sm mb-3 {{ $tran->type == 'income' ? 'border-income' : 'border-expense' }}">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="tran-icon {{ $tran->type == 'income' ? 'bg-success-light' : 'bg-danger-light' }}">
                    @php
    $name = mb_strtolower($tran->category->name ?? '', 'UTF-8');
    
    // Icon mặc định dựa trên loại giao dịch
    $icon = ($tran->type == 'income') ? '💰' : '💸';

    // Logic đồng bộ hóa toàn bộ icon danh mục
    if (str_contains($name, 'ăn') || str_contains($name, 'uống')) {
        $icon = '🍔';
    } elseif (str_contains($name, 'lương')) {
        $icon = '💵';
    } elseif (str_contains($name, 'thưởng')) {
        $icon = '🧧'; // Bao lì xì may mắn cho Thưởng
    } elseif (str_contains($name, 'xe') || str_contains($name, 'di chuyển')) {
        $icon = '🚗';
    } elseif (str_contains($name, 'nhà') || str_contains($name, 'trọ')) {
        $icon = '🏠';
    // Cập nhật dòng Mỹ phẩm/Làm đẹp trong khối @php của ông:
} elseif (str_contains($name, 'mỹ phẩm') || 
          str_contains($name, 'làm đẹp') || 
          str_contains($name, 'mặt nạ') ||   
          str_contains($name, 'dụng cụ') || str_contains($name, 'đẹp') || 
          str_contains($name, 'skincare')) {
    $icon = '💄';
    } elseif (str_contains($name, 'cá nhân') || str_contains($name, 'đồ dùng')) {
        $icon = '🪥';
    } elseif (str_contains($name, 'mua sắm')) {
        $icon = '🛍️';
    } elseif (str_contains($name, 'thuốc') || str_contains($name, 'bệnh') || str_contains($name, 'y tế')) {
        $icon = '💊';
    } elseif (str_contains($name, 'giải trí') || str_contains($name, 'game') || str_contains($name, 'vui chơi')) {
        $icon = '🎮';
    } elseif (str_contains($name, 'phát sinh') || str_contains($name, 'khẩn cấp')) {
        $icon = '⚡';
    }
@endphp
                    {{ $icon }}
                </div>
                
                <div class="tran-info text-start">
                    <h6 class="mb-0 fw-bold text-dark">{{ $tran->category->name ?? 'Chưa phân loại' }}</h6>
                    <small class="text-muted">{{ $tran->description ?: 'Không có mô tả' }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="tran-amount text-end">
                    <span class="fw-bold fs-5 {{ $tran->type == 'income' ? 'text-success' : 'text-danger' }}">
                        {{ $tran->type == 'income' ? '+' : '-' }}{{ number_format($tran->amount, 0, ',', '.') }}đ
                    </span>
                </div>

                <div class="tran-actions d-flex gap-2">
                    {{-- Nút Sửa --}}
                    <button class="btn-action pencil-modern" data-bs-toggle="modal" data-bs-target="#editModal{{ $tran->id }}">✏️</button>
                    {{-- Nút Xóa --}}
                    <button class="btn-action trash-modern" onclick="setDeleteId('{{ $tran->id }}')">🗑</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 QUAN TRỌNG: MODAL SỬA CHO TỪNG GIAO DỊCH (Dán ngay trong loop) --}}
    <div class="modal fade" id="editModal{{ $tran->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 25px; border: none;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold title-gradient" style="font-family: 'Itim', cursive;">Sửa giao dịch ✏️</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('transactions.update', $tran->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Số tiền</label>
                            <input type="text" name="amount" value="{{ number_format($tran->amount, 0, ',', '.') }}" 
                                   oninput="formatMoney(this)" class="form-control input-pink-style fw-bold text-primary">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Danh mục</label>
                            <select name="category_id" class="form-select input-pink-style">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $tran->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Modal Sửa - Đảm bảo name="type" nằm trong thẻ select --}}
<div class="mb-3">
    <label class="small fw-bold text-muted">Loại giao dịch</label>
    <select name="type" class="form-select input-pink-style" required>
        <option value="expense" {{ $tran->type == 'expense' ? 'selected' : '' }}>Expense</option>
        <option value="income" {{ $tran->type == 'income' ? 'selected' : '' }}>Income</option>
    </select>
</div>
                        <div class="mb-3">
    <label class="small fw-bold text-muted">Ngày giao dịch</label>
    <input type="date" name="transaction_date" value="{{ \Carbon\Carbon::parse($tran->transaction_date)->format('Y-m-d') }}" 
           class="form-control input-pink-style">
</div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Ghi chú</label>
                            <input type="text" name="description" value="{{ $tran->description }}" class="form-control input-pink-style">
                        </div>
                    </div>
                    {{-- CỤM NÚT: HỦY TRẮNG & CẬP NHẬT HỒNG TÍM --}}
<div class="modal-footer border-0 pb-4 justify-content-center gap-3">
    <button type="button" class="btn btn-white-soft" data-bs-dismiss="modal">
        Hủy
    </button>
    
    <button type="submit" class="btn btn-pink-purple-gradient">
        Cập nhật
    </button>
</div>
                </form>
            </div>
        </div>
    </div>
@endforeach
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-delete-modal">
            <div class="modal-body text-center p-5">
                <div class="warning-icon">⚠️</div>
                <h5 class="fw-bold mt-3">Xóa giao dịch?</h5>
                <p class="text-secondary mb-4">Hành động này không thể hoàn tác</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Hủy</button>
                    <button id="confirmDeleteBtn" class="btn btn-danger px-4">Xác nhận xóa</button>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="deleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<style>
/* 🔥 STYLE HỒNG VÀNG SANG CHẢNH */
.title-gradient {
    /* Hồng đậm ở đầu chuyển sang hồng phấn ở đuôi */
    background: linear-gradient(135deg, #ec4899 0%, #fbcfe8 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
    
    /* Hiệu ứng hào quang hồng nhẹ */
    filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.15));
}

.btn-diary-gold { 
    color: #640747 !important; 
    background: #ffffff; 
    border: 2px solid #ddd6fe; 
    padding: 10px 24px; 
    font-size: 17px; 
    border-radius: 12px; 
    text-decoration: none; 
    transition: 0.3s;
}
.btn-diary-gold:hover { background: linear-gradient(45deg, #f43f5e, #fb8c00); color: #ffffff !important; transform: translateY(-3px); }

.input-pink-style {
    border: 1px solid #f9a8d4 !important;
    border-radius: 12px;
    background: white;
    padding: 10px 15px;
}

.btn-gradient { background: linear-gradient(45deg, #d946ef, #ec4899); color: white !important; border: none; border-radius: 12px; transition: 0.3s; }

.transaction-card {
    background: white;
    border-radius: 20px;
    padding: 15px 20px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(251, 207, 232, 0.3) !important;
}
.transaction-card:hover {
    transform: translateX(8px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.2) !important;
}

.border-expense { border-color: #fecaca !important; } 
.border-income { border-color: #bbf7d0 !important; }
.border-pink-gold { border: 1px solid #fde68a !important; }

.tran-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.bg-danger-light { background-color: #fff1f2; }
.bg-success-light { background-color: #f0fdf4; }

.btn-action { border: none; width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
.pencil-modern { background: #fae0ce; color: #d97706; }
.trash-modern { background: #fae2e4; color: #f43f5e; }
/* Nút Hủy trắng mềm mại */
.btn-white-soft {
    background-color: #ffffff !important;
    color: #333333 !important;
    border: none !important;
    padding: 10px 35px !important;
    border-radius: 16px !important; /* Bo tròn sâu như hình */
    font-weight: 500 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s ease;
}

.btn-white-soft:hover {
    background-color: #f8f9fa !important;
    transform: translateY(-2px);
}

/* Nút Cập nhật Gradient Hồng - Tím */
.btn-pink-purple-gradient {
    background: linear-gradient(135deg, #d946ef 0%, #ec4899 100%) !important;
    color: white !important;
    border: none !important;
    padding: 10px 30px !important;
    border-radius: 16px !important; /* Bo tròn sâu đúng hình Quân gửi */
    font-weight: bold !important;
    box-shadow: 0 6px 15px rgba(236, 72, 153, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.btn-pink-purple-gradient:hover {
    filter: brightness(1.1);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(217, 70, 239, 0.4) !important;
}

.btn-pink-purple-gradient:active {
    transform: scale(0.95);
}
.custom-delete-modal { border-radius: 25px; border: none; background: #fffafa; animation: zoomIn 0.3s ease; }
    .warning-icon { font-size: 55px; }
    .btn-danger { background: linear-gradient(45deg, #fb7185, #f43f5e); border: none; border-radius: 12px; font-weight: bold; }
    /* Nút Xác nhận xóa chuẩn ảnh Quân gửi */
.btn-danger-confirm {
    background: #e63946 !important; /* Đỏ Rose chuẩn */
    color: white !important;
    border: none !important;
    padding: 10px 30px !important;
    border-radius: 12px !important;
    font-weight: bold !important;
    transition: 0.3s;
}

.btn-danger-confirm:hover {
    background: #d62839 !important;
    transform: scale(1.05);
}

/* Nút Hủy trong Modal Xóa */
.btn-light-cancel {
    background: #f8faff !important;
    border: none !important;
    padding: 10px 30px !important;
    border-radius: 12px !important;
    color: #333 !important;
}
.btn-pink-blue {
    background: linear-gradient(45deg, #ed2e8e 0%, #80e4f1 100%) !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2); /* Đổ bóng ánh xanh dương nhẹ */
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

}
.btn-pink-blue:hover {
    background: linear-gradient(45deg, #f472b6 10%, #60a5fa 90%) !important;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 20px rgba(236, 72, 153, 0.3);
}
.btn-pink-blue:active {
    transform: scale(0.95);
}
.main-control-wrapper {
    background: linear-gradient(135deg, #fdf2f8, #f5f3ff) !important;
    border-radius: 25px;
    padding: 15px;
    box-shadow: none !important;
    border: none !important;
}

/* Ô nhập liệu nền trắng bên trong */
.inner-white-box {
    background: #ffffff !important;
    border: 1px solid #f9a8d4 !important;
    border-radius: 12px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    box-shadow: none !important;
}

/* Đồng bộ nút Tìm kiếm và nút Cộng */
.btn-search-flat, .btn-add-flat {
    background: linear-gradient(45deg, #ec4899, #d946ef) !important;
    color: white !important;
    border: none;
    width: 50px;
    height: 45px;
    border-radius: 12px;
    box-shadow: none !important;
    flex-shrink: 0;
}

/* 🔥 BỔ SUNG CSS: STYLE SWITCH NÚT GẠT CỐ ĐỊNH KUTE */
.soft-switch {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    user-select: none;
}
.soft-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.soft-switch .slider {
    position: relative;
    width: 44px;
    height: 22px;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 34px;
}
.soft-switch .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.soft-switch input:checked + .slider {
    background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%);
}
.soft-switch input:checked + .slider:before {
    transform: translateX(22px);
}
.soft-switch .switch-label {
    font-size: 11px;
    font-weight: bold;
    color: #64748b;
    white-space: nowrap;
}
.soft-switch input:checked ~ .switch-label {
    color: #db2777;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. Hàm format tiền khi gõ (Giữ nguyên của Quân)
function formatMoney(input) {
    let value = input.value.replace(/\D/g, '');
    if(value !== "") value = new Intl.NumberFormat('vi-VN').format(value);
    input.value = value;
}

// 2. Hàm xử lý xóa (Giữ nguyên của Quân)
let deleteId = null;
function setDeleteId(id) {
    deleteId = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    const form = document.getElementById('deleteForm');
    const modalEl = document.getElementById('deleteModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
    form.action = "{{ url('transactions') }}/" + deleteId;
    form.submit();
});

// 3. Logic chính khi trang load xong
document.addEventListener('DOMContentLoaded', () => {
    const userBalance = Number("{{ $userBalance ?? 0 }}");
    const toggleBtn = document.getElementById('toggleFormBtn');
    const formBox = document.getElementById('collapseTranForm');

    // --- CHỈ HIỆN THÔNG BÁO NHẮC NHỞ MỘT LẦN KHI VÀO TRANG ---
    if (userBalance <= 0) {
        Swal.fire({
            title: '<span style="font-family: \'Itim\'; color: #db2777;">Nhắc nhở ✨</span>',
            html: '<p style="font-family: \'Itim\';">Chào bạn, bạn nên <b>nhập khoản Lương</b> trước để bắt đầu quản lý chi tiêu hiệu quả nhé!</p>',
            icon: 'info',
            confirmButtonText: 'Đồng ý',
            confirmButtonColor: '#ec4899',
            background: '#fffaff',
            borderRadius: '25px'
        });
    }

    // --- MỞ/ĐÓNG FORM BÌNH THƯỜNG (KHÔNG CHẶN NỮA) ---
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (formBox) {
                // Ấn nút (+) là mở hoặc đóng form luôn, không kiểm tra số dư nữa
                formBox.style.display = (formBox.style.display === 'none' || formBox.style.display === '') ? 'block' : 'none';
            }
        });
    }

    // --- TỰ ĐỘNG ẨN ALERT (Giữ nguyên của Quân) ---
    const alerts = document.querySelectorAll('.alert, #toast-success, #toast-error');
    alerts.forEach(el => {
        setTimeout(() => {
            el.style.transition = "opacity 1s ease, transform 0.5s ease";
            el.style.opacity = "0";
            el.style.transform = "translateY(-10px)";
            setTimeout(() => el.remove(), 1000); 
        }, 3000);
    });
});
</script>

</x-app-layout>