<?php

namespace App\Services;

use PayOS\PayOS;
use Exception;
use Illuminate\Support\Facades\Log;

class PayOSService
{
    protected $payos;

    public function __construct()
    {
        // Khởi tạo PayOS với các biến lấy từ file .env qua config/services.php
        $this->payos = new PayOS(
            config('services.payos.client_id'),
            config('services.payos.api_key'),
            config('services.payos.checksum_key')
        );
    }

    /**
     * Tạo link thanh toán
     * * @param array $data Dữ liệu đơn hàng: orderCode, amount, description, returnUrl, cancelUrl
     * @return array|null
     */
    public function createPaymentLink(array $data)
    {
        try {
            // Gọi thư viện PayOS để tạo link
            $response = $this->payos->createPaymentLink($data);
            return $response;
        } catch (Exception $e) {
            // Ghi log nếu có lỗi để dễ debug
            Log::error("Lỗi tạo link thanh toán PayOS: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra thông tin thanh toán (Webhook hoặc khi khách quay lại web)
     * * @param int|string $orderId
     */
    public function getPaymentInfo($orderId)
    {
        try {
            return $this->payos->getPaymentLinkInformation($orderId);
        } catch (Exception $e) {
            Log::error("Lỗi lấy thông tin thanh toán: " . $e->getMessage());
            return null;
        }
    }
}