<x-app-layout>

<div class="container py-4">
   <div class="w-full flex justify-center mb-8 px-4">
    <h3 class="flex items-center gap-3 text-center" 
        style="font-family: 'Itim', cursive; font-size: 2.2rem; display: flex; align-items: center;">
        
        <span class="flex-shrink-0" style="filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.3));">
            🏷️
        </span>

        <span class="title-gradient" 
              style="font-weight: 900; 
                     letter-spacing: 0.5px;
                     /* Tạo độ dày cho nét chữ Itim */
                     text-shadow: 0.5px 0px 0px #e8117d, -0.5px 0px 0px #f756a6;
                     display: flex; align-items: center;">
            Phân loại chi tiêu
        </span>
    </h3>
</div>

    @if(session('success'))
        <div id="toast-success" class="alert alert-success shadow-sm mb-4 border-0 text-center">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Thêm Mới --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 gradient-box" style="border-radius: 25px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.store') }}" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="name" class="form-control input-soft" placeholder="Tên danh mục mới..." required>
                        <select name="type" class="form-select input-soft" style="width: 120px;">
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                        <button class="btn btn-gradient px-3 fw-bold">+</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Danh Sách --}}
    {{-- Bao bọc toàn bộ bằng swiper-container --}}
{{-- Grid Danh Sách với Swiper --}}
<div class="swiper mySwiper px-5" style="padding-left: 50px; padding-right: 50px;">
    <div class="swiper-wrapper">
        {{-- Vòng lặp chia nhóm 8 item --}}
        @foreach($categories->chunk(8) as $chunk)
            <div class="swiper-slide">
                <div class="row g-4 justify-content-center p-3">
                    @foreach($chunk as $cat)
                        <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                            <div class="category-card animate-item shadow-sm {{ $cat->type == 'income' ? 'border-income' : 'border-expense' }}">
                                <div class="text-center w-100">
                                    @php
                                        $nameLower = mb_strtolower($cat->name, 'UTF-8');
                                        $icon = ($cat->type == 'income') ? '💰' : '💸';
                                        if (str_contains($nameLower, 'ăn')) $icon = '🍔';
                                        elseif (str_contains($nameLower, 'lương')) $icon = '💵';
                                        elseif (str_contains($nameLower, 'thưởng')) $icon = '🧧';
                                        elseif (str_contains($nameLower, 'xe') || str_contains($nameLower, 'di chuyển')) $icon = '🚗';
                                        elseif (str_contains($nameLower, 'trọ')) $icon = '🏠';
                                        elseif (str_contains($nameLower, 'mỹ phẩm') || str_contains($nameLower, 'mặt nạ')) $icon = '💄';
                                        elseif (str_contains($nameLower, 'cá nhân')) $icon = '🪥';
                                        elseif (str_contains($nameLower, 'mua sắm')) $icon = '🛍️';
                                        elseif (str_contains($nameLower, 'thuốc')) $icon = '💊';
                                        elseif (str_contains($nameLower, 'giải trí')) $icon = '🎮';
                                        elseif (str_contains($nameLower, 'phát sinh')) $icon = '⚡';
                                    @endphp
                                    
                                    <div class="category-icon-main">{{ $icon }}</div>
                                    
                                    <div id="view-mode-{{ $cat->id }}">
                                        <span class="fw-bold d-block category-label mt-2">{{ $cat->name }}</span>
                                        <div class="category-actions mt-3">
                                            @if($cat->is_default)
                                                <div class="mac-dinh-label">MẶC ĐỊNH</div>
                                            @else
                                                <button type="button" class="btn-action pencil-modern" onclick="toggleEdit('{{ $cat->id }}')">✏️</button>
                                                <button type="button" class="btn-action trash-modern" onclick="setDeleteId('{{ $cat->id }}')">🗑</button>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!$cat->is_default)
                                        <div id="edit-mode-{{ $cat->id }}" class="d-none mt-2 px-2">
                                            <form method="POST" action="{{ route('categories.update', $cat->id) }}">
                                                @csrf @method('PUT')
                                                <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm text-center border-bottom-only mb-2" required>
                                                <div class="d-flex justify-content-center gap-1 mb-2">
                                                    <input type="radio" class="btn-check" name="type" id="inc-{{ $cat->id }}" value="income" {{ $cat->type == 'income' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-xs" for="inc-{{ $cat->id }}">In</label>
                                                    <input type="radio" class="btn-check" name="type" id="exp-{{ $cat->id }}" value="expense" {{ $cat->type == 'expense' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-xs" for="exp-{{ $cat->id }}">Ex</label>
                                                </div>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="submit" class="btn btn-xs btn-gradient-soft shadow-sm">Lưu</button>
                                                    <button type="button" class="btn btn-xs btn-light shadow-sm" onclick="toggleEdit('{{ $cat->id }}')">Hủy</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div> {{-- Hết text-center --}}
                            </div> {{-- Hết category-card --}}
                        </div> {{-- Hết col-6 --}}
                    @endforeach {{-- Hết vòng lặp 8 item --}}
                </div> {{-- Hết row g-4 --}}
            </div> {{-- Hết swiper-slide --}}
        @endforeach {{-- Hết vòng lặp chunk --}}
    </div> {{-- Hết swiper-wrapper --}}

    {{-- Nút điều hướng Swiper --}}
    <div class="swiper-button-next custom-swiper-nav"></div>
    <div class="swiper-button-prev custom-swiper-nav"></div>
    <div class="swiper-pagination mt-4"></div>
</div>
<!-- Popup xác nhận xoá -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom-pink">
            <div class="modal-body text-center p-5">
                <div class="warning-icon">⚠️</div>
                <h5 class="fw-bold mt-3">Xoá danh mục?</h5>
                <p class="text-secondary mb-4">Hành động này không thể hoàn tác</p>

                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Hủy</button>
<button id="confirmDeleteBtn" class="btn btn-danger px-4">Xác nhận xoá</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- form xoá (ẩn) -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
<style>
.category-card { 
    background: white; 
    width: 220px;   /* 🔥 tăng lên */
    height: 220px; 
    border-radius: 50%; /* 🔥 tròn */
    padding: 15px;
    border: 4px solid transparent;

    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    margin: auto;
    transition: all 0.3s ease;
}

/* 🔥 hover nâng cấp */
.category-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.2);
}

.border-expense { border-color: #fecaca; }
.border-income { border-color: #bbf7d0; }

.category-icon-main {
    font-size: 50px;
}

.category-label {
    font-size: 16px;
}

/* 🔥 icon animation */
.category-card:hover .category-icon-main {
    transform: scale(1.2) rotate(5deg);
}

/* 🔥 animation load */
.animate-item {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.5s ease forwards;
}

@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.category-actions { display: flex; justify-content: center; gap: 15px; padding-top: 12px; border-top: 1px dashed #eee; }
.btn-action {
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%; /* 🔥 QUAN TRỌNG */
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s;
}

/* ✏️ nút sửa */
.pencil-modern {
    background: #ffe4e6;
    color: #ec4899;
}

.trash-modern {
    background: #fee2e2;
    color: #f43f5e;
}

.btn-action:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.btn-xs { padding: 2px 8px; font-size: 11px; border-radius: 6px; }
.btn-outline-success { color: #15803d; border-color: #dcfce7; }
.btn-outline-danger { color: #b91c1c; border-color: #fee2e2; }

.btn-gradient-soft { background: linear-gradient(45deg, #d946ef, #ec4899); color: white; border: none; }

.border-bottom-only { border: none; border-bottom: 2px solid #ec4899; border-radius: 0; outline: none; box-shadow: none; font-size: 0.9rem; }

.title-gradient {
    /* Hồng đậm ở đầu ("Phân loại") chuyển sang hồng phấn ở đuôi ("chi tiêu") */
    background: linear-gradient(135deg, #ec4899 0%, #fbcfe8 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
    
    /* Hiệu ứng hào quang hồng nhẹ */
    filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.15));
}

.gradient-box { background: linear-gradient(135deg, #fdf2f8, #f5f3ff); }

.btn-gradient { background: linear-gradient(45deg, #d946ef, #ec4899); color: white; border: none; border-radius: 12px; }

/* 🔥 click effect */
.btn-gradient:active {
    transform: scale(0.9);
}

.input-soft { border: 1px solid #f9a8d4; border-radius: 12px; }

.mac-dinh-label { 
    font-size: 10px; 
    font-weight: 800; 
    color: #cbd5e1; 
    letter-spacing: 1px; 
}

/* 🔥 toast animation */
#toast-success {
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* --- CSS CHO MODAL MỚI (HỒNG TRẮNG TINH TẾ) --- */

.modal-content {
    /* Màu nền: Hồng trắng sữa pastel, rất tinh khiết */
    background: #fffafa !important; /* hoặc #fff8f8, tớ đề xuất #fffafa cho màu trắng hồng sữa đẹp nhất */
    
    border-radius: 25px !important; /* Giữ bo góc đẹp */
    border: none !important;
    
    /* Đổ bóng nhẹ và ấm hơn cho tông hồng */
    box-shadow: 0 10px 30px rgba(255, 192, 203, 0.25); 
    
    padding: 20px;
    animation: zoomIn 0.3s ease-out; /* Giữ hiệu ứng hiện mượt */
}

/* Nút Hủy (Trắng ngà tinh tế) */
.modal-body .btn-light {
    background: #fdfdfd !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 30px !important;
    font-weight: 600;
    color: #6c757d !important;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.modal-body .btn-light:hover {
    background: #f1f3f5 !important;
    transform: translateY(-1px);
}

/* Nút Xóa (Hồng trắng đỏ tươi tắn) */
.modal-body .btn-danger {
    background: #ff6b81 !important; /* Màu hồng đỏ tươi và dịu mắt hơn */
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 30px !important;
    font-weight: 600;
    color: white;
    box-shadow: 0 4px 12px rgba(255, 107, 129, 0.3);
    transition: all 0.2s ease;
}

.modal-body .btn-danger:hover {
    background: #ff5270 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(255, 107, 129, 0.4);
}

/* Icon cảnh báo và chữ */
.modal-body .warning-icon {
    font-size: 50px;
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
}

.modal-body h5 {
    color: #495057; /* Màu chữ tiêu đề tối hơn một chút cho dễ đọc */
}

.modal-body p {
    color: #868e96; /* Màu chữ mô tả */
}

/* Hiệu ứng phóng to mượt hơn */
@keyframes zoomIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
/* Tùy chỉnh mũi tên Swiper */
.custom-swiper-nav {
    color: #ec4899 !important;
    background: white;
    width: 60px !important;  /* To hơn nút cũ của ông */
    height: 60px !important;
    border-radius: 50%;
    box-shadow: 0 8px 20px rgba(236, 72, 153, 0.25);
    transition: all 0.3s ease;
    border: 2px solid #fdf2f8;
}

.custom-swiper-nav:after {
    font-size: 24px !important; /* Mũi tên to rõ ràng */
    font-weight: 900;
}

.custom-swiper-nav:hover {
    transform: scale(1.15);
    background: #fff1f5;
}

.swiper-button-next { 
    right: 5px !important; 
}
.swiper-button-prev { 
    left: 5px !important; 
}

/* Tăng z-index để nó luôn nằm trên các card */
.custom-swiper-nav {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    width: 40px !important; /* Thu nhỏ lại vì không còn nền */
}

/* 2. Thiết kế 2 mũi tên (Mũi tên kép) */
.custom-swiper-nav:after {
    /* Dùng ký hiệu mũi tên kép góc nhọn */
    content: '»' !important; 
    font-size: 45px !important; /* To cho dễ bấm */
    font-weight: 300 !important; /* Mảnh cho sang */
    color: #f472b6 !important; /* Màu hồng pastel */
    transition: all 0.3s ease;
    display: block;
}
/* Mũi tên bên trái quay ngược lại */
.swiper-button-prev.custom-swiper-nav:after {
    content: '«' !important;
}

/* 3. Hiệu ứng khi di chuột vào (Hover) */
.custom-swiper-nav:hover:after {
    color: #ec4899 !important; /* Hồng đậm hơn */
    transform: scale(1.2); /* Phóng to nhẹ */
    text-shadow: 0 0 15px rgba(236, 72, 153, 0.4); /* Tạo chút hào quang hồng */
}

/* 4. Hiệu ứng khi bấm (Active) */
.custom-swiper-nav:active:after {
    transform: scale(0.9);
}

/* Ẩn mũi tên nếu không có trang sau (Tránh bị thừa khi có < 8 mục) */
.swiper-button-disabled {
    opacity: 0 !important;
    cursor: default;
}

.swiper-pagination-bullet-active {
    background: #ec4899 !important;
    width: 20px;
    border-radius: 5px;
}
</style>

<script>
function toggleEdit(id) {
    const v = document.getElementById('view-mode-' + id);
    const e = document.getElementById('edit-mode-' + id);

    if (!v || !e) return;

    if (e.classList.contains('d-none')) {
        e.classList.remove('d-none');
        v.classList.add('d-none');

        // 🔥 auto focus
        const input = e.querySelector('input[name="name"]');
        if (input) input.focus();
    } else {
        e.classList.add('d-none');
        v.classList.remove('d-none');
    }
}

let deleteId = null;

function setDeleteId(id) {
    deleteId = id;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// khi bấm xác nhận xoá
document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!deleteId) return;

    const form = document.getElementById('deleteForm');
    form.action = '/categories/' + deleteId;
    form.submit();
});
setTimeout(() => {
    const successAlert = document.getElementById('toast-success');
    const errorAlert = document.getElementById('toast-error');

    if (successAlert) {
        successAlert.style.transition = "opacity 0.5s ease";
        successAlert.style.opacity = "0";
        setTimeout(() => successAlert.remove(), 500);
    }

    if (errorAlert) {
        errorAlert.style.transition = "opacity 0.5s ease";
        errorAlert.style.opacity = "0";
        setTimeout(() => errorAlert.remove(), 500);
    }
}, 3000);

// 🔥 delay animation từng card
document.querySelectorAll('.animate-item').forEach((el, i) => {
    el.style.animationDelay = (i * 0.05) + 's';
});
document.addEventListener('DOMContentLoaded', function () {
    const swiper = new Swiper(".mySwiper", {
        slidesPerView: 1,
        spaceBetween: 20,
        // Khi tạo thêm mục mới, nó sẽ tự động nhận diện và cho phép lướt
        observer: true,
        observeParents: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });
});
</script>

</x-app-layout>