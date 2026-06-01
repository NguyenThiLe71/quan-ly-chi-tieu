<x-app-layout>

<div class="container py-4">

    <div class="w-full flex justify-center mb-8 px-4">
        <h3 class="flex items-center gap-3 text-center" 
            style="font-family: 'Itim', cursive; font-size: 2.2rem; display: flex; align-items: center;">
            
            <span class="flex-shrink-0" style="filter: drop-shadow(0 2px 4px rgba(236, 72, 153, 0.3));">
                📊
            </span>

            <span class="title-gradient" 
                  style="font-weight: 900; 
                         letter-spacing: 0.5px;
                         text-shadow: 0.5px 0px 0px #e8117d, -0.5px 0px 0px #f756a6;
                         display: flex; align-items: center;">
                Dòng chảy chi tiêu
            </span>
        </h3>
    </div>

    {{-- FILTER --}}
    <div class="d-flex justify-content-center mb-5 animate-item">
        <div class="filter-card-luxury" style="max-width: 800px; width: 100%;">
            <div class="flex-grow-1">
                <select id="month" class="form-control input-pink-style" aria-label="Chọn tháng thống kê">
                    <option value="">Cả năm</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">Tháng {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div style="max-width: 120px;">
                <input type="number" id="year" value="{{ date('Y') }}" class="form-control input-pink-style" aria-label="Nhập năm thống kê">
            </div>

            <button onclick="loadData()" class="btn btn-gradient fw-bold px-4" style="border-radius: 10px; height: 42px;">
                Lọc
            </button>
        </div>
    </div>

    {{-- THẺ THỐNG KÊ TỔNG --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4 animate-item">
            <div class="polaroid-card p-4 text-center">
                <h6 class="fw-bold mb-2" style="font-size: 16px;">🌸 Thu nhập</h6>
                <h3 id="income" class="fw-bold text-success" style="font-size: 24px;">0</h3>
            </div>
        </div>

        <div class="col-md-4 animate-item">
            <div class="polaroid-card p-4 text-center">
                <h6 class="fw-bold mb-2" style="font-size: 16px;">🌺 Chi tiêu</h6>
                <h3 id="expense" class="fw-bold text-danger" style="font-size: 24px;">0</h3>
            </div>
        </div>

        <div class="col-md-4 animate-item">
            <div class="polaroid-card p-4 text-center">
                <h6 class="fw-bold mb-2" style="font-size: 16px;">🌷 Số dư</h6>
                <h3 id="saving" class="fw-bold text-balance" style="font-size: 24px;">0</h3>
            </div>
        </div>
    </div>

    {{-- BIỂU ĐỒ THU CHI --}}
    <div class="animate-item">
        <div class="polaroid-card no-corner-shapes p-4">
            <canvas id="chart" aria-label="Biểu đồ thống kê thu chi" role="img"></canvas>
        </div>
    </div>

    {{-- 🔥 ĐÃ NÂNG CẤP: TIẾN ĐỘ TIẾT KIỆM CHI TIẾT --}}
    <div class="animate-item mt-4">
        <div class="polaroid-card p-4 text-center saving-progress-container">
            <h6 class="mb-4" style="font-family: 'Itim', cursive; font-weight: 900; font-size: 1.2rem; text-shadow: 0.3px 0px 0px currentColor;">
                🎯 Tiến độ tiết kiệm
            </h6>
            
            <div class="d-flex align-items-center justify-content-center flex-wrap gap-5 my-2">
                {{-- Vòng tròn Chart bọc chữ ở giữa --}}
                <div class="chart-wrapper">
                    <canvas id="savingChart" aria-label="Biểu đồ tiến độ tiết kiệm" role="img"></canvas>
                    <div class="chart-center-text">
                        <span id="chartCenterPercent" class="percent fw-bold">0%</span>
                        <span class="label text-muted">Đạt được</span>
                    </div>
                </div>

                {{-- Hộp thông tin chi tiết mềm mại kế bên --}}
                <div class="saving-details-box text-start">
                    <div class="detail-item mb-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="indicator-dot" style="background-color: #d21473;"></span>
                            <span class="detail-title">Đã tích lũy:</span>
                        </div>
                        <p id="detailSaved" class="detail-money text-pink mb-0">0 ₫</p>
                    </div>

                    <div class="detail-item mb-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="indicator-dot" style="background-color: #fbcfe8;"></span>
                            <span class="detail-title">Mục tiêu cần đạt:</span>
                        </div>
                        <p id="detailTarget" class="detail-money text-dark mb-0">0 ₫</p>
                    </div>

                    <div class="border-top pt-2 mt-2 border-dashed">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <span class="text-secondary small fw-bold">Còn thiếu:</span>
                            <span id="detailRemaining" class="badge bg-soft-warning fw-bold">0 ₫ 🎯</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dòng trạng thái kute chuyển động nhịp tim bên dưới --}}
            <div class="mt-4">
                <span id="savingPercent" class="status-pill animate-pulse">0% hoàn thành 💖</span>
            </div>
        </div>
    </div>

</div>

<style>
.title-gradient {
    background: linear-gradient(135deg, #ec4899, #fbcfe8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.filter-card-luxury {
    background: linear-gradient(135deg, #fff0f6 0%, #f5f3ff 100%) !important;
    border-radius: 18px !important;
    padding: 12px 20px !important;
    display: flex;
    align-items: center;
    gap: 10px;
}

.input-pink-style {
    background-color: #ffffff !important;
    border: 1.2px solid #f9a8d4 !important;
    border-radius: 10px !important;
    height: 42px !important;
}
.btn-gradient {
    background: linear-gradient(45deg, #d946ef, #ec4899);
    color: white;
    border: none;
}

.polaroid-card {
    background: linear-gradient(145deg, #ffffff, #fff1f5);
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.1);
    border: 1px solid #fbcfe8;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}
.polaroid-card::before,
.polaroid-card::after {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    /* ✅ CHỈNH SỬA TẠI ĐÂY: Thay đổi độ mờ từ 0.37 lên 0.9 để màu đậm và rõ ràng hơn */
    background: rgba(255, 102, 191, 0.9); /* Đã làm đậm màu hồng cánh sen */
    border-radius: 50%;
    z-index: 0;
}
.polaroid-card::before { top: -40px; left: -40px; }
.polaroid-card::after { bottom: -40px; right: -40px; }

.polaroid-card * { position: relative; z-index: 1; }

.polaroid-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 35px rgba(236, 72, 153, 0.2);
}

.btn-gradient:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
}

.polaroid-card.no-corner-shapes::before,
.polaroid-card.no-corner-shapes::after {
    display: none !important;
    content: none !important;
}

.animate-item {
    opacity: 0;
    transform: translateY(30px);
    will-change: opacity, transform;
}
.show-now {
    opacity: 1;
    transform: translateY(0);
    transition: 0.6s ease;
}
body { background: #fdf2f8; }

.text-balance { color: #a855f7; }

/* 🛠 CSS BỔ SUNG CHO KHỐI TIẾT KIỆM NÂNG CẤP */
.saving-progress-container {
    font-family: 'Itim', cursive;
}
.chart-wrapper {
    position: relative;
    width: 200px;
    height: 200px;
}
.chart-center-text {
    position: absolute;
    top: 45%; /* Căn chỉnh đẩy tâm vừa vặn vòng tròn donut */
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    display: flex;
    flex-direction: column;
    pointer-events: none;
}
.chart-center-text .percent {
    font-size: 24px;
    color: #d21473;
    line-height: 1;
}
.chart-center-text .label {
    font-size: 11px;
    font-weight: bold;
    margin-top: 2px;
}
.saving-details-box {
    background: #ffffff;
    border: 1.5px solid #fbcfe8;
    border-radius: 20px;
    padding: 18px;
    min-width: 250px;
    box-shadow: 0 4px 12px rgba(236, 72, 153, 0.03);
}
.indicator-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    display: inline-block;
}
.detail-title {
    font-size: 13px; color: #64748b; font-weight: bold;
}
.detail-money {
    font-size: 18px; font-weight: 900; padding-left: 18px;
}
.text-pink { color: #d21473; }
.border-dashed { border-top: 1.5px dashed #f1f5f9 !important; }
.bg-soft-warning {
    background-color: #fef3c7; color: #d97706;
    border-radius: 8px; padding: 4px 8px; font-size: 13px;
}
.status-pill {
    background: #fff1f2; color: #db2777;
    padding: 6px 18px; border-radius: 20px;
    font-size: 14px; font-weight: bold;
    border: 1px solid #fecaca; display: inline-block;
}
.animate-pulse { animation: soft-pulse 2s infinite; }
@keyframes soft-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.03); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

<script>
let chart;
let savingChart;

function loadData() {
    const month = document.getElementById('month').value;
    const year = document.getElementById('year').value;

    fetch(`/statistics/data?year=${year}&month=${month}`)
        .then(res => res.json())
        .then(res => {
            const data = res.chartData;
            const saving = res.saving;

            let labels = [];
            let income = [];
            let expense = [];

            let totalIncome = 0;
            let totalExpense = 0;

            data.forEach(item => {
                let incomeVal = parseFloat(item.total_income) || 0;
                let expenseVal = parseFloat(item.total_expense) || 0;

                labels.push(`Tháng ${item.month}`);
                income.push(incomeVal);
                expense.push(expenseVal);

                totalIncome += incomeVal;
                totalExpense += expenseVal;
            });

            // GÁN DỮ LIỆU CÁC CARD TRÊN
            document.getElementById('income').innerText = totalIncome.toLocaleString('vi-VN') + ' ₫';
            document.getElementById('expense').innerText = totalExpense.toLocaleString('vi-VN') + ' ₫';
            document.getElementById('saving').innerText = (totalIncome - totalExpense).toLocaleString('vi-VN') + ' ₫';

            // CHART THU CHI
            if (chart) chart.destroy();
            const chartType = month ? 'bar' : 'line';

            chart = new Chart(document.getElementById('chart'), {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Thu nhập',
                            data: income,
                            borderColor: '#22c55e',
                            backgroundColor: '#6ef1a5',
                            tension: month ? 0 : 0.4
                        },
                        {
                            label: 'Chi tiêu',
                            data: expense,
                            borderColor: '#e42d2d',
                            backgroundColor: '#fab1b1',
                            tension: month ? 0 : 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });

            // 🔥 XỬ LÝ DỮ LIỆU ĐÃ NÂNG CẤP CHO CHART TIẾT KIỆM VÀ KHỐI THÔNG TIN CHI TIẾT
            const saved = saving.saved || 0;
            const target = saving.target || 0;
            const remaining = saving.remaining || 0;
            const percent = saving.percentage || 0;

            // 1. Đổ dữ liệu text ra hộp Soft UI chi tiết bên cạnh
            document.getElementById('detailSaved').innerText = saved.toLocaleString('vi-VN') + ' ₫';
            document.getElementById('detailTarget').innerText = target.toLocaleString('vi-VN') + ' ₫';
            document.getElementById('detailRemaining').innerText = remaining.toLocaleString('vi-VN') + ' ₫ ';
            
            // 2. Điền text phần trăm vào tâm vòng tròn và thanh trạng thái dưới cùng
            document.getElementById('chartCenterPercent').innerText = percent + '%';
            document.getElementById('savingPercent').innerText = `✨ Cố lên! Bạn đã hoàn thành ${percent}% chặng đường rồi 💖`;

            // 3. Render biểu đồ tròn (Donut Chart)
            if (savingChart) savingChart.destroy();
            savingChart = new Chart(document.getElementById('savingChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Đã tiết kiệm', 'Còn lại'],
                    datasets: [{
                        data: [saved, remaining],
                        backgroundColor: ['#d21473', '#fbcfe8'],
                        borderWidth: 0 // Tắt viền để vòng tròn trông mềm mịn chuẩn Soft UI
                    }]
                },
                options: {
                    cutout: '75%', // Tăng độ rỗng ruột lên xíu để chữ hiển thị thoáng đãng
                    responsive: true,
                    plugins: {
                        legend: { display: false } // Tắt chú thích mặc định của ChartJS vì ta đã tự thiết kế hộp text xịn bên cạnh
                    }
                }
            });
        });
}

// Animation khởi tạo ban đầu
document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.animate-item');
    items.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('show-now');
        }, index * 100);
    });

    if (typeof Chart !== 'undefined') {
        loadData();
    } else {
        window.addEventListener('load', loadData);
    }
});
</script>

</x-app-layout>