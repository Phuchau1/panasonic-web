<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Service tích hợp Cổng thanh toán VNPAY (Môi trường Doanh Nghiệp Sandbox Test)
 * Tuân thủ tiêu chuẩn API VNPAY 2.1.0 (SHA-512 Checksum)
 */
final class VnPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;

    public function __construct()
    {
        $this->tmnCode    = (string) config('vnpay.tmn_code', 'CGXZLS0Z');
        $this->hashSecret = (string) config('vnpay.hash_secret', 'XNBCJFAKAZQSGTARRLTXCVPFOHVUXHYW');
        $this->url        = (string) config('vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    }

    /**
     * Tạo URL chuyển hướng thanh toán VNPAY với đúng số tiền và mã đơn hàng
     */
    public function createPaymentUrl(array $order): string
    {
        $vnp_Returnurl = full_url('/thanh-toan/vnpay-return');
        $vnp_TxnRef    = (string) $order['order_code'];
        $vnp_OrderInfo = 'Thanh toan don hang #' . $vnp_TxnRef . ' tai Panasonic Electric';
        $vnp_Amount    = ((int) $order['total']) * 100; // VNPAY tính đơn vị xu (nhân 100)
        $vnp_IpAddr    = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (string) $vnp_Amount,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $vnp_IpAddr,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $vnp_OrderInfo,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnp_Returnurl,
            'vnp_TxnRef'     => $vnp_TxnRef,
            'vnp_ExpireDate' => date('YmdHis', strtotime('+30 minutes')),
        ];

        ksort($inputData);
        $query    = '';
        $hashdata = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashdata .= '&' . urlencode((string)$key) . '=' . urlencode((string)$value);
            } else {
                $hashdata .= urlencode((string)$key) . '=' . urlencode((string)$value);
                $i = 1;
            }
            $query .= urlencode((string)$key) . '=' . urlencode((string)$value) . '&';
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        return $this->url . '?' . $query . 'vnp_SecureHash=' . $vnp_SecureHash;
    }

    /**
     * Kiểm tra tính hợp lệ dữ liệu phản hồi từ VNPAY (Verify Checksum & Mã kết quả)
     */
    public function verifyReturn(array $params): array
    {
        $vnp_SecureHash = (string) ($params['vnp_SecureHash'] ?? '');
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        ksort($params);
        $hashdata = '';
        $i = 0;
        foreach ($params as $key => $value) {
            if ($i === 1) {
                $hashdata .= '&' . urlencode((string)$key) . '=' . urlencode((string)$value);
            } else {
                $hashdata .= urlencode((string)$key) . '=' . urlencode((string)$value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $orderCode  = (string) ($params['vnp_TxnRef'] ?? '');
        $respCode   = (string) ($params['vnp_ResponseCode'] ?? '');
        $amount     = (int) (($params['vnp_Amount'] ?? 0) / 100);

        // Chữ ký hợp lệ và mã phản hồi = '00' (Thành công)
        $isValidChecksum = hash_equals(strtolower($secureHash), strtolower($vnp_SecureHash));
        $isSuccess = ($isValidChecksum && $respCode === '00');

        return [
            'success'       => $isSuccess,
            'order_code'    => $orderCode,
            'amount'        => $amount,
            'response_code' => $respCode,
            'transaction_no'=> (string) ($params['vnp_TransactionNo'] ?? ''),
            'bank_code'     => (string) ($params['vnp_BankCode'] ?? ''),
            'message'       => $isSuccess 
                ? 'Giao dịch thanh toán VNPAY thành công!' 
                : $this->getErrorMessage($respCode),
        ];
    }

    /**
     * Diễn giải mã phản hồi VNPAY
     */
    private function getErrorMessage(string $code): string
    {
        return match ($code) {
            '07'    => 'Trừ tiền thành công nhưng nghi ngờ gian lận.',
            '09'    => 'Thẻ/Tài khoản chưa đăng ký dịch vụ Internet Banking.',
            '10'    => 'Xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11'    => 'Hết hạn chờ thanh toán. Xin vui lòng thử lại.',
            '12'    => 'Thẻ/Tài khoản bị khóa.',
            '13'    => 'Nhập sai mã OTP xác thực.',
            '24'    => 'Khách hàng hủy giao dịch.',
            '51'    => 'Tài khoản không đủ số dư để thanh toán.',
            '65'    => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
            '75'    => 'Ngân hàng thanh toán đang bảo trì.',
            '79'    => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
            default => 'Giao dịch không thành công hoặc chữ ký không hợp lệ (Mã: ' . $code . ').'
        };
    }
}
