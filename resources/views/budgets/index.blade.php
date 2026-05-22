<x-app-layout>
<div class="container py-4">

    {{-- 🔥 TIÊU ĐỀ ĐỒNG BỘ --}}
    <div class="w-full flex justify-center mb-6 px-4">
        <h3 class="flex items-center gap-3 text-center" 
            style="font-family: 'Itim', cursive; font-size: 2.2rem;">
            <span class="flex-shrink-0" style="filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.3));" aria-hidden="true">
                💸
            </span>
            <span class="title-gradient" 
                  style="font-weight: 900; 
                         letter-spacing: 0.5px;
                         text-shadow: 0.5px 0px 0px #e8117d, -0.5px 0px 0px #f756a6;">
                Sổ thu chi của bạn
            </span>
        </h3>
    </div>

    {{-- 🔥 THÔNG BÁO --}}
    @if(session('success'))
        <div id="toast-success" class="alert alert-success shadow-sm border-0 text-center mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div id="toast-error" class="alert alert-danger shadow-sm border-0 text-center mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- 🔥 BỘ ĐIỀU KHIỂN --}}
    <div class="d-flex justify-content-center mb-4">
        <div class="main-control-wrapper">
            <div class="d-flex align-items-center gap-2">
                
                {{-- 📅 FORM TÌM KIẾM --}}
                <form method="GET" action="{{ route('budgets.index') }}" class="d-flex align-items-center gap-2" style="flex: 1;">
                    <input type="hidden" name="search_action" value="1">

                    <div class="inner-white-box" style="flex: 1;">
                        <input type="month" name="month_year" 
                               id="search_month_year"
                               aria-label="Chọn tháng năm tìm kiếm ngân sách"
                               value="{{ request('month_year', date('Y-m')) }}" 
                               class="form-control border-0 bg-transparent" 
                               style="height: 45px; color: #5c0404;">
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-search-flat" aria-label="Tìm kiếm">
                            <span aria-hidden="true">🔍</span>
                        </button>

                        @if(request('search_action'))
                            <a href="{{ route('budgets.index') }}" class="btn btn-close-search" aria-label="Xóa bộ lọc tìm kiếm">
                                <span aria-hidden="true">✕</span>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- ➕ NÚT HIỆN FORM THÊM --}}
                <button class="btn btn-add-flat" 
                        type="button"
                        id="toggleBudgetFormBtn"
                        aria-label="Thêm mới ngân sách"
                        aria-expanded="false">
                    <span class="fs-4" aria-hidden="true">+</span>
                </button>
            </div>

            {{-- FORM THÊM MỚI (ẨN MẶC ĐỊNH) --}}
            <div id="collapseAddForm"
                 @if($errors->any()) style="display:block;" @else style="display:none;" @endif>
                <div class="pt-3 mt-2 border-top border-danger border-opacity-10">
                    <form method="POST" action="{{ route('budgets.store') }}">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-3">
                                <select name="category_id" class="form-select input-soft" aria-label="Chọn danh mục ngân sách" required>
                                    <option value="" disabled selected>📁 Chọn danh mục...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <input type="text" name="amount_limit" class="form-control input-soft fw-bold text-primary"
                                       id="amount_limit_input" aria-label="Nhập số tiền hạn mức"
                                       placeholder="💰 Hạn mức tiền..." oninput="formatMoney(this)" required>
                            </div>
                            <div class="col-9 col-md-4">
                                <input type="month" name="month_year" value="{{ date('Y-m') }}" 
                                       aria-label="Chọn tháng năm áp dụng" class="form-control input-soft" required>
                            </div>
                            <div class="col-3 col-md-2">
                                <button type="submit" class="btn btn-pink-blue w-100 fw-bold" style="height: 45px; border-radius: 12px;">Lưu</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 DANH SÁCH NGÂN SÁCH --}}
    <div class="row g-4">
        @foreach($budgets as $b)
        <div class="col-md-4">
            <div class="budget-card shadow-sm h-100 border-pastel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.35rem; font-family: 'Itim', cursive;">{{ $b->category->name }}</h4>
                    <span class="badge rounded-pill bg-light text-muted border px-3 py-2">
                        <span aria-hidden="true">📅</span> {{ $b->month }}/{{ $b->year }}
                    </span>
                </div>

                @php
                    // 🌟 Toàn bộ logic SQL đã chuyển vào Controller. Biến $b->spent_amount giờ đã được nạp sẵn từ bên ngoài!
                    $spent = $b->spent_amount; 
                    $remaining = $b->amount_limit - $spent;
                    $percent = $b->amount_limit > 0 ? ($spent / $b->amount_limit) * 100 : 0;
                    $barColor = $percent > 100 ? 'bg-danger' : ($percent > 75 ? 'bg-warning' : 'bg-success');
                    $width = min($percent, 100);
                @endphp

                <div class="budget-info-box mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Hạn mức:</span>
                        <span class="fw-bold text-primary">{{ number_format($b->amount_limit,0,',','.') }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Đã chi:</span>
                        <span class="fw-bold text-danger">{{ number_format($spent,0,',','.') }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="text-muted small">Còn lại:</span>
                        <span class="fw-bold {{ $remaining < 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($remaining,0,',','.') }}đ
                        </span>
                    </div>
                </div>

                <div class="progress mb-3" style="height:12px; border-radius: 10px; background: #f1f5f9;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $barColor }}"
                         role="progressbar"
                         style="width: {{ $width }}%"
                         aria-valuenow="{{ round($percent) }}"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         aria-label="Tiến độ sử dụng ngân sách">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button class="btn-action pencil-modern" data-bs-toggle="modal" data-bs-target="#edit{{ $b->id }}" aria-label="Sửa hạn mức">
                        <span aria-hidden="true">✏️</span>
                    </button>
                    <button class="btn-action trash-modern" onclick="setDeleteId('{{ $b->id }}')" aria-label="Xóa ngân sách">
                        <span aria-hidden="true">🗑</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL SỬA --}}
        <div class="modal fade" id="edit{{ $b->id }}" tabindex="-1" aria-labelledby="editLabel{{ $b->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-custom-pink">
                    <form method="POST" action="{{ route('budgets.update', $b->id) }}">
                        @csrf @method('PUT')
                        <div class="modal-header border-0">
                            <h5 class="fw-bold" id="editLabel{{ $b->id }}">Sửa hạn mức</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label for="amount_limit_edit_{{ $b->id }}" class="small fw-bold mb-1">Số tiền ngân sách</label>
                                <input type="text" name="amount_limit" id="amount_limit_edit_{{ $b->id }}" value="{{ number_format($b->amount_limit, 0, ',', '.') }}" 
                                       oninput="formatMoney(this)" class="form-control input-soft fw-bold text-primary">
                            </div>
                            <p class="text-muted small">* Giữ nguyên danh mục và thời gian của ngân sách này.</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-gradient px-4 fw-bold">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 🔥 PHẦN PHÂN TRANG --}}
    <nav class="d-flex justify-content-center mt-5 custom-pagination" aria-label="Điều hướng phân trang ngân sách">
        {{ $budgets->links() }}
    </nav>
</div>

{{-- MODAL XÓA --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom-pink">
            <div class="modal-body text-center p-5">
                <div class="warning-icon" aria-hidden="true">⚠️</div>
                <h5 class="fw-bold mt-3" id="deleteModalLabel">Xóa ngân sách này?</h5>
                <p class="text-secondary mb-4">Dữ liệu ngân sách cho tháng này sẽ bị xóa bỏ.</p>
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
/* GIỮ NGUYÊN HOÀN TOÀN TẤT CẢ CSS STYLE LUXURY SOFT UI CỦA QUÂN */
.title-gradient {
    background: linear-gradient(135deg, #ec4899 0%, #fbcfe8 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
    display: inline-flex;
    align-items: center;
    filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.15));
}
.btn-gradient { background: linear-gradient(45deg, #d946ef, #ec4899); color: white !important; border: none; border-radius: 12px; transition: 0.3s; }
.btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(236, 72, 153, 0.3); }

.input-soft {
    border: 1px solid #f9a8d4;
    border-radius: 12px;
    background: white;
    padding: 10px 15px;
    height: 45px;
}

.budget-card h4.fw-bold {
    color: #5c0404 !important; 
    font-weight: 900 !important;
    font-family: 'Itim', cursive;
    text-shadow: 1px 1px 0px #fce7f3, 2px 2px 0px rgba(237, 46, 142, 0.3);
    background: none !important;
    -webkit-text-fill-color: initial !important;
    background-clip: unset !important;
    margin-bottom: 0;
}

.budget-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid #f9a0cb !important; 
    box-shadow: 0 4px 20px rgba(245, 158, 11, 0.15) !important;
}

.budget-card:hover {
    transform: translateY(-5px);
    border-color: #f472b6 !important;
    box-shadow: 0 8px 25px rgba(236, 72, 153, 0.2) !important;
}

.budget-card .badge {
    background-color: #fff1f2 !important;
    color: #f43f5e !important;
    border: 1px solid #ffe4e6 !important;
    font-weight: 600;
}

.btn-action {
    border: none; width: 38px; height: 38px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; transition: 0.3s;
}
.pencil-modern { background: #fae0ce; color: #d97706; }
.trash-modern { background: #fae2e4; color: #f43f5e; }

.modal-custom-pink {
    background: #fffafa !important;
    border-radius: 25px !important;
    border: none !important;
    box-shadow: 0 10px 30px rgba(255, 192, 203, 0.25);
}
.warning-icon { font-size: 50px; }

.btn-pink-blue {
    background: linear-gradient(45deg, #ed2e8e 0%, #80e4f1 100%) !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(128, 228, 241, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-pink-blue:hover {
    background: linear-gradient(45deg, #f472b6 10%, #60a5fa 90%) !important;
    transform: translateY(-3px) scale(1.02) !important;
    box-shadow: 0 8px 20px rgba(237, 46, 142, 0.3) !important;
}

.btn-pink-blue:active { transform: scale(0.95) !important; }

.text-primary { color: #ed2e8e !important; }

.custom-pagination .pagination { gap: 10px; border: none; }
.custom-pagination .page-item .page-link {
    border-radius: 12px !important;
    border: 1px solid #f9a8d4 !important;
    color: #ec4899 !important;
    font-family: 'Itim', cursive;
    font-weight: bold;
    padding: 10px 18px;
    transition: all 0.3s ease;
    background: white;
}
.custom-pagination .page-item.active .page-link {
    background: linear-gradient(45deg, #ed2e8e, #f59e0b) !important;
    border: none !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(237, 46, 142, 0.3);
}
.custom-pagination .page-item .page-link:hover {
    transform: translateY(-3px);
    background-color: #fff1f2;
    border-color: #ec4899 !important;
}

.main-control-wrapper {
    background: linear-gradient(135deg, #fdf2f8, #f5f3ff) !important; 
    border-radius: 25px;
    padding: 15px;
    max-width: 850px; 
    width: 100%;
    box-shadow: none !important;
}

.inner-white-box {
    background: #ffffff !important;
    border: 1px solid #f9a8d4 !important;
    border-radius: 15px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    box-shadow: none !important;
}

.btn-search-flat, .btn-add-flat {
    background: linear-gradient(45deg, #ec4899, #d946ef) !important;
    color: white !important;
    border: none;
    width: 50px;
    height: 45px;
    border-radius: 15px;
    box-shadow: none !important;
}
.btn-add-flat { background: linear-gradient(45deg, #d946ef, #ec4899) !important; width: 45px; }
.btn-close-search {
    width: 50px; height: 45px; border-radius: 15px; border: 1px solid #d1d5db; background: #fdfdfd;
    display: flex; align-items: center; justify-content: center;
    color: #64748b; font-size: 20px; font-weight: 500; text-decoration: none; transition: all 0.25s ease;
}
.btn-close-search:hover { background: #e5e7eb; color: #334155; transform: translateY(-1px); }
</style>

<script>
function formatMoney(input) {
    let value = input.value.replace(/\D/g, '');
    if(value !== "") value = new Intl.NumberFormat('vi-VN').format(value);
    input.value = value;
}

let deleteId = null;
function setDeleteId(id) {
    deleteId = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').onclick = function () {
    const form = document.getElementById('deleteForm');
    form.action = '/budgets/' + deleteId;
    form.submit();
};

setTimeout(() => {
    ['toast-success', 'toast-error'].forEach(id => {
        let el = document.getElementById(id);
        if (el) {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        }
    });
}, 3000);

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleBudgetFormBtn');
    const formBox = document.getElementById('collapseAddForm');

    toggleBtn.addEventListener('click', () => {
        if (formBox.style.display === 'none') {
            formBox.style.display = 'block';
            toggleBtn.setAttribute('aria-expanded', 'true');
        } else {
            formBox.style.display = 'none';
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>
</x-app-layout>