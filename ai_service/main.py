import os
from dotenv import load_dotenv
from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
import numpy as np
from sklearn.cluster import KMeans
from sklearn.linear_model import LinearRegression
from google import genai
from google.genai import types

# Tải .env một lần duy nhất
load_dotenv()
API_KEY = os.getenv("GEMINI_API_KEY")

app = FastAPI()

# FIX LỖI KẾT NỐI & CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Biến toàn cục để lưu kết quả phân tích tạm thời cho Chat
analysis_storage = {}

@app.post("/analyze")
async def analyze(data: dict):
    transactions = data.get("transactions", [])
    current_user_id = data.get("user_id")
    month = data.get("month", "này")
    year = data.get("year", "")

    # FIX: tránh crash nếu user_id = None
    if current_user_id is None:
        return {
            "prediction": 0,
            "health_score": 50,
            "insights": [{
                "type": "pattern",
                "content": "Không xác định được người dùng."
            }]
        }

    # Lọc: Chỉ lấy chi tiêu (expense), đúng User, đồng thời tính toán danh mục chi tiêu
    amounts = []
    category_map = {}  # 🛒 Dùng để gom nhóm chi tiêu theo danh mục cho thẻ THÓI QUEN
    all_expenses = [] # <--- GIỮ NGUYÊN CODE CỦA ÔNG, CHỈ CHÈN THÊM DÒNG NÀY

    for t in transactions:
        try:
            if (
                str(t.get("type")).lower() == "expense" and
                int(t.get("user_id", 0)) == int(current_user_id)
            ):
                amt = float(t.get("amount", 0))
                amounts.append(amt)
                
                # Đọc tên danh mục từ object lồng `category` do Laravel Eager Loading truyền sang
                cat_name = "Chi tiêu khác"
                if t.get("category") and isinstance(t.get("category"), dict):
                    cat_name = t.get("category").get("name", "Chi tiêu khác")
                
                category_map[cat_name] = category_map.get(cat_name, 0) + amt
                all_expenses.append({"amount": amt, "category": cat_name}) # <--- CHÈN THÊM
        except:
            continue

    if not amounts:
        return {
            "prediction": 0,
            "health_score": 50,
            "insights": [{
                "type": "pattern",
                "content": f"Không tìm thấy khoản chi tiêu nào trong tháng {month}."
            }]
        }

    # ======================
    # PHÂN TÍCH DỮ LIỆU
    # ======================
    avg = sum(amounts) / len(amounts)
    high_spending = max(amounts)
    days = np.array(list(range(len(amounts)))).reshape(-1, 1)
    
    # <--- LẤY TÊN DANH MỤC CỦA KHOẢN CHI LỚN NHẤT
    max_cat = next((item["category"] for item in all_expenses if item["amount"] == high_spending), "Chi tiêu khác")

    # 📈 Chạy mô hình hồi quy tuyến tính xác định hệ số góc (Xu hướng tăng/giảm)
    slope = 0
    if len(amounts) < 2:
        next_day_pred = int(amounts[0])
    else:
        model = LinearRegression().fit(days, amounts)
        next_day_pred = int(model.predict([[len(amounts)]])[0])
        slope = model.coef_[0]

        if next_day_pred < 0:
            next_day_pred = 0

    # ==========================================
    # 🐷 THUẬT TOÁN TÍNH ĐIỂM SỨC KHỎE TÀI CHÍNH
    # ==========================================
    health_score = 100
    
    # 1. Trừ điểm dựa trên mức độ chi tiêu đột biến so với trung bình (Tối đa trừ 25 điểm)
    if high_spending > avg * 1.5:
        penalty_outlier = min(25, int((high_spending / avg) * 5))
        health_score -= penalty_outlier

    # 2. Trừ điểm dựa trên hệ số dốc xu hướng (Hồi quy tuyến tính tăng = ví tiền nguy hiểm)
    if slope > 0:
        penalty_trend = min(20, int(slope / (avg + 1) * 10))
        health_score -= penalty_trend

    # 3. Trừ điểm nếu dự đoán chi tiêu lần tới vượt ngưỡng chi tiêu trung bình hiện tại
    if next_day_pred > avg:
        health_score -= 10

    # Đảm bảo điểm số luôn nằm gọn trong khung chuẩn 10 - 100
    health_score = max(10, min(100, health_score))

    # Lấy 5 giao dịch gần nhất
    recent_5 = all_expenses[-5:]

    # Lưu thông tin phân tích vào bộ nhớ tạm phục vụ ngữ cảnh phản hồi Chat Proxy
    analysis_storage[str(current_user_id)] = {
        "avg": avg,
        "max": high_spending,
        "max_cat": max_cat,
        "prediction": next_day_pred,
        "health_score": health_score,
        "month": month,
        "recent_5": recent_5  # <--- CHÈN THÊM ĐỂ AI CÓ DỮ LIỆU CỤ THỂ
    }

    # ==========================================
    # 🔥 INSIGHTS (ĐÃ SỬA MỤC THÓI QUEN THEO DANH MỤC AN TOÀN VIEW)
    # ==========================================
    # Xác định nội dung cho thẻ THÓI QUEN (pattern) sử dụng ngoặc vuông để tránh lỗi vỡ HTML onclick
    if category_map:
        top_category = max(category_map, key=category_map.get)
        top_amount = category_map[top_category]
        total_expense = sum(amounts)
        percentage = (top_amount / total_expense) * 100 if total_expense > 0 else 0
        
        pattern_text = f"Thói quen tháng này của bạn là chi nhiều nhất cho [{top_category}], chiếm {int(percentage)}% tổng chi tiêu của tháng."
    else:
        pattern_text = f"Mức chi tiêu trung bình tháng {month}: {int(avg):,} đ."

    insights = [
        {
            "type": "pattern",
            "content": pattern_text
        },
        {
            "type": "prediction",
            "content": f"Dự báo lần chi tiếp theo trong tháng: {int(next_day_pred):,} đ."
        }
    ]

    if high_spending > avg * 1.8:
        insights.append({
            "type": "warning",
            "content": f"Phát hiện khoản chi đột biến: {int(high_spending):,} đ."
        })

    # ĐÂY LÀ PHẦN XU HƯỚNG:
    if len(amounts) >= 2:
        if amounts[-1] > amounts[-2]:
            text = f"Xu hướng chi tiêu tháng {month} đang tăng. Hãy cân nhắc cắt giảm."
        else:
            text = f"Bạn đang quản lý chi tiêu tháng {month} rất tốt, xu hướng đang giảm!"
    else:
        if next_day_pred > avg:
            text = f"Xu hướng chi tiêu tháng {month} có dấu hiệu tăng nhẹ."
        else:
            text = f"Chi tiêu tháng {month} hiện tại đang ở mức ổn định."

    insights.append({
        "type": "recommendation",
        "content": text
    })

    # ======================
    # CHART DATA
    # ======================
    trend_labels = [f"Lần {i+1}" for i in range(len(amounts))]
    trend_values = amounts

    low_count = len([x for x in amounts if x < avg])
    mid_count = len([x for x in amounts if avg <= x < avg * 1.5])
    high_count = len([x for x in amounts if x >= avg * 1.5])

    compare_labels = ["Chi TB", "AI dự đoán"]
    compare_values = [int(avg), int(next_day_pred)]

    return {
        "prediction": next_day_pred,
        "health_score": health_score,
        "insights": insights,
        "charts": {
            "trend": {
                "labels": trend_labels,
                "values": trend_values
            },
            "level": {
                "labels": ["Thấp", "Trung bình", "Cao"],
                "values": [low_count, mid_count, high_count]
            },
            "compare": {
                "labels": compare_labels,
                "values": compare_values
            }
        }
    }
@app.post("/compare-insight")
async def get_compare_insight(data: dict):
    # 1. Lấy dữ liệu với giá trị mặc định là dictionary trống
    m1_data = data.get("month1") or {}
    m2_data = data.get("month2") or {}
    m1_label = data.get("m1_label", "Tháng trước")
    m2_label = data.get("m2_label", "Tháng này")

    # Hàm hỗ trợ định dạng dữ liệu cho AI
    def format_data(d):
        if not d: return "Không có dữ liệu"
        return "\n".join([f"- {cat}: {int(amt):,}đ" for cat, amt in d.items()])

    # 2. Xử lý logic AI với prompt đã được làm sạch
    prompt = f"""
    Hãy đóng vai trợ lý tài chính 🐷. 
    Người dùng đang so sánh chi tiêu giữa:
    {m1_label}:
    {format_data(m1_data)}
    
    {m2_label}:
    {format_data(m2_data)}
    
    Hãy viết 1 đoạn văn ngắn (tối đa 3 câu) so sánh. 
    - Nêu rõ danh mục nào tăng/giảm mạnh nhất.
    - Dùng icon dễ thương, phong cách Itim. 
    - Đưa ra 1 lời khuyên tiết kiệm cụ thể cho {m2_label}.
    """
    
    insight_text = "🐷 Hiện tại AI đang bận chút, nhưng bạn có thể nhìn vào biểu đồ để thấy sự thay đổi nhé!"
    
    try:
        client = genai.Client(api_key=API_KEY)
        
        # --- LOGIC TỰ DÒ TÌM MODEL ĐỂ TRÁNH LỖI 404 ---
        models_list = list(client.models.list())
        # Lấy danh sách tên model khả dụng
        gemini_models = [m.name for m in models_list if "gemini" in m.name]
        
        if not gemini_models:
            raise Exception("Không tìm thấy model nào khả dụng trên tài khoản của bạn!")
            
        selected_model = gemini_models[0] # Chọn model đầu tiên tìm được
        print(f"DEBUG: Đang sử dụng model: {selected_model}")
        
        response = client.models.generate_content(
            model=selected_model, 
            contents=prompt
        )
        insight_text = response.text
        
    except Exception as e:
        print(f"LỖI AI TỰ ĐỘNG DÒ: {e}")

    # 3. TRẢ VỀ DỮ LIỆU ĐÃ LÀM SẠCH
    return {
        "month1": m1_data,
        "month2": m2_data,
        "insight": insight_text
    }

@app.post("/chat")
def chat(data: dict):
    message = data.get("message", "")
    context = data.get("context", "")
    user_name = data.get("user_name", "Người dùng")
    current_user_id = data.get("user_id")

    client = genai.Client(api_key=API_KEY)

    # 3. Lấy dữ liệu phân tích số liệu thực tế từ bộ nhớ tạm
    user_storage = analysis_storage.get(str(current_user_id), {})
    
    # Tạo chuỗi danh sách 5 giao dịch gần nhất
    recent_txs = user_storage.get('recent_5', [])
    recent_str = "\n".join([f"- {int(t['amount']):,} đ mục {t['category']}" for t in recent_txs])

    financial_context = ""
    if user_storage:
        financial_context = (
            f"- Chi tiêu trung bình hiện tại: {int(user_storage.get('avg', 0)):,} đ.\n"
            f"- Khoản chi đột biến lớn nhất: {int(user_storage.get('max', 0)):,} đ thuộc mục '{user_storage.get('max_cat', 'chưa rõ')}'.\n"
            f"- 5 giao dịch gần nhất:\n{recent_str}\n" # <--- AI ĐÃ CÓ BẰNG CHỨNG THỰC TẾ
            f"- Dự báo số tiền chi lần tiếp theo: {int(user_storage.get('prediction', 0)):,} đ.\n"
            f"- Điểm sức khỏe tài chính: {user_storage.get('health_score', 50)}/100.\n"
        )

    # 4. ĐỊNH HÌNH LẠI CHỈ THỊ ĐỂ AI TRẢ LỜI NGẮN GỌN (TỐI ĐA 3 CÂU)
    system_instruction = (
        "Bạn là trợ lý tài chính thông minh. Quy tắc trả lời: "
        "1. Dựa vào 5 giao dịch gần nhất để giải thích cụ thể các thắc mắc của người dùng. "
        "2. Đưa ra 1 nhận xét ngắn về sự đột biến và 1 gợi ý hành động cụ thể. "
        "3. Tối đa 3 câu, không liệt kê, không giáo điều."
    )

    full_prompt = (
        f"Ngữ cảnh: '{context}'. Dữ liệu: {financial_context}. Câu hỏi: '{message}'"
    )

    try:
        # 6. TỰ ĐỘNG DÒ TÌM MODEL VÀ DỰ PHÒNG LỖI 503
        models_list = list(client.models.list())
        gemini_models = [m.name for m in models_list if "gemini" in m.name]
        
        response = None
        for model_name in gemini_models[:3]:
            try:
                response = client.models.generate_content(
                    model=model_name,
                    contents=full_prompt,
                    config=types.GenerateContentConfig(
                        system_instruction=system_instruction,
                        temperature=0.6,
                    ),
                )
                break
            except:
                continue
        
        if response:
            return {"answer": response.text}
        else:
            return {"answer": "Hệ thống AI đang tạm bận, bạn thử lại sau một chút nhé!"}
        
    except Exception as e:
        print(f"DEBUG LỖI: {e}") 
        return {"answer": "Bạn ơi, mình đang gặp chút trục trặc, bạn hỏi lại sau nhé!"}