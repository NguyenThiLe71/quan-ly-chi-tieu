<x-app-layout>
<div class="container py-4">
    <h3 class="fw-bold text-center mb-4 title-gradient">
        🔔 Thông báo
    </h3>

    {{-- FILTER --}}
    <div class="mb-3 d-flex gap-2 align-items-center flex-wrap">
        <a href="?status=all" class="btn btn-light">Tất cả</a>
        <a href="?status=unread" class="btn btn-light">Chưa đọc</a>
        <a href="?status=read" class="btn btn-light">Đã đọc</a>

        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="btn btn-gradient">Đánh dấu tất cả đã đọc</button>
        </form>
    </div>

    {{-- LIST --}}
    @forelse($notifications as $n)
        <div class="noti-card mb-2 d-flex justify-content-between align-items-center {{ $n->is_read ? '' : 'unread' }}">
            <div class="noti-item" data-id="{{ $n->id }}" style="cursor:pointer; flex:1;">
                <h6 class="fw-bold mb-1">
                    {{ $n->title }}
                    @if(!$n->is_read)
                        <span class="dot"></span>
                    @endif
                </h6>
                <small class="text-muted">{{ $n->message }}</small><br>
                <small class="text-secondary">
                    {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}
                </small>
            </div>

            <form method="POST" action="{{ route('notifications.delete', $n->id) }}">
                @csrf 
                @method('DELETE')
                <button class="btn btn-sm text-danger">🗑</button>
            </form>
        </div>
    @empty
        <p class="text-center text-muted">Không có thông báo</p>
    @endforelse
</div>

{{-- Nút Back to Top - Để ngoài Container để fixed vị trí chuẩn --}}
<button id="backToTop" class="btn-to-top shadow-lg" title="Lên đầu trang">
    ↑
</button>

{{-- FORM ẨN --}}
<form id="readForm" method="POST" style="display:none;">
    @csrf
</form>

{{-- JS --}}
<script>
// Xử lý đọc thông báo
document.querySelectorAll('.noti-item').forEach(el => {
    el.addEventListener('click', function(){
        let id = this.dataset.id;
        if(!id) return;
        let form = document.getElementById('readForm');
        form.action = `/notifications/${id}/read`;
        form.submit();
    });
});

// XỬ LÝ NÚT BACK TO TOP
const backToTopBtn = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

backToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
</script>

{{-- CSS --}}
<style>
.title-gradient {
    font-size: 28px;
    background: linear-gradient(45deg, #f43f5e, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}

.noti-card {
    background: white;
    padding: 15px;
    border-radius: 15px;
    border: 1px solid #eee;
    transition: 0.2s;
}

.noti-card:hover { transform: translateX(5px); }

.unread {
    background: #fff1f2;
    border-left: 5px solid #ec4899;
}

.dot {
    width: 8px; height: 8px;
    background: #ec4899;
    border-radius: 50%;
    display: inline-block;
    margin-left: 6px;
}

/* NÚT BACK TO TOP ĐÃ SỬA */
.btn-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    font-weight: bold;
    display: flex; /* Đổi thành flex */
    align-items: center;
    justify-content: center;
    z-index: 9999;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(.47,1.64,.41,.8); /* Hiệu ứng nảy nhẹ */
    box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4) !important;
    
    /* Ẩn mặc định bằng opacity để dùng transition mượt hơn */
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
}

.btn-to-top.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.btn-to-top:hover {
    transform: translateY(-5px) scale(1.1);
    filter: brightness(1.1);
}
</style>
</x-app-layout>