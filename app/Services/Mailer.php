<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Service gửi email thông báo đơn hàng tự động (Hỗ trợ Gmail SMTP và Local Archive)
 */
final class Mailer
{
    private array $config;

    public function __construct()
    {
        $this->config = config('mail', [
            'driver'                   => 'smtp',
            'host'                     => 'smtp.gmail.com',
            'port'                     => 587,
            'encryption'               => 'tls',
            'username'                 => 'ntphau21@gmail.com',
            'password'                 => '',
            'from_name'                => 'Thiết Bị Điện Panasonic Việt Nam',
            'from_email'               => 'ntphau21@gmail.com',
            'admin_notification_email' => 'ntphau21@gmail.com',
        ]);
    }

    /**
     * Gửi email xác nhận đặt hàng thành công tới khách hàng VÀ thông báo đơn mới tới email doanh nghiệp
     */
    public function sendOrderConfirmation(array $order, array $items): array
    {
        $customerEmail = trim((string)($order['email'] ?? ''));
        $adminEmail    = $this->config['admin_notification_email'] ?? 'ntphau21@gmail.com';

        $isPaid = (($order['payment_status'] ?? '') === 'paid');
        $isBank = in_array($order['payment_method'] ?? '', ['vietqr', 'bank_transfer'], true);

        if ($isBank && !$isPaid) {
            $customerSubject = '⏳ [Chờ thanh toán chuyển khoản đơn hàng #' . $order['order_code'] . '] - Hướng dẫn thanh toán VietQR';
            $adminSubject    = '🔔 [ĐƠN HÀNG MỚI - CHỜ CHUYỂN KHOẢN #' . $order['order_code'] . '] - Giá trị: ' . money($order['total']) . ' - KH: ' . $order['customer_name'];
        } else {
            $customerSubject = '✓ [Xác nhận đặt hàng thành công #' . $order['order_code'] . '] - Cảm ơn quý khách đã mua sắm tại Thiết Bị Điện Panasonic';
            $adminSubject    = ($isPaid && $isBank ? '💰 [ĐÃ THANH TOÁN VIETQR #' : '🔔 [ĐƠN HÀNG MỚI #') . $order['order_code'] . '] - Giá trị: ' . money($order['total']) . ' - KH: ' . $order['customer_name'];
        }

        $customerBody = $this->buildOrderEmailTemplate($order, $items, false);
        $adminBody    = $this->buildOrderEmailTemplate($order, $items, true);

        // 1. Lưu bản sao lưu trữ trên máy chủ để đối soát
        $storageDir = STORAGE_PATH . '/emails';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        @file_put_contents($storageDir . '/order-' . $order['order_code'] . '-customer.html', $customerBody);
        @file_put_contents($storageDir . '/order-' . $order['order_code'] . '-admin.html', $adminBody);

        $sentCustomer = false;
        $sentAdmin    = false;
        $errorMsg     = '';

        // 2. Gửi qua SMTP nếu có mật khẩu ứng dụng Gmail
        if (!empty($this->config['password'])) {
            try {
                // Gửi cho Khách hàng (nếu có email)
                if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                    $sentCustomer = $this->sendViaSmtp($customerEmail, $customerSubject, $customerBody);
                }
                // Luôn gửi thông báo đơn hàng mới về Gmail Doanh Nghiệp
                if (!empty($adminEmail)) {
                    $sentAdmin = $this->sendViaSmtp($adminEmail, $adminSubject, $adminBody);
                }
            } catch (\Throwable $e) {
                $errorMsg = $e->getMessage();
            }
        } else {
            // Dự phòng gửi bằng hàm mail() của PHP Server
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$this->config['from_name']} <{$this->config['from_email']}>\r\n";
            $headers .= "Reply-To: {$this->config['from_email']}\r\n";

            if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                @mail($customerEmail, $customerSubject, $customerBody, $headers);
            }
            if (!empty($adminEmail)) {
                @mail($adminEmail, $adminSubject, $adminBody, $headers);
            }
            $errorMsg = 'Email đã được xử lý và lưu trữ đối soát an toàn.';
        }

        return [
            'success'       => true,
            'sent_customer' => $sentCustomer,
            'sent_admin'    => $sentAdmin,
            'customer'      => $customerEmail,
            'admin'         => $adminEmail,
            'error'         => $errorMsg,
        ];
    }

    /**
     * Kích hoạt gửi email đơn hàng chạy ngầm (Asynchronous Worker)
     * Trả lời request của khách tức thì trong vài mili-giây, không làm lag hoặc xoay vòng trang đặt hàng.
     */
    public static function dispatchAsync(string $orderCode): void
    {
        $orderCode = preg_replace('/[^a-zA-Z0-9_\-]/', '', $orderCode);
        if (empty($orderCode)) {
            return;
        }

        $cliScript = ROOT_PATH . '/cli/send_order_email.php';
        if (!is_file($cliScript)) {
            return;
        }

        if (stripos(PHP_OS, 'WIN') === 0) {
            $phpBin = 'C:\\xampp\\php\\php.exe';
            if (!is_file($phpBin)) {
                $phpBin = 'php';
            }
            $cmd = 'start /B ' . escapeshellarg($phpBin) . ' ' . escapeshellarg($cliScript) . ' ' . escapeshellarg($orderCode) . ' > NUL 2>&1';
            @pclose(@popen($cmd, 'r'));
        } else {
            $cmd = 'php ' . escapeshellarg($cliScript) . ' ' . escapeshellarg($orderCode) . ' > /dev/null 2>&1 &';
            @exec($cmd);
        }
    }

    /**
     * Gửi trực tiếp qua kết nối SMTP Socket (Hỗ trợ STARTTLS & SSL của Gmail)
     */
    private function sendViaSmtp(string $to, string $subject, string $htmlBody): bool
    {
        $host       = $this->config['host'] ?? 'smtp.gmail.com';
        $port       = (int) ($this->config['port'] ?? 587);
        $encryption = $this->config['encryption'] ?? 'tls';
        $username   = $this->config['username'] ?? '';
        $password   = $this->config['password'] ?? '';
        $fromEmail  = $this->config['from_email'] ?? $username;
        $fromName   = $this->config['from_name'] ?? 'Panasonic Electric';

        $socketHost = ($encryption === 'ssl') ? 'ssl://' . $host : $host;
        $socket = @fsockopen($socketHost, $port, $errno, $errstr, 5);
        if (!$socket) {
            throw new \RuntimeException("Không thể kết nối máy chủ SMTP {$host}:{$port} - {$errstr}");
        }

        stream_set_timeout($socket, 5);

        $read = function () use ($socket): string {
            $data = '';
            while ($str = fgets($socket, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) === ' ') break;
            }
            return $data;
        };

        $write = function (string $cmd) use ($socket, $read): string {
            fputs($socket, $cmd . "\r\n");
            return $read();
        };

        $read(); // Đọc lời chào ban đầu từ SMTP server
        $write("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));

        // Kích hoạt STARTTLS nếu chạy cổng 587
        if ($encryption === 'tls') {
            $write("STARTTLS");
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $write("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        }

        // Đăng nhập
        $write("AUTH LOGIN");
        $write(base64_encode($username));
        $res = $write(base64_encode($password));
        if (!str_starts_with($res, '235')) {
            fclose($socket);
            throw new \RuntimeException("Xác thực SMTP thất bại. Vui lòng kiểm tra mật khẩu ứng dụng Gmail.");
        }

        // Gửi lệnh gửi mail (chỉ gửi cho đúng địa chỉ người nhận $to)
        $write("MAIL FROM: <{$fromEmail}>");
        $write("RCPT TO: <{$to}>");
        $write("DATA");

        // Headers
        $emailData  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
        $emailData .= "To: <{$to}>\r\n";
        $emailData .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $emailData .= "Date: " . date('r') . "\r\n";
        $emailData .= "MIME-Version: 1.0\r\n";
        $emailData .= "Content-Type: text/html; charset=UTF-8\r\n";
        $emailData .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $emailData .= $htmlBody . "\r\n.\r\n";

        fputs($socket, $emailData);
        $read();

        $write("QUIT");
        fclose($socket);
        return true;
    }

    /**
     * Mẫu Email HTML Xác nhận Đơn Hàng Chuẩn Doanh Nghiệp Panasonic
     */
    private function buildOrderEmailTemplate(array $order, array $items, bool $isAdmin = false): string
    {
        $code = e($order['order_code']);
        $name = e($order['customer_name']);
        $phone = e($order['phone']);
        $customerEmail = e($order['email'] ?? 'Không cung cấp');
        $address = e($order['address']);
        $totalText = money($order['total']);
        $created = date('H:i, d/m/Y', strtotime($order['created_at'] ?? 'now'));
        
        $bankAccount = config('bank.account_no', '37051817');
        $bankName = config('bank.name', 'Ngân hàng TMCP Á Châu (ACB)');
        $bankBin = config('bank.bin', '970416');
        $vietqrImg = "https://img.vietqr.io/image/{$bankBin}-{$bankAccount}-compact2.png?amount=" . (int)$order['total'] . "&addInfo=" . urlencode($order['order_code']);

        $itemsRows = '';
        foreach ($items as $it) {
            $unitPrice = money($it['unit_price'] ?? $it['price'] ?? 0);
            $lineTotal = money($it['line_total']);
            $itemsRows .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eef2f6;'>
                        <strong style='color: #0f172a;'>{$it['product_name']}</strong>
                        <div style='font-size: 12px; color: #64748b; font-family: monospace;'>Model: {$it['model_code']}</div>
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eef2f6; text-align: center; font-weight: 600;'>
                        {$it['quantity']}
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eef2f6; text-align: right; color: #475569;'>
                        {$unitPrice}
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eef2f6; text-align: right; font-weight: 700; color: #004098;'>
                        {$lineTotal}
                    </td>
                </tr>
            ";
        }

        $paymentMethodTitle = match ($order['payment_method']) {
            'vietqr'        => 'Chuyển khoản VietQR Siêu Tốc (ACB STK 37051817)',
            'bank_transfer' => 'Chuyển khoản ngân hàng ACB (STK 37051817)',
            'cod'           => 'Thanh toán tiền mặt khi nhận hàng (COD)',
            'momo'          => 'Ví Điện Tử MoMo (Sandbox Test)',
            'vnpay'         => 'Cổng Thanh Toán VNPAY (Sandbox Test)',
            'b2b_debt'      => 'Bảo lãnh công nợ B2B theo hợp đồng khung',
            default         => 'Thanh toán trực tiếp'
        };

        $paymentBoxHtml = '';
        if ($order['payment_method'] === 'vietqr' || $order['payment_method'] === 'bank_transfer') {
            $paymentBoxHtml = "
                <div style='background: #f0f7ff; border: 2px solid #004098; border-radius: 8px; padding: 16px; margin: 20px 0;'>
                    <h3 style='margin: 0 0 10px 0; color: #004098; font-size: 16px;'>⚡ Thông tin chuyển khoản thanh toán VietQR Napas 24/7</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='width: 140px; text-align: center; vertical-align: top; padding-right: 15px;'>
                                <img src='{$vietqrImg}' alt='VietQR ACB {$code}' width='140' height='140' style='border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; padding: 4px;' />
                                <div style='font-size: 11px; color: #64748b; margin-top: 4px;'>Quét bằng mọi App Ngân Hàng</div>
                            </td>
                            <td style='vertical-align: top;'>
                                <p style='margin: 4px 0; font-size: 14px;'>Ngân hàng: <strong>{$bankName}</strong></p>
                                <p style='margin: 4px 0; font-size: 14px;'>Số tài khoản nhận: <strong style='color: #004098; font-size: 16px; font-family: monospace;'>{$bankAccount}</strong></p>
                                <p style='margin: 4px 0; font-size: 14px;'>Số tiền chính xác: <strong style='color: #e11d48; font-size: 16px;'>{$totalText}</strong></p>
                                <p style='margin: 4px 0; font-size: 14px;'>Nội dung chuyển tiền: <strong style='background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #004098; font-family: monospace;'>{$code}</strong></p>
                                <p style='margin: 6px 0 0 0; font-size: 12px; color: #1e40af;'><em>* Quét mã QR để ứng dụng ngân hàng tự động điền đúng STK, số tiền và nội dung đơn hàng.</em></p>
                            </td>
                        </tr>
                    </table>
                </div>
            ";
        }

        $isPaid = (($order['payment_status'] ?? '') === 'paid');
        $isBank = in_array($order['payment_method'] ?? '', ['vietqr', 'bank_transfer'], true);

        if ($isAdmin) {
            if ($isBank && !$isPaid) {
                $headerTitle = "
                    <span style='background:#fef3c7;color:#b45309;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;'>⏳ ĐƠN HÀNG MỚI - CHỜ KHÁCH CHUYỂN KHOẢN</span>
                    <h2 style='margin:10px 0 4px 0;color:#0f172a;font-size:20px;'>Có đơn hàng mới #{$code} (Chờ chuyển khoản)</h2>
                    <p style='margin:0;color:#64748b;font-size:14px;'>Đơn hàng đang chờ khách hàng chuyển khoản VietQR Napas 24/7. Chưa xuất kho cho tới khi có xác nhận tiền về.</p>
                ";
            } elseif ($isPaid && $isBank) {
                $headerTitle = "
                    <span style='background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;'>💰 ĐÃ XÁC NHẬN THANH TOÁN VIETQR THÀNH CÔNG</span>
                    <h2 style='margin:10px 0 4px 0;color:#0f172a;font-size:20px;'>Đơn hàng #{$code} đã thanh toán thành công!</h2>
                    <p style='margin:0;color:#64748b;font-size:14px;'>Khách hàng đã hoàn tất chuyển khoản. Bộ phận kho bãi tiến hành đóng hàng và xuất kho.</p>
                ";
            } else {
                $headerTitle = "
                    <span style='background:#fef08a;color:#854d0e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;'>🔔 THÔNG BÁO ĐƠN HÀNG MỚI (COD)</span>
                    <h2 style='margin:10px 0 4px 0;color:#0f172a;font-size:20px;'>Có khách hàng vừa đặt đơn mới #{$code}</h2>
                    <p style='margin:0;color:#64748b;font-size:14px;'>Vui lòng kiểm tra tồn kho, liên hệ khách hàng để xác nhận và điều phối xe giao hàng.</p>
                ";
            }
        } else {
            if ($isBank && !$isPaid) {
                $headerTitle = "
                    <span style='background:#fef3c7;color:#b45309;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;'>⏳ ĐANG CHỜ THANH TOÁN CHUYỂN KHOẢN</span>
                    <h2 style='margin:10px 0 4px 0;color:#b45309;font-size:20px;'>Đơn hàng #{$code} đang chờ thanh toán</h2>
                    <p style='margin:0;color:#64748b;font-size:14px;'>Đơn hàng của Quý khách đã được khởi tạo. <strong>Vui lòng hoàn tất chuyển khoản</strong> theo thông tin bên dưới để hệ thống xác nhận đặt hàng thành công.</p>
                ";
            } else {
                $headerTitle = "
                    <span style='background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;'>✓ ĐẶT HÀNG THÀNH CÔNG</span>
                    <h2 style='margin:10px 0 4px 0;color:#0f172a;font-size:20px;'>Cảm ơn Quý khách {$name} đã đặt hàng!</h2>
                    <p style='margin:0;color:#64748b;font-size:14px;'>Đơn hàng của Quý khách đã được xác nhận thành công và tiếp nhận vào hệ thống điều phối vật tư Panasonic.</p>
                ";
            }
        }

        $adminActionBtn = $isAdmin
            ? "<div style='text-align:center;margin:25px 0 10px 0;'>
                <a href='" . url('/admin/don-hang') . "' style='background:#004098;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:700;font-size:14px;display:inline-block;'>MỞ TRANG QUẢN TRỊ ĐƠN HÀNG »</a>
               </div>"
            : "";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Đơn hàng #{$code}</title>
        </head>
        <body style='margin: 0; padding: 20px; background-color: #f6f8fb; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif;'>
            <div style='max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #e5e9f2;'>
                
                <!-- Header -->
                <div style='background: #004098; padding: 24px; text-align: center; color: #ffffff;'>
                    <h1 style='margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.5px;'>PANASONIC ELECTRIC PRO</h1>
                    <p style='margin: 6px 0 0 0; font-size: 13px; opacity: 0.9;'>Kho Phân Phối Thiết Bị Điện Panasonic Chính Hãng Việt Nam</p>
                </div>

                <!-- Main Content -->
                <div style='padding: 24px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        {$headerTitle}
                    </div>

                    <!-- Thông tin đơn -->
                    <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 20px;'>
                        <div style='display: flex; justify-content: space-between; margin-bottom: 6px;'>
                            <span style='color: #64748b; font-size: 13px;'>Mã đơn hàng:</span>
                            <strong style='color: #004098; font-size: 14px;'>#{$code}</strong>
                        </div>
                        <div style='display: flex; justify-content: space-between; margin-bottom: 6px;'>
                            <span style='color: #64748b; font-size: 13px;'>Thời gian đặt:</span>
                            <span style='color: #0f172a; font-size: 13px;'>{$created}</span>
                        </div>
                        <div style='display: flex; justify-content: space-between; margin-bottom: 6px;'>
                            <span style='color: #64748b; font-size: 13px;'>Phương thức:</span>
                            <span style='color: #0f172a; font-size: 13px; font-weight: 600;'>{$paymentMethodTitle}</span>
                        </div>
                    </div>

                    <!-- Người nhận -->
                    <div style='margin-bottom: 20px;'>
                        <h3 style='margin: 0 0 8px 0; font-size: 14px; color: #0f172a; text-transform: uppercase;'>📍 Thông tin người nhận & Địa chỉ công trình</h3>
                        <p style='margin: 3px 0; font-size: 14px;'><strong>Người nhận:</strong> {$name} - SĐT: <a href='tel:{$phone}' style='color:#004098;font-weight:700;'>{$phone}</a></p>
                        <p style='margin: 3px 0; font-size: 14px;'><strong>Email liên hệ:</strong> {$customerEmail}</p>
                        <p style='margin: 3px 0; font-size: 14px;'><strong>Địa chỉ giao:</strong> {$address}</p>
                    </div>

                    <!-- Bảng thiết bị -->
                    <div style='margin-bottom: 20px;'>
                        <h3 style='margin: 0 0 8px 0; font-size: 14px; color: #0f172a; text-transform: uppercase;'>📦 Danh mục thiết bị đặt mua</h3>
                        <table style='width: 100%; border-collapse: collapse; font-size: 13px;'>
                            <thead>
                                <tr style='background: #f1f5f9;'>
                                    <th style='padding: 8px 10px; text-align: left; color: #475569;'>Thiết bị</th>
                                    <th style='padding: 8px 10px; text-align: center; color: #475569;'>SL</th>
                                    <th style='padding: 8px 10px; text-align: right; color: #475569;'>Đơn giá</th>
                                    <th style='padding: 8px 10px; text-align: right; color: #475569;'>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$itemsRows}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='3' style='padding: 12px 10px; text-align: right; font-weight: 700; font-size: 14px;'>Tổng thanh toán:</td>
                                    <td style='padding: 12px 10px; text-align: right; font-weight: 800; font-size: 17px; color: #004098;'>{$totalText}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Khối thanh toán QR nếu chuyển khoản -->
                    {$paymentBoxHtml}

                    {$adminActionBtn}

                    <!-- Cam kết -->
                    <div style='background: #fafbfc; border-top: 1px solid #eef2f6; padding: 14px; border-radius: 6px; font-size: 12px; color: #64748b;'>
                        <p style='margin: 2px 0;'>✓ Cam kết 100% thiết bị điện Panasonic chính hãng, có CO/CQ và hóa đơn VAT.</p>
                        <p style='margin: 2px 0;'>✓ Nhân viên giao vận sẽ liên hệ trước khi điều xe đến chân công trình.</p>
                        <p style='margin: 2px 0;'>Hotline hỗ trợ điều phối: <strong>0838.484.885</strong></p>
                    </div>
                </div>

                <!-- Footer -->
                <div style='background: #f1f5f9; padding: 14px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;'>
                    © " . date('Y') . " Thiết Bị Điện Panasonic Việt Nam. Email này được gửi tự động từ hệ thống quản lý đơn hàng.
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
