<x-app-layout>

<div class="container py-4">
    <div class="w-full flex justify-center mb-6 px-4">
        <h3 class="flex items-center gap-3 text-center" 
            style="font-family: 'Itim', cursive; font-size: 2.2rem; display: flex; align-items: center;">
            
            <span class="flex-shrink-0" style="filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.3));">
                🐷
            </span>

            <span class="title-gradient" 
                  style="font-weight: 900; 
                         letter-spacing: 0.5px;
                          text-shadow: 0.5px 0px 0px #e8117d, -0.5px 0px 0px #f756a6;
                         display: flex; align-items: center;">
                Heo đất tiết kiệm
            </span>
        </h3>
    </div>
</div>

    {{-- THÔNG BÁO SUCCESS/ERROR --}}
    @if(session('success'))
        <div id="toast-success" class="alert alert-success shadow-sm border-0 text-center mb-4" style="border-radius: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="toast-error" class="alert alert-danger shadow-sm mb-4 border-0 text-center" style="border-radius: 15px;">
            ❌ {{ session('error') }}
        </div>
    @endif

 {{-- 🔥 CỤM ĐIỀU KHIỂN: THIẾT KẾ LUXURY PHẲNG --}}
<div class="d-flex justify-content-center align-items-center gap-3 mb-5 flex-nowrap animate-item" style="width: 100%; max-width: 1200px; margin: 0 auto;">
    
    <div class="main-control-wrapper shadow-none" style="flex: 1; min-width: 0;">
        <div class="p-1">
            {{-- FORM TÌM KIẾM & LỌC --}}
            <form method="GET" action="{{ route('goals.index') }}" class="d-flex align-items-center gap-2">
                <div class="d-flex gap-2 flex-grow-1">
                    {{-- Ô tìm kiếm tên --}}
                    <div class="inner-white-box" style="flex: 2;">
                        <input type="text" name="search" id="goalSearch" value="{{ request('search') }}" aria-label="Tìm tên mục tiêu"
                               class="form-control border-0 bg-transparent" placeholder="Tìm tên mục tiêu..." style="height: 45px;">
                    </div>

                    {{-- Ô chọn Tháng/Năm --}}
                    <div class="inner-white-box" style="flex: 1; min-width: 140px;">
                        <input type="month" name="month_year" aria-label="Chọn tháng năm lọc mục tiêu" value="{{ request('month_year') ?? date('Y-m') }}"
                               class="form-control border-0 bg-transparent" style="height: 45px;">
                    </div>
                    
                    <button type="submit" id="searchBtn" class="btn btn-search-flat" aria-label="Tìm kiếm">🔍</button>

                    @if(request('search') || request('month_year'))
                        <a href="{{ route('goals.index') }}" id="clearSearch" class="btn btn-light d-flex align-items-center justify-content-center border" 
                           style="width: 45px; height: 45px; border-radius: 12px; background: #ffffff; flex-shrink: 0; color: #64748b;">
                           ✕
                        </a>
                    @endif
                </div>
                <button class="btn btn-add-flat" type="button" id="toggleGoalFormBtn" aria-label="Thêm mới mục tiêu">
                    <span class="fs-4">+</span>
                </button>
            </form>

            {{-- HÀNG XỔ XUỐNG ĐỂ THÊM MỚI --}}
            <div id="collapseGoalForm" style="{{ $errors->any() ? 'display:block;' : 'display:none;' }}">
                <div class="pt-3 mt-3 border-top border-danger border-opacity-10">
                    <form method="POST" action="{{ route('goals.store') }}" class="d-flex align-items-center gap-2 flex-nowrap">
                        @csrf
                        <div style="flex: 2; min-width: 150px;">
                            <input name="name" class="form-control input-pink-style" placeholder="Tên mục tiêu..." aria-label="Nhập tên mục tiêu mới" required>
                        </div>
                        <div style="flex: 1.5; min-width: 120px;">
                            <input name="target_amount" type="number" min="0" class="form-control input-pink-style fw-bold text-primary" placeholder="Số tiền mục tiêu" aria-label="Nhập số tiền mục tiêu">
                        </div>
                        <div style="flex: 1.5; min-width: 150px;">
                            <input type="date" name="deadline" aria-label="Chọn ngày hết hạn" class="form-control input-pink-style">
                        </div>
                        <div style="flex: 0.8; min-width: 80px;">
                            <button class="btn btn-pink-blue fw-bold w-100" style="height: 45px; border-radius: 12px; white-space: nowrap;">
                                Lưu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- LIST MỤC TIÊU --}}
    <div class="row g-4 d-flex align-items-stretch" id="goalList">
        @foreach($goals as $index => $goal)
            @php
                $percent = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                $width = min($percent, 100);
                
                if($goal->status == 'expired') {
                    $accent = '#ef4444';
                    $bg = '#fef2f2';
                }
                elseif($percent < 30) {
                    $accent = '#f43f5e';
                    $bg = '#fff1f2';
                }
                elseif($percent < 80) {
                    $accent = '#a855f7';
                    $bg = '#f3e8ff';
                }
                else {
                    $accent = '#10b981';
                    $bg = '#ecfdf5';
                }

                $colClass = ($index % 4 == 0 || $index % 4 == 3) ? 'col-lg-7' : 'col-lg-5';
            @endphp

            <div class="col-12 {{ $colClass }} d-flex animate-item goal-item">
                <div class="polaroid-card w-100 shadow-sm d-flex flex-column" style="--accent: {{ $accent }};">
                    <div class="polaroid-image-area" style="background: {{ $bg }}">
                        <div class="goal-icon">
                            @if($goal->status == 'expired') ⏰ @elseif($percent >= 100) 👑 @else 🎯 @endif
                        </div>
                    </div>
                    
                    <div class="polaroid-content-area d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold mb-0 text-dark goal-name">
                                {{ $goal->name }}
                            </h5>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-percent" style='background-color: {{ $bg }}; color: {{ $accent }};'>
                                    {{ round($percent) }}%
                                </span>

                                @if($goal->status == 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @elseif($goal->status == 'expired')
                                    <span class="badge bg-danger">Hết hạn</span>
                                @else
                                    <span class="badge bg-primary">Đang thực hiện</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- TỐI ƯU ACCESSIBILITY: Thay text-muted bằng text-secondary trầm hơn để tăng độ tương phản chữ --}}
                        <p class="extra-small mb-3 {{ $goal->status == 'expired' ? 'text-danger fw-bold' : 'text-secondary fw-semibold' }}">
                            📅 Hạn: {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') : '---' }}
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <div>
                                    <span class="fs-4 fw-bold" style="color: {{ $accent }}">{{ number_format($goal->current_amount) }}</span>
                                    <span class="text-secondary small fw-medium"> / {{ number_format($goal->target_amount) }}</span>
                                </div>
                            </div>

                            <div class="progress-bar-wrapper">
                                <div class="progress-bar-inner" style="width: {{ $width }}%; background: {{ $accent }}; box-shadow: 0 0 10px {{ $accent }}66;"></div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex gap-2 align-items-center">
                                {{-- Form Nạp/Rút tiền (Icon Ví) --}}
                                <form method="POST" action="{{ route('goals.addMoney', $goal->id) }}" class="d-flex flex-grow-1 gap-2">
                                    @csrf
                                    <input name="amount" type="number" class="form-control form-control-sm polaroid-input" placeholder="+ / - tiền..." aria-label="Số tiền thay đổi">
                                    <button type="submit" title="Nạp/Rút tiền" class="btn-action wallet-modern">👛</button>
                                </form>

                                {{-- Nút Sửa thông tin (Icon Bút chì) --}}
                                <button title="Sửa mục tiêu" class="btn-action pencil-modern" 
                                        data-bs-toggle="modal" data-bs-target="#editModal{{ $goal->id }}">
                                    ✏️
                                </button>

                                {{-- Nút Xóa --}}
                                <button class="btn-action trash-modern" aria-label="Xóa mục tiêu" onclick="setDeleteId('{{ $goal->id }}')">🗑</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="custom-pagination-wrapper">
        {{-- Trái --}}
        @if ($goals->onFirstPage())
            <div class="arrow-btn left disabled">‹</div>
        @else
            <a href="{{ $goals->appends(request()->query())->previousPageUrl() }}" 
               class="arrow-btn left"
               onclick="loadPage(event, this.href)">‹</a>
        @endif

        {{-- Phải --}}
        @if ($goals->hasMorePages())
            <a href="{{ $goals->appends(request()->query())->nextPageUrl() }}" 
               class="arrow-btn right"
               onclick="loadPage(event, this.href)">›</a>
        @else
            <div class="arrow-btn right disabled">›</div>
        @endif
    </div>
</div>

{{-- MODAL XÓA --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-delete-modal">
            <div class="modal-body text-center p-5">
                <div class="warning-icon">⚠️</div>
                <h5 class="fw-bold mt-3 text-dark">Xóa mục tiêu?</h5>
                <p class="text-secondary fw-medium mb-4">Hành động này không thể hoàn tác</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Hủy</button>
                    <button id="confirmDeleteBtn" class="btn btn-danger px-4">Xác nhận xóa</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
@foreach($goals as $goal)
<div class="modal fade" id="editModal{{ $goal->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-delete-modal" style="background: #fff;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-dark">✏️ Chỉnh sửa mục tiêu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('goals.update', $goal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Tên mục tiêu</label>
                        <input name="name" class="form-control input-pink-style" value="{{ $goal->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Số tiền đích (VNĐ)</label>
                        <input name="target_amount" type="number" class="form-control input-pink-style fw-bold text-primary" value="{{ $goal->target_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Ngày hết hạn</label>
                        <input type="date" name="deadline" class="form-control input-pink-style" value="{{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Hủy</button>
                    <button type="submit" class="btn btn-pink-blue px-4" style="height: 45px; border-radius: 12px;">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<form id="deleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<style>
    body { background-color: #fdf2f8; }

    /* Animation */
    .animate-item { opacity: 0; transform: translateY(30px); }
    .show-now {
        opacity: 1;
        transform: translateY(0);
        transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Style Input & Buttons */
    .title-gradient {
        background: linear-gradient(135deg, #ec4899 0%, #fbcfe8 100%) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.15));
        display: inline-flex;
        align-items: center;
    }
    .input-pink-style { border-radius: 12px; border: 1px solid #f9a8d4 !important; height: 45px; padding-left: 15px; }
    .input-pink-style:focus { box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.1); border-color: #ec4899 !important; }
    .btn-gradient { background: linear-gradient(45deg, #d946ef, #ec4899); color: white; border: none; transition: 0.3s; }
    .btn-gradient:hover { filter: brightness(1.1); transform: scale(1.05); color: white; }

    /* Polaroid Cards */
    .polaroid-card { background: white; border-radius: 20px; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; border: 1px solid transparent; }
    .polaroid-card:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1) !important; border: 1px solid var(--accent); }
    .polaroid-image-area { height: 120px; display: flex; align-items: center; justify-content: center; }
    .goal-icon { font-size: 60px; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1)); transition: 0.4s; }
    .polaroid-card:hover .goal-icon { transform: scale(1.2) rotate(10deg); }
    .polaroid-content-area { padding: 25px; }

    /* Progress Bar */
    .progress-bar-wrapper { height: 10px; background: #f1f5f9; border-radius: 20px; overflow: hidden; }
    .progress-bar-inner { height: 100%; border-radius: 20px; transition: width 1s ease-in-out; }
    
    /* Actions */
    .btn-action { border: none; width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .pencil-modern { background: #fae0ce; color: #c2410c; }
    .trash-modern { background: #fae2e4; color: #f43f5e; }
    .badge-percent { font-weight: 800; font-size: 13px; padding: 6px 14px; border-radius: 12px; }

    /* Modal Delete */
    .custom-delete-modal { border-radius: 25px; border: none; background: #fffafa; animation: zoomIn 0.3s ease; }
    .warning-icon { font-size: 55px; }
    .btn-danger { background: linear-gradient(45deg, #fb7185, #f43f5e); border: none; border-radius: 12px; font-weight: bold; }
    @keyframes zoomIn { from { transform: scale(0.85); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    .btn-pink-blue {
        background: linear-gradient(45deg, #ed2e8e 0%, #80e4f1 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(128, 228, 241, 0.3) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .btn-pink-blue:hover {
        background: linear-gradient(45deg, #f472b6 10%, #60a5fa 90%) !important;
        transform: translateY(-3px) scale(1.02) !important;
        box-shadow: 0 8px 20px rgba(237, 46, 142, 0.3) !important;
    }
    .btn-pink-blue:active { transform: scale(0.95) !important; }

    .custom-pagination .page-link {
        border: none !important;
        margin: 0 5px;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px !important;
        background: white !important;
        color: #ed2e8e !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        font-weight: bold;
    }
    .custom-pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #ed2e8e, #ff9a8b) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(237, 46, 142, 0.4);
    }
    .custom-pagination .page-link:hover {
        transform: translateY(-3px);
        background: #fff0f6 !important;
        color: #d946ef !important;
    }
    .custom-pagination nav div:first-child { display: none !important; }

    .custom-pagination-wrapper {
        position: fixed;
        top: 50%;
        left: 0;
        width: 100%;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 999;
        pointer-events: none;
    }
    .arrow-btn {
        pointer-events: auto;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #ec4899;
        color: #ec4899;
        font-size: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, background 0.25s ease, color 0.25s ease;
        box-shadow: 0 4px 12px rgba(236, 72, 153, 0.25);
        will-change: transform;
        backface-visibility: hidden;
    }
    .arrow-btn:hover {
        transform: scale(1.1) translateY(-3px);
        background: #ec487c;
        color: white;
        box-shadow: 0 0 25px rgba(236, 72, 153, 0.8);
    }
    .arrow-btn.disabled { opacity: 0.3; pointer-events: none; }

    .main-control-wrapper {
        background: linear-gradient(135deg, #fdf2f8, #f5f3ff) !important;
        border-radius: 25px;
        padding: 15px;
        border: none !important;
    }
    .inner-white-box {
        background: #ffffff !important;
        border: 1px solid #f9a8d4 !important;
        border-radius: 12px;
        padding: 0 10px;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    .inner-white-box:focus-within {
        border-color: #ec4899 !important;
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
    }
    .btn-search-flat, .btn-add-flat {
        background: linear-gradient(45deg, #ec4899, #d946ef) !important;
        color: white !important;
        border: none;
        width: 50px;
        height: 45px;
        border-radius: 12px;
        flex-shrink: 0;
        transition: 0.3s;
    }
    .btn-search-flat:hover, .btn-add-flat:hover { transform: scale(1.05); filter: brightness(1.1); }

    .wallet-modern { background: #e0f2fe; color: #0369a1; transition: all 0.3s ease; }
    .wallet-modern:hover { background: #bae6fd; transform: translateY(-2px); }
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Animation hiện các phần tử
        const items = document.querySelectorAll('.animate-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('show-now');
            }, index * 100);
        });

        // 🔥 TOGGLE FORM THÊM MỤC TIÊU
        const toggleBtn = document.getElementById('toggleGoalFormBtn');
        const formBox = document.getElementById('collapseGoalForm');

        if (toggleBtn && formBox) {
            toggleBtn.addEventListener('click', () => {
                formBox.style.display = (formBox.style.display === 'none') ? 'block' : 'none';
            });
        }

        // Tự ẩn thông báo toast
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
    });

    // Xử lý Xóa
    let deleteId = null;
    function setDeleteId(id) {
        deleteId = id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.onclick = function () {
            const form = document.getElementById('deleteForm');
            if (form) {
                form.action = '/goals/' + deleteId;
                form.submit();
            }
        };
    }

    // Phân trang AJAX
    function loadPage(e, url) {
        e.preventDefault();
        const container = document.getElementById('goalList');
        if (!container) return;

        container.style.opacity = "0.3";

        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newList = doc.getElementById('goalList').innerHTML;
                container.innerHTML = newList;

                const newNav = doc.querySelector('.custom-pagination-wrapper').innerHTML;
                document.querySelector('.custom-pagination-wrapper').innerHTML = newNav;

                container.style.opacity = "1";

                const items = document.querySelectorAll('.goal-item');
                items.forEach((item, index) => {
                    item.classList.remove('show-now');
                    setTimeout(() => {
                        item.classList.add('show-now');
                    }, index * 80);
                });
            }).catch(err => console.error("Lỗi phân trang:", err));
    }
</script>

</x-app-layout>