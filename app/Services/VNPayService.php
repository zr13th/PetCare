<?php


namespace App\Services;

class VNPayService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('vnpay');
    }

    public function createPaymentUrl(string $orderRef, int $amount, string $orderInfo, string $ipAddr): string 
    {
        $now = now()->timezone('Asia/Ho_Chi_Minh');

        $vnpParams = [
            'vnp_Version'    => $this->config['version'],
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->config['tmn_code'],
            'vnp_Amount'     => $amount * 100,
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $orderRef,
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => $this->config['locale'],
            'vnp_ReturnUrl'  => $this->config['return_url'],
            'vnp_IpAddr'     => $ipAddr,
            'vnp_CreateDate' => $now->format('YmdHis'),
        ];

        ksort($vnpParams);

        $hashData = "";
        $i = 0;
        foreach ($vnpParams as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->config['hash_secret']);
        
        // Sử dụng http_build_query để tạo query string chuẩn
        $queryString = http_build_query($vnpParams);
        
        return $this->config['url'] . "?" . $queryString . '&vnp_SecureHash=' . $vnpSecureHash;
    }

    public function verifyReturn(array $params): bool
    {
        // 1. Lấy mã hash VNPay gửi về
        $vnp_SecureHash = $params['vnp_SecureHash'] ?? '';
        
        // 2. Lọc bỏ các tham số không dùng để băm
        $inputData = array();
        foreach ($params as $key => $value) {
            // Chỉ lấy các tham số có tiền tố vnp_ và loại bỏ các trường hash
            if (substr($key, 0, 4) == "vnp_" && $key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
                $inputData[$key] = $value;
            }
        }

        // 3. Sắp xếp alphabet (Quan trọng nhất)
        ksort($inputData);

        // 4. Tạo chuỗi hash data chuẩn
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // 5. Tính toán mã hash mong đợi bằng Secret Key trong .env
        $expectedHash = hash_hmac('sha512', $hashData, $this->config['hash_secret']);

        // 6. So sánh
        return hash_equals(strtolower($expectedHash), strtolower($vnp_SecureHash));
    }

    public function verifyIPN(array $params): bool
    {
        return $this->verifyReturn($params);
    }

    public function isSuccess(array $params): bool
    {
        return ($params['vnp_ResponseCode'] ?? '') === '00'
            && ($params['vnp_TransactionStatus'] ?? '') === '00';
    }
}