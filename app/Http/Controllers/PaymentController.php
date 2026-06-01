<?php

namespace App\Http\Controllers;

use App\Services\PayOSService;
use Illuminate\Http\Request;
use App\Models\Order; 


class PaymentController extends Controller
{
    protected $payosService;

    public function __construct(PayOSService $payosService)
    {
        $this->payosService = $payosService;
    }

    public function checkout()
    {
        // 1. Tạo mã đơn hàng
        $orderCode = intval(substr(strval(microtime(true) * 10000), -6));

        // 2. LƯU VÀO DATABASE ĐỂ WEBHOOK BIẾT ĐƯỜNG CẬP NHẬT
        Order::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'order_code' => $orderCode,
            'status' => 'PENDING'
        ]);

        // 3. Tạo thông tin thanh toán
        $data = [
            "orderCode" => $orderCode,
            "amount" => 2000, 
            "description" => "Nap Premium 1 thang", // Đổi tên rõ ràng để dễ quản lý
            "returnUrl" => "http://localhost:8001/dashboard", 
            "cancelUrl" => "http://localhost:8001/dashboard", 
        ];

        $response = $this->payosService->createPaymentLink($data);

        if ($response && isset($response['checkoutUrl'])) {
            return redirect($response['checkoutUrl']);
        }

        return back()->with('error', 'Không thể tạo link thanh toán!');
    }
}