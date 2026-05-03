<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\VNPayService;
use Illuminate\Http\Request;

class VNPayController extends Controller
{
    public function __construct(private VNPayService $vnpay) {}

    /**
     * Return URL — xử lý DB tạm (dùng khi test localhost)
     * Production: chuyển logic này sang IPN
     */
    public function return(Request $request)
    {
        $params        = $request->all();
        $invoiceNumber = $params['vnp_TxnRef'] ?? '';

        // 1. Xác thực chữ ký
        if (!$this->vnpay->verifyReturn($params)) {
            return redirect()->route('order.failed', $invoiceNumber)
                ->with('error', 'Chữ ký không hợp lệ!');
        }

        // 2. Tìm invoice
        $invoice = Invoice::with('payment')
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (!$invoice) {
            abort(404);
        }

        // 3. Chỉ cập nhật nếu chưa xử lý (idempotent)
        if ($invoice->payment?->status === 'pending') {
            if ($this->vnpay->isSuccess($params)) {
                $invoice->payment->update([
                    'status'         => 'completed',
                    'transaction_id' => $params['vnp_TransactionNo'] ?? null,
                    'paid_at'        => now(),
                    'meta'           => $params,
                ]);
                $invoice->update(['status' => 'confirmed']);
            } else {
                $invoice->payment->update([
                    'status' => 'failed',
                    'meta'   => $params,
                ]);
                $invoice->update(['status' => 'cancelled']);
            }
        }

        // 4. Redirect theo kết quả
        return $this->vnpay->isSuccess($params)
            ? redirect()->route('order.success', $invoiceNumber)
            : redirect()->route('order.failed', $invoiceNumber);
    }

    /**
     * IPN — bỏ qua khi test localhost
     * VNPay không gọi được vào localhost nên method này không chạy
     */
    public function ipn(Request $request)
    {
        // TODO: Enable khi deploy production với public URL
        return response()->json(['RspCode' => '00', 'Message' => 'OK']);
    }
}