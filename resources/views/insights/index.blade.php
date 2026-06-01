<x-app-layout>
    <style>
        body { font-family: 'Itim', cursive; }

        .luxury-gradient {
            background: linear-gradient(45deg, #f976bc 0%, #f5a18a 100%) !important;
            border: none;
        }

        .card { border-radius: 15px; border: none; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        #chat-box { z-index: 1050; }

        .insight-card {
            border: none !important;
            border-radius: 15px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
            background: #ffffff !important;
        }

        .type-pattern { border-left: 6px solid #1d8ae9 !important; }
        .type-warning { border-left: 6px solid #de0000 !important; }
        .type-prediction { border-left: 6px solid #0db952 !important; }
        .type-recommendation { border-left: 6px solid #f8a407 !important; }

        .type-label {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #bf0e46 !important;
        }

       .title-gradient {
    background: linear-gradient(135deg, #ec4899 0%, #fbcfe8 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;

    filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.15));

    /* 🔥 thêm độ đậm giống ảnh mẫu */
    text-shadow:
        0.5px 0px 0px #e8117d,
       -0.5px 0px 0px #f756a6;

    display: inline-flex;
    align-items: center;
}

        .btn-analysis {
            background: linear-gradient(45deg, #FF9966, #FF5E62) !important;
            color: white !important;
            border: none;
        }

        .btn-ask-ai {
            background: linear-gradient(45deg, #f5e534 0%, #fba6cf 100%) !important;
            color: #85032c !important;
            border: none;
        }

        .chart-card {
            border-radius: 20px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        /* 🐷 Thêm style cho widget vòng tròn Điểm sức khỏe tài chính (Không đổi bất kỳ class cũ nào) */
        .circle-bg { fill: none; stroke: #fff1f5; stroke-width: 3.8; }
        .circle-progress { fill: none; stroke-dasharray: 0, 100; stroke-width: 3.8; stroke-linecap: round; transition: stroke-dasharray 1s ease-in-out; }
/* Style cho khối tin nhắn chat */
.message-user {
    background-color: #fce4ec; /* Hồng nhạt cho user */
    padding: 8px 12px;
    border-radius: 15px 15px 0 15px;
    margin-bottom: 10px;
    text-align: right;
    color: #880e4f;
    font-weight: 500;
}
.message-ai {
    background-color: #ffffff;
    padding: 8px 12px;
    border-radius: 0 15px 15px 15px;
    margin-bottom: 10px;
    border: 1px solid #f8bbd0;
    color: #444;
}
.message-ai b { color: #d81b60; } /* Màu nổi bật cho tag AI */
.health-box {
    /* Màu nền hồng pastel nhạt để cả khung đổ màu */
    background: linear-gradient(135deg, #f5adbc 0%, #f1d6d6 100%) !important;
    border: 1px solid rgba(255, 153, 102, 0.2) !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 20px rgba(255, 131, 193, 0.22) !important;
    position: relative;
    overflow: hidden;
    transition: 0.3s;
}

/* Hiệu ứng mờ ảo cho nền khi di chuột vào */
.health-box:hover {
    background: linear-gradient(135deg, #ffeef2 0%, #f4b5b5 100%) !important;
}
    </style>

<div class="container-fluid py-3 px-4">

    <div class="w-full flex justify-center mb-5">
        <h3 class="flex items-center gap-3 text-center"
            style="font-family:'Itim',cursive;font-size:2.2rem;">
            <span aria-hidden="true">💕</span>
            <span class="title-gradient fw-bold">
                Tiền bạc trong tầm tay
            </span>
        </h3>
    </div>

    {{-- chọn tháng kiểu mới --}}
<div class="card p-4 mb-5 shadow-sm border-0 mx-auto" 
     style="max-width: 950px; border-radius: 20px; background:rgba(255,255,255,.9); backdrop-filter:blur(10px);">
    
    {{-- Hàng 1: Phân tích --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mb-3 pb-3 border-bottom border-light">
        <span class="fw-bold">Phân tích:</span>
        <input type="month" id="filterDate" value="{{ now()->format('Y-m') }}" 
               class="form-control rounded-pill border-0 shadow-sm px-4" 
               style="width: 220px; background:#fff5f5; color:#FF6B6B; font-weight:bold; height: 50px;">
        
        <button onclick="loadAnalysis()" class="btn btn-analysis rounded-pill px-4 fw-bold shadow-sm" style="height: 50px;">
            🤖 Phân tích
        </button>
        
        <button onclick="downloadReport()" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm" style="height: 50px;">
            📥 PDF
        </button>
    </div>

    {{-- Hàng 2: So sánh --}}
    <div class="d-flex align-items-center justify-content-center gap-3">
    <span class="fw-bold"> So sánh:</span>
    
    <input type="month" id="month1" value="{{ now()->subMonth()->format('Y-m') }}" 
           class="form-control rounded-pill border-0 shadow-sm px-4" 
           style="width: 220px; background:#fff5f5; color:#FF6B6B; font-weight:bold; height: 50px;">
    
    <span class="fw-bold">vs</span>
    
    <input type="month" id="month2" value="{{ now()->format('Y-m') }}" 
           class="form-control rounded-pill border-0 shadow-sm px-4" 
           style="width: 220px; background:#fff5f5; color:#FF6B6B; font-weight:bold; height: 50px;">
    
    {{-- Nút bấm thông minh: Kiểm tra Premium ngay tại Blade --}}
    <button onclick="{{ auth()->user()->is_premium ? 'compareAnalysis()' : 'showUpgradeModal()' }}" 
            class="btn rounded-pill px-4 fw-bold shadow-sm" 
            style="height: 50px; 
                   background: {{ auth()->user()->is_premium ? 'linear-gradient(45deg, #FF9966, #FF5E62)' : '#e0e0e0' }} !important; 
                   color: {{ auth()->user()->is_premium ? 'white' : '#757575' }} !important; 
                   border: none;">
        {{ auth()->user()->is_premium ? '⚖️ So sánh' : '👑 Premium' }}
    </button>
</div>

{{-- Script thông báo đơn giản --}}
<script>
    function showUpgradeModal() {
    if(confirm("🐷 Tính năng So sánh chuyên sâu chỉ dành cho tài khoản Premium.\nBạn có muốn nâng cấp ngay không?")) {
        window.location.href = "{{ route('payment.checkout') }}";
    }
}
</script>
</div>
</div>

    {{-- Thẻ dự đoán & Điểm tài chính được đặt trong hàng để đảm bảo layout cân đối --}}
    <div class="row mb-4">
        <div class="col-md-7">
            {{-- prediction --}}
            <div class="card luxury-gradient h-100 shadow-sm">
                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                    <h5 class="text-uppercase" style="font-size:.9rem;letter-spacing:2px;">
                        Dự đoán chi tiêu có thể xảy ra tiếp trong tháng này
                    </h5>

                    <h1 id="prediction" class="display-4 fw-bold">--</h1>

                    <p class="mb-0 opacity-75">
                        AI dựa trên lịch sử giao dịch gần đây
                    </p>
                </div>
            </div>
        </div>

        {{-- 🐷 THÊM MỚI: Thẻ Điểm Sức Khỏe Tài Chính (Thiết kế bo tròn 15px đúng chuẩn .card cũ của ông) --}}
       <div class="col-md-5 mt-3 mt-md-0">
    <div class="card health-box p-4 text-center d-flex flex-column align-items-center justify-content-center h-100">
        <h5 class="fw-bold text-muted mb-3" style="font-size: 0.95rem;">🐷 Điểm Sức Khỏe Tài Chính</h5>
        
        <div class="position-relative d-flex align-items-center justify-content-center mb-2" style="width: 120px; height: 120px;">
            <svg viewBox="0 0 36 36" class="w-100 h-100">
                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path id="healthCircle" class="circle-progress" stroke="url(#scoreGlow)" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <defs>
                    <linearGradient id="scoreGlow" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FF5E62" />
                        <stop offset="100%" stop-color="#FF9966" />
                    </linearGradient>
                </defs>
            </svg>
            <div class="position-absolute text-center">
                <h2 id="healthScore" class="fw-bold mb-0 text-dark" style="font-size: 1.8rem; font-family: 'Itim';">--</h2>
                <span id="pigStatus" style="font-size: 1rem;">🐷</span>
            </div>
        </div>
        
        <p id="healthComment" class="mb-0 text-muted small fw-bold">Đang tính toán mức độ an toàn dòng tiền...</p>
    </div>
</div>

  {{-- Chart Xu hướng (Giữ nguyên) --}}
    <div class="chart-card mb-4">
        <h5 class="fw-bold mb-3 text-center">
             Biểu đồ xu hướng chi tiêu dùng để AI dự đoán
        </h5>
        <canvas id="expenseChart" height="100" role="img" aria-label="Biểu đồ xu hướng chi tiêu thực tế và dự báo của trí tuệ nhân tạo"></canvas>
        <div class="chart-note" id="chartExplain">
            AI đang phân tích dữ liệu...
        </div>
    </div>

    {{-- insights --}}
    <div id="insights" class="row">
        <div class="text-center w-100 py-5">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-2">Đang phân tích dữ liệu...</p>
        </div>
    </div>

</div>

{{-- chat --}}
<div id="chat-box"
     style="position:fixed;bottom:20px;right:20px;width:350px;display:none;">

    <div class="card shadow-lg border-0">

        <div class="card-header btn-ask-ai d-flex justify-content-between align-items-center">
            <span class="fw-bold">💬 Trợ lý AI</span>

            <button type="button" class="btn-close"
                onclick="document.getElementById('chat-box').style.display='none'"
                aria-label="Đóng hộp hội thoại"></button>
        </div>

        <div class="card-body bg-light"
             id="chat-content"
             style="height:300px;overflow-y:auto;font-size:.9rem;">
        </div>

        <div class="card-footer bg-white border-0">
            <div class="input-group">
                <input type="text"
                       id="chat-input"
                       class="form-control"
                       placeholder="Hỏi thêm..."
                       aria-label="Nội dung tin nhắn gửi tới AI">

                <button class="btn btn-ask-ai" id="btn-send" aria-label="Gửi tin nhắn">Gửi</button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

@push('scripts')
<script>
let expenseChart = null;
let currentInsightContent = "";
// Biến toàn cục để lưu instance của biểu đồ so sánh
let compareChart = null;

function compareAnalysis() {
    const m1Id = document.getElementById('month1').value;
    const m2Id = document.getElementById('month2').value;
    const insightDiv = document.getElementById('compareInsight');
    
    if (insightDiv) insightDiv.innerHTML = `<div class="alert alert-warning mt-3">🐷 Đang kiểm tra quyền Premium và tải dữ liệu...</div>`;

    // Bước 1: Lấy dữ liệu từ Laravel (Controller đã chặn 403 nếu không phải Premium)
    fetch(`/insights/compare?month1=${m1Id}&month2=${m2Id}`)
        .then(res => {
            // Kiểm tra lỗi Premium
            if (res.status === 403) {
                throw new Error("PREMIUM_REQUIRED");
            }
            if (!res.ok) throw new Error("SERVER_ERROR");
            return res.json();
        })
        .then(data => {
            // Bước 2: Gửi dữ liệu sang FastAPI
            return fetch('http://127.0.0.1:8000/compare-insight', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    month1: data.month1,
                    month2: data.month2,
                    m1_label: "Tháng " + m1Id,
                    m2_label: "Tháng " + m2Id 
                })
            });
        })
        .then(res => res.json())
        .then(result => {
            // 1. Hiển thị Modal
            const modalEl = document.getElementById('compareModal');
            new bootstrap.Modal(modalEl).show();

            // 2. Vẽ biểu đồ
            renderCompareChart(result.month1, result.month2);

            // 3. Hiển thị lời khuyên
            if (insightDiv) {
                insightDiv.innerHTML = `<div class="alert alert-info mt-3" style="border-radius:15px;"><b>🐷 Trợ lý tài chính:</b><br>${result.insight}</div>`;
            }
        })
        .catch(err => {
            console.error("Lỗi:", err);
            if (err.message === "PREMIUM_REQUIRED") {
                alert("👑 Tính năng này chỉ dành cho tài khoản Premium.\nHãy nâng cấp để so sánh chi tiêu chuyên sâu nhé!");
                if (insightDiv) insightDiv.innerHTML = `<div class="alert alert-danger mt-3">⚠️ Tính năng chỉ dành cho Premium!</div>`;
            } else {
                if (insightDiv) insightDiv.innerHTML = `<div class="alert alert-danger mt-3">⚠️ Có lỗi xảy ra, vui lòng thử lại!</div>`;
            }
        });
}

function renderCompareChart(m1Data, m2Data) {
    const ctx = document.getElementById('compareChart').getContext('2d');
    
    // Xóa chart cũ nếu đã tồn tại để tránh lỗi "Chart already in use"
    if (compareChart) {
        compareChart.destroy();
    }

    const labels = [...new Set([...Object.keys(m1Data), ...Object.keys(m2Data)])];
    
    compareChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { 
                    label: 'Tháng cũ', 
                    data: labels.map(l => m1Data[l] || 0), 
                    backgroundColor: '#ecbfbb', // Màu xám nhạt
                    borderRadius: 8
                },
                { 
                    label: 'Tháng mới', 
                    data: labels.map(l => m2Data[l] || 0), 
                    backgroundColor: '#d71174', // Màu hồng
                    borderRadius: 8
                }
            ]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: false, // Để chart khít với khung Modal
            scales: { y: { beginAtZero: true } },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
}
// Hàm cập nhật Chart
function renderChart(labels, values, prediction = 0) {
    const canvasEl = document.getElementById('expenseChart');
    if (!canvasEl) return;
    const ctx = canvasEl.getContext('2d');
    if (expenseChart) expenseChart.destroy();

    const gradient = ctx.createLinearGradient(0,0,0,300);
    gradient.addColorStop(0,'rgba(236,72,153,0.35)');
    gradient.addColorStop(1,'rgba(236,72,153,0.02)');

    let futureLabels = [...labels, 'Dự đoán'];
    let futureValues = [...values, prediction];

    expenseChart = new Chart(ctx,{
        type:'line',
        data:{
            labels: futureLabels,
            datasets:[
                { label:'Chi tiêu thực tế', data: values, borderColor:'#ec4899', backgroundColor:gradient, fill:true, tension:.4, borderWidth:3, pointRadius:5 },
                { label:'AI dự đoán', data: futureValues.map((v,i)=> i === futureValues.length-1 ? v : null), borderColor:'#ff5722', pointBackgroundColor:'#ff5722', pointRadius:7, showLine:false }
            ]
        },
        options:{ responsive:true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
    });
}

function explainTrend(values,prediction){
    if(values.length < 2) return "AI chưa đủ dữ liệu, dự đoán dựa trên giao dịch hiện tại.";
    const avg = values.reduce((a,b)=>a+b,0)/values.length;
    const last = values[values.length-1];
    if(last > avg * 1.5) return "Chi tiêu gần đây tăng mạnh nên AI dự đoán tháng này sẽ cao hơn bình thường.";
    if(last < avg) return "Chi tiêu gần đây đang giảm nên AI dự đoán mức chi khá ổn định.";
    return "AI dự đoán dựa trên xu hướng chi tiêu ổn định từ các giao dịch gần đây.";
}

function updateHealthWidget(score) {
    const scoreEl = document.getElementById('healthScore');
    const circleEl = document.getElementById('healthCircle');
    const commentEl = document.getElementById('healthComment');
    const pigEl = document.getElementById('pigStatus');
    if (!scoreEl || !circleEl) return;
    scoreEl.innerText = score;
    circleEl.style.transition = "none";
    circleEl.style.strokeDasharray = "0, 100";
    setTimeout(() => {
        circleEl.style.transition = "stroke-dasharray 1.5s ease-in-out";
        circleEl.style.strokeDasharray = `${score}, 100`;
    }, 50);

    if (score == 0) { pigEl.innerText = "🐷"; commentEl.innerText = "Chưa có giao dịch, hũ tài chính đang chờ bạn!"; commentEl.style.color = "#6c757d"; }
    else if (score >= 80) { pigEl.innerText = "🥰🐷"; commentEl.innerText = "Xuất sắc! Hũ tài chính cực kỳ an toàn."; commentEl.style.color = "#0db952"; }
    else if (score >= 50) { pigEl.innerText = "😐🐷"; commentEl.innerText = "Khá ổn, nhưng cần chú ý các khoản chi phát sinh."; commentEl.style.color = "#f8a407"; }
    else { pigEl.innerText = "😭🐷"; commentEl.innerText = "Báo động! Bạn đang chi tiêu vượt hạn mức nguy hiểm."; commentEl.style.color = "#de0000"; }
}

function loadAnalysis(){
    const filterDate = document.getElementById('filterDate').value;
    if(!filterDate) return;
    const dateParts = filterDate.split('-');
    const year = dateParts[0];
    const month = dateParts[1];

    fetch('/insights/analyze',{
        method:'POST',
        headers:{
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type':'application/json'
        },
        body: JSON.stringify({month, year})
    })
    .then(res => res.json())
    .then(data => {
        const prediction = Number(data.prediction || 0);
        document.getElementById('prediction').innerText = prediction.toLocaleString('vi-VN') + ' đ';
        updateHealthWidget(data.health_score ?? 0);
        renderChart(data.charts?.trend?.labels || [], data.charts?.trend?.values || [], prediction);
        document.getElementById('chartExplain').innerText = explainTrend(data.charts?.trend?.values || [], prediction);

        let html = '';
        const isUserPremium = {{ auth()->check() && auth()->user()->is_premium ? 'true' : 'false' }};

        if(Array.isArray(data.insights)){
data.insights.forEach(i => {
    let type = i.type || 'pattern';
    let isLocked = (i.is_premium && !isUserPremium);

    // Ánh xạ loại sang tiếng Việt
    const typeLabels = {
        'pattern': 'Thói quen',
        'warning': 'Cảnh báo',
        'prediction': 'Dự báo',
        'recommendation': 'Gợi ý'
    };
    let vietnameseLabel = typeLabels[type] || type.toUpperCase();

    // Cấu hình icon
    let icon = isLocked ? '🔒' : '✨';
    if(type === 'warning') icon = isLocked ? '🔒' : '⚠️';
    if(type === 'prediction') icon = isLocked ? '🔒' : '📈';
    if(type === 'recommendation') icon = isLocked ? '🔒' : '💡';

    let contentHtml = isLocked 
        ? `<div style="filter: blur(5px); pointer-events: none; user-select: none;">Nội dung dành riêng cho tài khoản Premium. Vui lòng nâng cấp để xem chi tiết!</div>` 
        : `<p>${i.content}</p>`;

    let actionButton;
   if (isLocked) {
    // Sửa href thành route thanh toán của bạn
    actionButton = `<a href="{{ route('payment.checkout') }}" class="btn btn-sm btn-outline-warning w-100 rounded-pill mt-2">🔒 Nâng cấp Premium</a>`;
    } else {
        // Xử lý an toàn: Thay thế dấu nháy kép, dấu nháy đơn và loại bỏ xuống dòng
        let safeContent = i.content
            .replace(/"/g, '&quot;')
            .replace(/'/g, "\\'")
            .replace(/\r?\n|\r/g, ' '); 
            
        actionButton = `<button onclick="openChat('${safeContent}')" class="btn btn-sm btn-ask-ai rounded-pill px-4">Hỏi thêm trợ lý</button>`;
    }

    html += `
    <div class="col-md-6 mb-3">
        <div class="card insight-card type-${type}">
            <div class="card-body p-4">
                <span class="type-label">${icon} ${vietnameseLabel}</span>
                ${contentHtml}
                ${actionButton}
            </div>
        </div>
    </div>`;
});
        }
        document.getElementById('insights').innerHTML = html;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart !== 'undefined') loadAnalysis();
    else window.addEventListener('load', loadAnalysis);
});

function openChat(content){
    currentInsightContent = content;
    document.getElementById('chat-box').style.display = 'block';
    document.getElementById('chat-content').innerHTML = `
        <div class="small text-muted mb-2 text-center" style="border-bottom: 1px solid #eee;">
            Đang hỏi về: <i>"${content}"</i>
        </div>`;
}

// Hàm xử lý tải báo cáo PDF
function downloadReport() {
    const filterDate = document.getElementById('filterDate').value;
    if (!filterDate) {
        alert("Vui lòng chọn tháng cần tải báo cáo!");
        return;
    }

    // Tách năm và tháng từ input kiểu 'month' (YYYY-MM)
    const dateParts = filterDate.split('-');
    const year = dateParts[0];
    const month = dateParts[1];

    // Chuyển hướng tới route tải PDF đã khai báo
    window.location.href = `/reports/download?month=${month}&year=${year}`;
}

async function sendChat(){
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if(!msg) return;
    const box = document.getElementById('chat-content');
    box.innerHTML += `<div class="message-user">${msg}</div>`;
    input.value = '';
    box.scrollTop = box.scrollHeight;

    const res = await fetch('/insights/chat-proxy',{
        method:'POST',
        headers:{
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type':'application/json'
        },
        body: JSON.stringify({ message: msg, context: currentInsightContent })
    });
    const data = await res.json();
    box.innerHTML += `<div class="message-ai"><b>AI:</b> ${data.answer}</div>`;
    box.scrollTop = box.scrollHeight;
}
document.getElementById('btn-send').addEventListener('click', sendChat);
document.getElementById('chat-input').addEventListener('keypress',e=>{ if(e.key === 'Enter') sendChat(); });

</script>
@endpush
<div class="modal fade" id="compareModal" tabindex="-1" aria-labelledby="compareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 25px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="compareModalLabel" style="font-family: 'Itim'; font-size: 1.6rem; color: #a0073f;">
                    📊 Kết quả so sánh chi tiêu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
    <div style="height: 300px; width: 100%;">
        <canvas id="compareChart"></canvas>
    </div>
    
    <div id="compareInsight"></div>
    
    <p class="text-center text-muted small mt-3">
        * So sánh chi tiêu theo danh mục giữa hai tháng.
    </p>
</div>
        </div>
    </div>
</div>
</x-app-layout>