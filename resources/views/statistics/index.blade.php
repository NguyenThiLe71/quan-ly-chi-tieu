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
                     /* Tạo độ dày và viền mịn cho nét chữ Itim */
                     text-shadow: 0.5px 0px 0px #e8117d, -0.5px 0px 0px #f756a6;
                     display: flex; align-items: center;">
            Dòng chảy chi tiêu
        </span>
    </h3>
</div>

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

    <div class="row g-4 mb-5">

    <div class="col-md-4 animate-item">
        <div class="polaroid-card p-4 text-center">
            <h6 class="fw-bold mb-2" style="font-size: 16px;">
                🌸 Thu nhập
            </h6>
            <h3 id="income" class="fw-bold text-success" style="font-size: 24px;">
                0
            </h3>
        </div>
    </div>

    <div class="col-md-4 animate-item">
        <div class="polaroid-card p-4 text-center">
            <h6 class="fw-bold mb-2" style="font-size: 16px;">
                🌺 Chi tiêu
            </h6>
            <h3 id="expense" class="fw-bold text-danger" style="font-size: 24px;">
                0
            </h3>
        </div>
    </div>

    <div class="col-md-4 animate-item">
        <div class="polaroid-card p-4 text-center">
            <h6 class="fw-bold mb-2" style="font-size: 16px;">
                🌷 Số dư
            </h6>
            <h3 id="saving" class="fw-bold text-balance" style="font-size: 24px;">
                0
            </h3>
        </div>
    </div>

</div>
    <div class="animate-item">
    <div class="polaroid-card no-corner-shapes p-4">
        <canvas id="chart" aria-label="Biểu đồ thống kê thu chi" role="img"></canvas>
    </div>
</div>

    <div class="animate-item mt-4">
        <div class="polaroid-card p-4 text-center">
           <h6 class="mb-3" style="font-family: 'Itim', cursive; font-weight: 900; font-size: 1.1rem; text-shadow: 0.3px 0px 0px currentColor;">
    Tiến độ tiết kiệm
</h6>
            <canvas id="savingChart" style="max-width: 250px; margin: auto;" aria-label="Biểu đồ tiến độ tiết kiệm" role="img"></canvas>
            <p id="savingPercent" class="mt-3 fw-bold text-success"></p>
        </div>
    </div>

</div>

<style>
.title-gradient {
    background: linear-gradient(135deg, #ec4899, #fbcfe8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Khung bao ngoài bo vuông nhẹ - Đúng màu image_6ece26 */
.filter-card-luxury {
   background: linear-gradient(135deg, #fff0f6 0%, #f5f3ff 100%) !important;
    border-radius: 18px !important; /* Bo góc vuông nhẹ nhàng, không tròn xoe */
    padding: 12px 20px !important;
    
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Giữ ô input trắng tinh khôi */
.input-pink-style {
    background-color: #ffffff !important;
    border: 1.2px solid #f9a8d4 !important;
    border-radius: 10px !important; /* Bo góc nhẹ đồng bộ với khung ngoài */
    height: 42px !important;
}
.btn-gradient {
    background: linear-gradient(45deg, #d946ef, #ec4899);
    color: white;
    border: none;
}

.polaroid-card {
    background: linear-gradient(145deg, #ffffff, #fff1f5);
    border-radius: 30px; /* bo nhiều hơn */
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.1);
    border: 2px solid #f9d9eb;
    position: relative;
    overflow: hidden;
}
.polaroid-card::before,
.polaroid-card::after {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    background: rgba(238, 55, 162, 0.37);
    border-radius: 50%;
    z-index: 0;
}

/* cánh trái */
.polaroid-card::before {
    top: -40px;
    left: -40px;
}

/* cánh phải */
.polaroid-card::after {
    bottom: -40px;
    right: -40px;
}
.polaroid-card * {
    position: relative;
    z-index: 1;
}
.polaroid-card:hover {
    transform: translateY(-5px);
}

.animate-item {
    opacity: 0;
    transform: translateY(30px);
    will-change: opacity, transform; /* Gợi ý GPU tối ưu tốc độ quét điểm */
}
.show-now {
    opacity: 1;
    transform: translateY(0);
    transition: 0.6s ease;
}
body {
    background: #fdf2f8;
}

/* TEXT COLORS */
.text-income {
    color: #ec4899; /* hồng đậm */
}

.text-expense {
    color: #fb7185; /* hồng đỏ */
}

.text-balance {
    color: #a855f7; /* tím hồng */
}

/* CARD STYLE */
.polaroid-card {
    background: linear-gradient(145deg, #ffffff, #fff1f5);
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.1);
    transition: all 0.3s ease;
    border: 1px solid #fbcfe8;
}

.polaroid-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 35px rgba(236, 72, 153, 0.2);
}

.btn-gradient:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
}
/* Tắt hiệu ứng hình tròn góc cho thẻ có class no-corner-shapes */
.polaroid-card.no-corner-shapes::before,
.polaroid-card.no-corner-shapes::after {
    display: none !important;
    content: none !important;
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

            // CARDS
            document.getElementById('income').innerText =
                totalIncome.toLocaleString('vi-VN') + ' ₫';

            document.getElementById('expense').innerText =
                totalExpense.toLocaleString('vi-VN') + ' ₫';

            document.getElementById('saving').innerText =
                (totalIncome - totalExpense).toLocaleString('vi-VN') + ' ₫';

            // 🔥 CHART THU CHI
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
        borderColor: '#22c55e',        // xanh dịu
        backgroundColor: '#6ef1a5',    // xanh pastel
        tension: month ? 0 : 0.4
    },
    {
        label: 'Chi tiêu',
        data: expense,
        borderColor: '#e42d2d',        // đỏ dịu
        backgroundColor: '#fab1b1',    // đỏ pastel
        tension: month ? 0 : 0.4
    }
]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // 🔥 CHART TIẾT KIỆM
            const saved = saving.saved || 0;
            const target = saving.target || 0;

            if (savingChart) savingChart.destroy();

            savingChart = new Chart(document.getElementById('savingChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Đã tiết kiệm', 'Còn lại'],
                    datasets: [{
                        data: [saved, Math.max(target - saved, 0)],
                        backgroundColor: ['#d21473', '#fbcfe8']
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // % tiết kiệm
            const percent = target > 0 ? Math.round((saved / target) * 100) : 0;
            document.getElementById('savingPercent').innerText =
    percent + '% hoàn thành 💖';
        });
}

// animation
document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.animate-item');
    items.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('show-now');
        }, index * 100);
    });

    // Chờ Chart.js tải xong hoàn toàn do dùng thuộc tính defer
    if (typeof Chart !== 'undefined') {
        loadData();
    } else {
        window.addEventListener('load', loadData);
    }
});
</script>

</x-app-layout>