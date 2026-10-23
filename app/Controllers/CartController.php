<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\UserAddress;
use App\Services\Cart;
use Core\Controller;

class CartController extends Controller
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    /** GET /gio-hang */
    public function index(): void
    {
        $user = auth_user();
        $savedAddresses = [];
        $defaultAddress = null;

        if ($user) {
            $addressModel = new UserAddress();
            $savedAddresses = $addressModel->getByUser((int) $user['id']);
            $defaultAddress = $addressModel->getDefault((int) $user['id']);
        }

        $this->render('cart/index', [
            'title'          => 'Giỏ hàng & Dự toán vật tư công trình | ' . config('name'),
            'summary'        => $this->cart->summarize(),
            'user'           => $user,
            'savedAddresses' => $savedAddresses,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    /** GET /api/gio-hang (Lấy dữ liệu giỏ hàng JSON cho drawer bên phải) */
    public function apiGetCart(): void
    {
        $summary = $this->cart->summarize();
        $lines = array_map(function ($l) {
            return [
                'id'              => $l['id'],
                'name'            => $l['name'],
                'slug'            => $l['slug'],
                'model_code'      => $l['model_code'],
                'thumbnail'       => media($l['thumbnail']),
                'unit'            => $l['unit'],
                'qty'             => $l['qty'],
                'unit_price'      => $l['unit_price'],
                'unit_price_text' => money($l['unit_price']),
                'line_total'      => $l['line_total'],
                'line_total_text' => money($l['line_total']),
                'url'             => url('/san-pham/' . $l['slug']),
            ];
        }, $summary['lines']);

        $this->json([
            'success'       => true,
            'count'         => $summary['count'],
            'subtotal'      => $summary['subtotal'],
            'discount'      => $summary['discount'],
            'total'         => $summary['total'],
            'subtotal_text' => money($summary['subtotal']),
            'discount_text' => money($summary['discount']),
            'total_text'    => money($summary['total']),
            'lines'         => $lines,
        ]);
    }

    /** POST /gio-hang/them  (product_id, qty) */
    public function add(): void
    {
        $this->verifyCsrf();

        $productId = $this->inputInt('product_id') ?: $this->inputInt('id');
        $qty       = max(1, min(Cart::MAX_QTY, $this->inputInt('qty', 1)));
        $product   = (new Product())->find($productId);

        if (!$product || (int) $product['status'] !== 1) {
            $this->respond(false, 'Sản phẩm không tồn tại.', 404);
        }

        $inCart = (int) ($this->cart->items()[$productId] ?? 0);
        if ($inCart + $qty > (int) $product['stock']) {
            $this->respond(false, sprintf('Chỉ còn %d %s trong kho. Vui lòng gửi yêu cầu báo giá công trình để đặt số lượng lớn.', $product['stock'], $product['unit']), 422);
        }

        $this->cart->add($productId, $qty);

        $this->respond(true, sprintf('Đã thêm %d × %s vào giỏ hàng', $qty, $product['model_code']), 200, [
            'product' => ['name' => $product['name'], 'model_code' => $product['model_code']],
        ]);
    }

    /** POST /gio-hang/cap-nhat (product_id, qty) */
    public function update(): void
    {
        $this->verifyCsrf();

        $productId = $this->inputInt('product_id');
        $qty       = $this->inputInt('qty', 1);
        $product   = (new Product())->find($productId);

        if (!$product) {
            $this->respond(false, 'Sản phẩm không tồn tại.', 404);
        }
        if ($qty > (int) $product['stock']) {
            $qty = (int) $product['stock'];
            $this->cart->set($productId, $qty);
            $this->respond(true, "Số lượng tối đa hiện có: {$qty}", 200);
        }

        $this->cart->set($productId, $qty);
        $this->respond(true, 'Đã cập nhật giỏ hàng');
    }

    /** POST /gio-hang/xoa (product_id) */
    public function remove(): void
    {
        $this->verifyCsrf();
        $this->cart->remove($this->inputInt('product_id'));
        $this->respond(true, 'Đã xoá sản phẩm khỏi giỏ');
    }

    /**
     * API: Lấy danh sách Tỉnh / Thành phố Việt Nam (Cập nhật sáp nhập mới nhất theo Nghị quyết Quốc hội)
     * GET /api/locations/provinces
     */
    public function getProvinces(): void
    {
        $dataFile = PUBLIC_PATH . '/data/vn_merged_provinces.json';
        if (!is_file($dataFile)) {
            $dataFile = PUBLIC_PATH . '/data/vn_full_locations.json';
        }
        if (!is_file($dataFile)) {
            $this->json([]);
            return;
        }

        $json = file_get_contents($dataFile);
        $data = json_decode($json, true) ?: [];

        $provinces = array_map(static function ($p) {
            return [
                'code' => $p['code'] ?? 0,
                'name' => $p['name'] ?? '',
            ];
        }, $data);

        $this->json($provinces);
    }

    /**
     * API: Lấy danh sách Quận / Huyện theo Tỉnh / Thành
     * GET /api/locations/districts?province_code=... OR ?province_name=...
     */
    public function getDistricts(): void
    {
        $dataFile = PUBLIC_PATH . '/data/vn_merged_provinces.json';
        if (!is_file($dataFile)) {
            $dataFile = PUBLIC_PATH . '/data/vn_full_locations.json';
        }
        if (!is_file($dataFile)) {
            $this->json([]);
            return;
        }

        $code = (int) ($this->input('province_code') ?? 0);
        $name = trim((string) $this->input('province_name'));

        $json = file_get_contents($dataFile);
        $data = json_decode($json, true) ?: [];

        $districts = [];
        foreach ($data as $p) {
            if (($code > 0 && (int)$p['code'] === $code) || ($name !== '' && mb_strtolower($p['name']) === mb_strtolower($name))) {
                foreach ($p['districts'] ?? [] as $d) {
                    $districts[] = [
                        'code' => $d['code'] ?? 0,
                        'name' => $d['name'] ?? '',
                    ];
                }
                break;
            }
        }

        $this->json($districts);
    }

    /**
     * API: Lấy danh sách Phường / Xã theo Quận / Huyện
     * GET /api/locations/wards?district_code=... OR ?district_name=...
     */
    public function getWards(): void
    {
        $districtCode = (int) ($this->input('district_code') ?? 0);
        $districtName = trim((string) $this->input('district_name'));

        $dataFile = PUBLIC_PATH . '/data/vn_merged_provinces.json';
        if (!is_file($dataFile)) {
            $dataFile = PUBLIC_PATH . '/data/vn_full_locations.json';
        }

        if (is_file($dataFile)) {
            $json = file_get_contents($dataFile);
            $data = json_decode($json, true) ?: [];

            foreach ($data as $p) {
                foreach ($p['districts'] ?? [] as $d) {
                    if (($districtCode > 0 && (int)$d['code'] === $districtCode) || 
                        ($districtName !== '' && mb_strtolower($d['name']) === mb_strtolower($districtName))) {
                        $wards = array_map(static function ($w) {
                            return [
                                'code' => $w['code'] ?? 0,
                                'name' => $w['name'] ?? '',
                            ];
                        }, $d['wards'] ?? []);

                        $this->json($wards);
                        return;
                    }
                }
            }
        }

        $this->json([]);
    }

    /**
     * API: Lấy danh sách địa chỉ của người dùng đã đăng nhập (Chuẩn Shopee / TikTok Shop)
     * GET /api/user/addresses
     */
    public function getUserAddresses(): void
    {
        $user = auth_user();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            return;
        }

        $addresses = (new UserAddress())->getByUser((int) $user['id']);
        $this->json(['success' => true, 'addresses' => $addresses]);
    }

    /**
     * API: Thêm địa chỉ mới vào sổ địa chỉ người dùng
     * POST /api/user/addresses/add
     */
    public function addUserAddress(): void
    {
        $this->verifyCsrf();
        $user = auth_user();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu địa chỉ'], 401);
            return;
        }

        $input = $this->getJsonInput() ?: $_POST;

        $recipient = trim((string)($input['recipient_name'] ?? ''));
        $phone     = preg_replace('/[\s\.\-]/', '', (string)($input['phone'] ?? ''));
        $province  = trim((string)($input['province'] ?? ''));
        $district  = trim((string)($input['district'] ?? ''));
        $ward      = trim((string)($input['ward'] ?? ''));
        $detail    = trim((string)($input['detail_address'] ?? ''));
        $label     = trim((string)($input['label'] ?? 'Chân công trình'));
        $isDefault = !empty($input['is_default']) ? 1 : 0;

        if (mb_strlen($recipient) < 2) {
            $this->json(['success' => false, 'message' => 'Họ tên người nhận không được để trống.'], 422);
            return;
        }
        if (!preg_match('/^(0|\+84)(3|5|7|8|9|2)\d{8}$/', $phone)) {
            $this->json(['success' => false, 'message' => 'Số điện thoại nhận hàng không hợp lệ.'], 422);
            return;
        }
        if (empty($province) || empty($district)) {
            $this->json(['success' => false, 'message' => 'Vui lòng chọn Tỉnh/Thành và Quận/Huyện.'], 422);
            return;
        }
        if (mb_strlen($detail) < 4) {
            $this->json(['success' => false, 'message' => 'Vui lòng nhập địa chỉ cụ thể (số nhà, tên đường, chân công trình).'], 422);
            return;
        }

        $addressModel = new UserAddress();
        $newId = $addressModel->addAddress((int) $user['id'], [
            'recipient_name' => $recipient,
            'phone'          => $phone,
            'province'       => $province,
            'district'       => $district,
            'ward'           => $ward,
            'detail_address' => $detail,
            'label'          => $label,
            'is_default'     => $isDefault,
        ]);

        $created = $addressModel->find($newId);
        $all = $addressModel->getByUser((int) $user['id']);

        $this->json([
            'success'   => true,
            'message'   => 'Đã thêm địa chỉ giao hàng mới thành công!',
            'address'   => $created,
            'addresses' => $all,
        ]);
    }

    /**
     * API: Đặt làm địa chỉ mặc định
     * POST /api/user/addresses/set-default
     */
    public function setDefaultAddress(): void
    {
        $this->verifyCsrf();
        $user = auth_user();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
            return;
        }

        $input = $this->getJsonInput() ?: $_POST;
        $addressId = (int) ($input['address_id'] ?? 0);

        (new UserAddress())->setDefault((int) $user['id'], $addressId);
        $addresses = (new UserAddress())->getByUser((int) $user['id']);

        $this->json([
            'success'   => true,
            'message'   => 'Đã thiết lập địa chỉ mặc định.',
            'addresses' => $addresses,
        ]);
    }

    /**
     * API: Xóa địa chỉ khỏi sổ địa chỉ
     * POST /api/user/addresses/delete
     */
    public function deleteUserAddress(): void
    {
        $this->verifyCsrf();
        $user = auth_user();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
            return;
        }

        $input = $this->getJsonInput() ?: $_POST;
        $addressId = (int) ($input['address_id'] ?? 0);

        (new UserAddress())->deleteAddress((int) $user['id'], $addressId);
        $addresses = (new UserAddress())->getByUser((int) $user['id']);

        $this->json([
            'success'   => true,
            'message'   => 'Đã xóa địa chỉ thành công.',
            'addresses' => $addresses,
        ]);
    }

    /** POST /gio-hang/dat-hang */
    public function checkout(): void
    {
        $this->verifyCsrf();

        $summary = $this->cart->summarize();
        if (!$summary['lines']) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống.'], 422);
            }
            flash('error', 'Giỏ hàng trống.');
            $this->redirect('/gio-hang');
            return;
        }

        $user = auth_user();
        $userId = $user['id'] ?? null;

        // Nhận dữ liệu đặt hàng
        $recipientName = trim($this->input('customer_name') ?? $this->input('recipient_name') ?? '');
        $phone         = preg_replace('/[\s\.\-]/', '', (string)$this->input('phone'));
        $province      = trim((string)$this->input('province'));
        $district      = trim((string)$this->input('district'));
        $ward          = trim((string)$this->input('ward'));
        $detailAddress = trim((string)($this->input('detail_address') ?? $this->input('address') ?? ''));

        // Địa chỉ tổng hợp đầy đủ
        $addressParts = array_filter([$detailAddress, $ward, $district, $province]);
        $fullAddress = implode(', ', $addressParts);

        // Hóa đơn VAT
        $isVat = !empty($this->input('vat_required'));
        $company  = $isVat ? mb_substr(trim((string)$this->input('company')), 0, 200) : null;
        $taxCode  = $isVat ? mb_substr(trim((string)$this->input('tax_code')), 0, 20) : null;
        $vatEmail = $isVat ? mb_substr(trim((string)$this->input('vat_email') ?? $this->input('email')), 0, 150) : null;

        // Phương thức thanh toán
        $paymentMethod = (string) $this->input('payment_method');
        $validMethods = ['cod', 'vietqr', 'bank_transfer', 'momo', 'vnpay', 'b2b_debt'];
        if (!in_array($paymentMethod, $validMethods, true)) {
            $paymentMethod = 'cod';
        }

        $note = mb_substr(trim((string)$this->input('note')), 0, 2000);

        // Kiểm tra hợp lệ
        $errors = [];
        if (mb_strlen($recipientName) < 2) {
            $errors[] = 'Vui lòng nhập họ và tên người nhận hàng.';
        }
        if (!preg_match('/^(0|\+84)(3|5|7|8|9|2)\d{8}$/', $phone)) {
            $errors[] = 'Số điện thoại nhận hàng không hợp lệ.';
        }
        if (mb_strlen($fullAddress) < 6) {
            $errors[] = 'Vui lòng nhập đầy đủ địa chỉ giao hàng (Tỉnh, Huyện, Xã và số nhà/đường).';
        }
        if ($isVat && mb_strlen((string)$company) < 3) {
            $errors[] = 'Vui lòng nhập tên công ty xuất hóa đơn VAT.';
        }
        if ($isVat && $taxCode !== '' && !preg_match('/^\d{10}(-\d{3})?$/', (string)$taxCode)) {
            $errors[] = 'Mã số thuế doanh nghiệp không đúng định dạng (10 hoặc 13 chữ số).';
        }

        if ($errors) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => implode(' ', $errors)], 422);
                return;
            }
            flash('error', implode(' ', $errors));
            $this->redirect('/gio-hang#checkout');
            return;
        }

        // Email khách hàng nhận thông báo
        $customerEmail = trim((string)($this->input('email') ?? $vatEmail ?? ($user['email'] ?? '')));

        // Chuẩn bị dữ liệu lưu đơn hàng
        $customerData = [
            'user_id'        => $userId,
            'customer_name'  => $recipientName,
            'phone'          => $phone,
            'email'          => $customerEmail ?: null,
            'company'        => $company,
            'tax_code'       => $taxCode,
            'address'        => $fullAddress,
            'province'       => $province ?: null,
            'district'       => $district ?: null,
            'ward'           => $ward ?: null,
            'note'           => $note ?: null,
            'payment_method' => $paymentMethod,
            'payment_status' => 'unpaid',
        ];

        try {
            $code = (new Order())->place($customerData, $summary);

            // Gửi email đơn hàng không đồng bộ (Asynchronous Background Worker):
            // Phản hồi trang biên nhận đặt hàng tức thì trong vài mili-giây, không làm lag hoặc xoay vòng trang
            try {
                \App\Services\Mailer::dispatchAsync($code);
            } catch (\Throwable $e) {
                error_log('Order Email error: ' . $e->getMessage());
            }

        } catch (\DomainException $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 422);
                return;
            }
            flash('error', $e->getMessage());
            $this->redirect('/gio-hang');
            return;
        }

        // Tùy chọn lưu địa chỉ này vào sổ địa chỉ nếu user đã đăng nhập
        if ($userId && !empty($this->input('save_as_address'))) {
            try {
                (new UserAddress())->addAddress((int) $userId, [
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'province'       => $province,
                    'district'       => $district,
                    'ward'           => $ward,
                    'detail_address' => $detailAddress ?: $fullAddress,
                    'label'          => 'Chân công trình',
                    'is_default'     => 0,
                ]);
            } catch (\Throwable $e) {
                // bỏ qua lỗi nếu lưu địa chỉ trùng
            }
        }

        $this->cart->clear();
        clear_old();
        $_SESSION['last_order'] = $code;

        $savedOrder = (new Order())->findByCode($code);
        $redirectUrl = url('/dat-hang-thanh-cong/' . $code);
        $message = 'Đặt hàng thành công! Đang chuyển đến biên lai xác nhận...';

        // Tích hợp Gateway chuyển hướng chính thức với đúng số tiền đơn hàng:
        if ($paymentMethod === 'vnpay' && $savedOrder) {
            $redirectUrl = (new \App\Services\VnPayService())->createPaymentUrl($savedOrder);
            $message = 'Khởi tạo đơn hàng thành công! Đang chuyển sang Cổng thanh toán VNPAY Test...';
        } elseif ($paymentMethod === 'momo' && $savedOrder) {
            $redirectUrl = (new \App\Services\MomoService())->createPaymentUrl($savedOrder);
            $message = 'Khởi tạo đơn hàng thành công! Đang chuyển sang Cổng thanh toán MoMo Test...';
        }

        if ($this->isAjax()) {
            $this->json([
                'success'      => true,
                'message'      => $message,
                'order_code'   => $code,
                'redirect_url' => $redirectUrl,
            ]);
            return;
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * API: Xác nhận đã chuyển khoản thành công từ phía khách hàng
     * POST /api/don-hang/xac-nhan-chuyen-khoan
     */
    public function confirmPayment(): void
    {
        $this->verifyCsrf();
        $input = $this->getJsonInput() ?: $_POST;
        $orderCode = trim((string)($input['order_code'] ?? ''));

        if (empty($orderCode)) {
            $this->json(['success' => false, 'message' => 'Thiếu mã đơn hàng.'], 422);
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->findByCode($orderCode);
        if (!$order) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
            return;
        }

        $orderModel->updatePaymentStatus($orderCode, 'paid');

        // Gửi email xác nhận đặt hàng thành công chính thức sau khi đã thanh toán (Async Background)
        try {
            \App\Services\Mailer::dispatchAsync($orderCode);
        } catch (\Throwable $e) {
            error_log('Confirm Payment Email error: ' . $e->getMessage());
        }

        $this->json([
            'success'        => true,
            'message'        => 'Hệ thống đã ghi nhận thanh toán chuyển khoản thành công! Đơn hàng đã được xác nhận chính thức.',
            'payment_status' => 'paid',
        ]);
    }

    /** GET /dat-hang-thanh-cong/{code} */
    public function success(string $code): void
    {
        // Cho phép xem nếu là đơn vừa đặt trong phiên hoặc là đơn của user đã đăng nhập
        $orderModel = new Order();
        $order = $orderModel->findByCode($code);
        if (!$order) {
            $this->abort(404, 'Đơn hàng không tồn tại.');
        }

        $user = auth_user();
        $isMyOrder = ($user && (int)($order['user_id'] ?? 0) === (int)$user['id']);
        $isLastOrder = (($_SESSION['last_order'] ?? '') === $code);

        if (!$isMyOrder && !$isLastOrder && empty($_SESSION['admin'])) {
            $this->redirect('/');
        }

        $items = $orderModel->items((int) $order['id']);

        // Sinh dữ liệu VietQR chuẩn Napas 24/7 (ACB - STK 37051817)
        $bankConfig = config('bank', [
            'name'       => 'Ngân hàng TMCP Á Châu (ACB)',
            'short_name' => 'ACB',
            'bin'        => '970416',
            'account_no' => '37051817',
        ]);

        $vietqrUrl = sprintf(
            'https://img.vietqr.io/image/%s-%s-compact2.png?amount=%d&addInfo=%s',
            $bankConfig['bin'] ?? '970416',
            $bankConfig['account_no'] ?? '37051817',
            (int) $order['total'],
            urlencode($order['order_code'])
        );

        $hasEmailArchive = is_file(STORAGE_PATH . '/emails/order-' . $code . '.html');

        $this->render('cart/success', [
            'title'           => 'Đặt hàng thành công #' . $code,
            'order'           => $order,
            'items'           => $items,
            'vietqrUrl'       => $vietqrUrl,
            'bank'            => $bankConfig,
            'hasEmailArchive' => $hasEmailArchive,
        ]);
    }

    /** GET /don-hang/xem-email/{code} */
    public function viewOrderEmail(string $code): void
    {
        $code = preg_replace('/[^a-zA-Z0-9_\-]/', '', $code);
        $file = STORAGE_PATH . '/emails/order-' . $code . '.html';
        if (is_file($file)) {
            header('Content-Type: text/html; charset=UTF-8');
            readfile($file);
            exit;
        }
        $this->abort(404, 'Không tìm thấy bản lưu email của đơn hàng này.');
    }

    /**
     * Phản hồi AJAX kèm trạng thái giỏ mới nhất; form thường thì redirect.
     */
    private function respond(bool $success, string $message, int $status = 200, array $extra = []): never
    {
        if ($this->isAjax()) {
            $summary = $this->cart->summarize();
            $this->json([
                'success' => $success,
                'message' => $message,
                'cart'    => [
                    'count'         => $summary['count'],
                    'subtotal'      => $summary['subtotal'],
                    'discount'      => $summary['discount'],
                    'total'         => $summary['total'],
                    'subtotal_text' => money($summary['subtotal']),
                    'discount_text' => money($summary['discount']),
                    'total_text'    => money($summary['total']),
                    'lines'         => array_map(fn($l) => [
                        'id'              => $l['id'],
                        'name'            => $l['name'],
                        'slug'            => $l['slug'],
                        'model_code'      => $l['model_code'],
                        'thumbnail'       => media($l['thumbnail']),
                        'unit'            => $l['unit'],
                        'qty'             => $l['qty'],
                        'price_type'      => $l['price_type'],
                        'unit_price'      => $l['unit_price'],
                        'unit_price_text' => money($l['unit_price']),
                        'line_total'      => $l['line_total'],
                        'line_total_text' => money($l['line_total']),
                        'need_more'       => $l['need_more'],
                        'url'             => url('/san-pham/' . $l['slug']),
                    ], $summary['lines']),
                ],
            ] + $extra, $status);
        }
        flash($success ? 'success' : 'error', $message);
        $this->back();
    }
}
