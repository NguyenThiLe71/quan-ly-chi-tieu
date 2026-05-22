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
<div class="card p-3 mb-5 shadow-sm border-0 mx-auto"
     style="max-width:520px; border-radius:20px; background:rgba(255,255,255,.7); backdrop-filter:blur(10px);">

    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <span class="fw-bold">Chọn thời gian:</span>

        {{-- Ô CHỌN THÁNG/NĂM KIỂU LỊCH --}}
        <div style="width: 200px;">
            <input 
                type="month" 
                id="filterDate" 
                value="{{ now()->format('Y-m') }}" 
                class="form-control rounded-pill px-4 border-0 shadow-sm" 
                style="background:#fff5f5; color:#FF6B6B; font-weight:bold; height: 45px;"
                aria-label="Chọn tháng năm phân tích">
        </div>

        <button onclick="loadAnalysis()"
                class="btn btn-analysis rounded-pill px-4 fw-bold shadow-sm" 
                style="height: 45px;"
                aria-label="Bấm vào đây để AI phân tích chi tiêu">
            🤖 Phân tích
        </button>
    </div>
</div>

    {{-- prediction --}}
    <div class="card luxury-gradient mb-4 shadow-sm">
        <div class="card-body text-center p-4">
            <h5 class="text-uppercase" style="font-size:.9rem;letter-spacing:2px;">
                Dự đoán chi tiêu có thể xảy ra tiếp trong tháng này
            </h5>

            <h1 id="prediction" class="display-4 fw-bold">--</h1>

            <p class="mb-0 opacity-75">
                AI dựa trên lịch sử giao dịch gần đây
            </p>
        </div>
    </div>

    {{-- chart --}}
    <div class="chart-card mb-4">
        <h5 class="fw-bold mb-3 text-center">
            📊 Xu hướng chi tiêu dùng để AI dự đoán
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
                {
                    label:'Chi tiêu thực tế',
                    data: values,
                    borderColor:'#ec4899',
                    backgroundColor:gradient,
                    fill:true,
                    tension:.4,
                    borderWidth:3,
                    pointRadius:5
                },
                {
                    label:'AI dự đoán',
                    data: futureValues.map((v,i)=> i === futureValues.length-1 ? v : null),
                    borderColor:'#ff5722',
                    pointBackgroundColor:'#ff5722',
                    pointRadius:7,
                    showLine:false
                }
            ]
        },
        options:{
            responsive:true,
            plugins:{
                legend:{ display:false }
            },
            scales:{
                y:{
                    beginAtZero:true
                }
            }
        }
    });
}

function explainTrend(values,prediction){
    if(values.length < 2){
        return "AI chưa đủ dữ liệu, dự đoán dựa trên giao dịch hiện tại.";
    }

    const avg = values.reduce((a,b)=>a+b,0)/values.length;
    const last = values[values.length-1];

    if(last > avg * 1.5){
        return "Chi tiêu gần đây tăng mạnh nên AI dự đoán tháng này sẽ cao hơn bình thường.";
    }

    if(last < avg){
        return "Chi tiêu gần đây đang giảm nên AI dự đoán mức chi khá ổn định.";
    }

    return "AI dự đoán dựa trên xu hướng chi tiêu ổn định từ các giao dịch gần đây.";
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

        document.getElementById('prediction').innerText =
            prediction.toLocaleString('vi-VN') + ' đ';

        const labels = data.charts?.trend?.labels || [];
        const values = data.charts?.trend?.values || [];

        renderChart(labels, values, prediction);

        document.getElementById('chartExplain').innerText =
            explainTrend(values, prediction);

        let html = '';

        if(Array.isArray(data.insights)){
            data.insights.forEach(i => {
                let type = i.type || 'pattern';
                let icon = '✨';
                let label = 'Phân tích';

                if(type==='warning'){ icon='⚠️'; label='Cảnh báo'; }
                if(type==='prediction'){ icon='📈'; label='Dự báo'; }
                if(type==='pattern'){ icon='🔍'; label='Thói quen'; }
                if(type==='recommendation'){ icon='💡'; label='Gợi ý'; }

                html += `
                <div class="col-md-6 mb-3">
                    <div class="card insight-card type-${type}">
                        <div class="card-body p-4">
                            <span class="type-label">${icon} ${label}</span>
                            <p>${i.content}</p>

                            <button onclick='openChat(${JSON.stringify(i.content)})'
                                class="btn btn-sm btn-ask-ai rounded-pill px-4">
                                Hỏi thêm trợ lý
                            </button>
                        </div>
                    </div>
                </div>`;
            });
        }

        document.getElementById('insights').innerHTML = html;
    });
}

// 🛠️ Tối ưu luồng khởi tạo: Tránh lỗi bất đồng bộ khi nạp thư viện bằng defer giúp tăng điểm Performance
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart !== 'undefined') {
        loadAnalysis();
    } else {
        window.addEventListener('load', loadAnalysis);
    }
});

function openChat(content){
    currentInsightContent = content;
    document.getElementById('chat-box').style.display = 'block';

    document.getElementById('chat-content').innerHTML =
        `<div class="text-muted">Đang hỏi về: "${content}"</div>`;
}

async function sendChat(){
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();

    if(!msg) return;

    const box = document.getElementById('chat-content');
    box.innerHTML += `<div class="text-end mb-2">${msg}</div>`;
    input.value = '';

    const res = await fetch('/insights/chat-proxy',{
        method:'POST',
        headers:{
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type':'application/json'
        },
        body: JSON.stringify({
            message: msg,
            context: currentInsightContent
        })
    });

    const data = await res.json();
    box.innerHTML += `<div><b>AI:</b> ${data.answer}</div>`;
}

document.getElementById('btn-send').addEventListener('click', sendChat);

document.getElementById('chat-input').addEventListener('keypress',e=>{
    if(e.key === 'Enter') sendChat();
});
</script>
@endpush
</x-app-layout>