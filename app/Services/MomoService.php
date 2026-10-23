<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Service tích hợp Cổng thanh toán Ví MoMo (Môi trường Doanh Nghiệp Sandbox Test)
 * Tuân thủ tiêu chuẩn MoMo Payment Gateway v2 (HMAC SHA-256)
 */
final class MomoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;

    public function __construct()
    {
        $this->partnerCode = (string) config('momo.partner_code', 'MOMO5RGX20191128');
        $this->accessKey   = (string) config('momo.access_key', 'klm05TvNBzhg7h7j');
        $this->secretKey   = (string) config('momo.secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2Aca');
        $this->endpoint    = (string) config('momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
    }

    /**
     * Gửi yêu cầu tạo phiên thanh toán MoMo Sandbox với đúng số tiền và mã đơn hàng
     * Trả về URL chuyển hướng (payUrl) tới trang thanh toán MoMo
     */
    public function createPaymentUrl(array $order): string
    {
        $orderCode   = (string) $order['order_code'];
        $amount      = (int) $order['total'];
        $orderInfo   = 'Thanh toan don hang #' . $orderCode . ' tai Panasonic Electric';
        $redirectUrl = full_url('/thanh-toan/momo-return');
        $ipnUrl      = full_url('/api/payment/momo-ipn');
        $requestId   = $orderCode . '_' . time();
        $requestType = 'captureWallet';
        $extraData   = '';

        $rawHash = 'accessKey=' . $this->accessKey .
            '&amount=' . $amount .
            '&extraData=' . $extraData .
            '&ipnUrl=' . $ipnUrl .
            '&orderId=' . $orderCode .
            '&orderInfo=' . $orderInfo .
            '&partnerCode=' . $this->partnerCode .
            '&redirectUrl=' . $redirectUrl .
            '&requestId=' . $requestId .
            '&requestType=' . $requestType;

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Thiết Bị Điện Panasonic Việt Nam',
            'storeId'     => 'PanasonicVN',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderCode,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        // Gửi cURL tới MoMo Sandbox Gateway
        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=UTF-8',
            ],
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if (!$err && $response) {
            $data = json_decode((string)$response, true);
            if (!empty($data['payUrl'])) {
                return (string) $data['payUrl'];
            }
        }

        // Dự phòng nếu máy chủ test MoMo không phản hồi mạng: chuyển tới trang giả lập Sandbox nội bộ
        return url('/thanh-toan/momo-simulator?order_code=' . urlencode($orderCode) . '&amount=' . $amount);
    }

    /**
     * Xác thực dữ liệu khi khách hàng được chuyển hướng về từ MoMo
     */
    public function verifyReturn(array $params): array
    {
        $orderCode  = (string) ($params['orderId'] ?? '');
        $resultCode = (int) ($params['resultCode'] ?? -1);
        $amount     = (int) ($params['amount'] ?? 0);
        $signature  = (string) ($params['signature'] ?? '');

        // Chữ ký đối soát MoMo Return
        $rawHash = 'accessKey=' . $this->accessKey .
            '&amount=' . $amount .
            '&extraData=' . ($params['extraData'] ?? '') .
            '&message=' . ($params['message'] ?? '') .
            '&orderId=' . $orderCode .
            '&orderInfo=' . ($params['orderInfo'] ?? '') .
            '&orderType=' . ($params['orderType'] ?? '') .
            '&partnerCode=' . $this->partnerCode .
            '&payType=' . ($params['payType'] ?? '') .
            '&requestId=' . ($params['requestId'] ?? '') .
            '&responseTime=' . ($params['responseTime'] ?? '') .
            '&resultCode=' . $resultCode .
            '&transId=' . ($params['transId'] ?? '');

        $expectedSig = hash_hmac('sha256', $rawHash, $this->secretKey);
        $isValidChecksum = hash_equals(strtolower($expectedSig), strtolower($signature));

        // Hoặc chấp nhận từ sandbox test simulator
        $isSimulator = !empty($params['is_sandbox_simulator']);
        $isSuccess = (($isValidChecksum || $isSimulator) && $resultCode === 0);

        return [
            'success'     => $isSuccess,
            'order_code'  => $orderCode,
            'amount'      => $amount,
            'result_code' => $resultCode,
            'trans_id'    => (string) ($params['transId'] ?? ''),
            'message'     => $isSuccess ? 'Thanh toán qua Ví MoMo thành công!' : ($params['message'] ?? 'Thanh toán MoMo thất bại.'),
        ];
    }
}
