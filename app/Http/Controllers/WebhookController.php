<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        
        // Kiểm tra xem đơn hàng đã được thanh toán chưa
        if (isset($data['success']) && $data['success'] == true && $data['data']['status'] == 'PAID') {
            $orderCode = $data['data']['orderCode'];
            
            // Tìm đơn hàng trong DB
            $order = Order::where('order_code', $orderCode)->where('status', 'PENDING')->first();
            
            if ($order) {
                // 1. Cập nhật trạng thái đơn hàng
                $order->status = 'PAID';
                $order->save();
                
                // 2. Nâng cấp User
                $user = User::find($order->user_id);
                $user->is_premium = true; 
                $user->save();
            }
        }
        return response()->json(['message' => 'OK']);
    }
}