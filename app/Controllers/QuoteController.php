<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\Quote;
use App\Services\Cart;
use App\Services\Uploader;
use Core\Controller;

class QuoteController extends Controller
{
    /** GET /bao-gia-cong-trinh */
    public function form(): void
    {
        $this->render('quotes/request_form', [
            'title'           => 'Gửi bảng bóc tách vật tư – Báo giá công trình | ' . config('name'),
            'metaDescription' => 'Gửi file dự toán, bảng bóc tách vật tư (Excel/PDF) để nhận báo giá thiết bị điện Panasonic với chiết khấu công trình tốt nhất trong 2 giờ làm việc.',
            'customerTypes'   => Quote::CUSTOMER_TYPES,
            'cartCount'       => (new Cart())->count(),
        ]);
    }

    /** POST /bao-gia-cong-trinh – nhận cả form thường & AJAX */
    public function submit(): void
    {
        $this->verifyCsrf();

        $data = [
            'customer_name' => mb_substr($this->input('customer_name'), 0, 150),
            'company'       => mb_substr($this->input('company'), 0, 200),
            'customer_type' => $this->input('customer_type', 'contractor'),
            'phone'         => preg_replace('/[\s\.\-]/', '', $this->input('phone')),
            'email'         => mb_substr($this->input('email'), 0, 150),
            'project_name'  => mb_substr($this->input('project_name'), 0, 255),
            'address'       => mb_substr($this->input('address'), 0, 255),
            'note'          => mb_substr($this->input('note'), 0, 3000),
        ];

        $errors = $this->validate($data);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Honeypot – bot thường điền mọi trường
        if ($this->input('website') !== '') {
            $this->finish(true, 'Cảm ơn bạn! Chúng tôi sẽ liên hệ sớm.');
        }

        $quoteModel = new Quote();
        if (!$errors && $quoteModel->recentCountByIp($ip, 10) >= 3) {
            $errors['general'] = 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau 10 phút hoặc gọi hotline ' . config('hotline') . '.';
        }

        $hasFile = isset($_FILES['bom_file']) && ($_FILES['bom_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $includeCart = $this->input('include_cart') === '1';

        if (!$hasFile && $data['note'] === '' && !$includeCart) {
            $errors['bom_file'] = 'Vui lòng đính kèm file bóc tách vật tư hoặc ghi chú danh sách thiết bị cần báo giá.';
        }

        if (!$errors && $hasFile) {
            try {
                $stored = Uploader::store(
                    $_FILES['bom_file'],
                    STORAGE_PATH . '/boms',
                    config('bom_extensions'),
                    (int) config('bom_max_size'),
                    'bom-'
                );
                $data['file_bom_path'] = $stored['path'];
                $data['file_original'] = $stored['original'];
            } catch (\RuntimeException $e) {
                $errors['bom_file'] = $e->getMessage();
            }
        }

        if ($errors) {
            $this->finish(false, reset($errors), $errors, $data);
        }

        // Đính kèm danh sách từ giỏ hàng (nếu khách chọn)
        if ($includeCart) {
            $summary = (new Cart())->summarize();
            if ($summary['lines']) {
                $lines = array_map(
                    fn($l) => sprintf('- %s (%s) x %d %s', $l['name'], $l['model_code'], $l['qty'], $l['unit']),
                    $summary['lines']
                );
                $data['note'] = trim($data['note'] . "\n\n[Danh sách từ giỏ hàng]\n" . implode("\n", $lines));
            }
        }

        $data['status']     = 'new';
        $data['ip_address'] = $ip;
        $data['email']      = $data['email'] ?: null;

        $id = $quoteModel->create($data);
        clear_old();

        $this->finish(true, sprintf(
            'Đã tiếp nhận yêu cầu #%d. Kỹ sư dự án sẽ liên hệ %s trong vòng 2 giờ làm việc.',
            $id,
            $data['phone']
        ));
    }

    /** GET /tai-catalogue */
    public function catalogue(): void
    {
        $master = config('catalog_master');

        $this->render('quotes/catalogue', [
            'title'           => 'Tải Catalogue & Bảng giá Panasonic 2026 | ' . config('name'),
            'metaDescription' => 'Tải miễn phí Catalogue, bảng giá niêm yết, datasheet thông số kỹ thuật thiết bị điện Panasonic 2026 (PDF).',
            'master'          => [
                'url'    => $master,
                'exists' => is_file(PUBLIC_PATH . '/' . $master),
                'size'   => is_file(PUBLIC_PATH . '/' . $master) ? filesize(PUBLIC_PATH . '/' . $master) : 0,
            ],
            'series'          => (new ProductSeries())->active(),
            'catalogs'        => (new Product())->catalogs(),
        ]);
    }

    private function validate(array $d): array
    {
        $errors = [];
        if (mb_strlen($d['customer_name']) < 2) {
            $errors['customer_name'] = 'Vui lòng nhập họ tên (tối thiểu 2 ký tự).';
        }
        if (!preg_match('/^(0|\+84)(3|5|7|8|9|2)\d{8}$/', $d['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ (VD: 0909123456).';
        }
        if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }
        if (!isset(Quote::CUSTOMER_TYPES[$d['customer_type']])) {
            $errors['customer_type'] = 'Loại khách hàng không hợp lệ.';
        }
        return $errors;
    }

    /**
     * Phản hồi thống nhất cho AJAX (JSON) và form thường (redirect + flash).
     */
    private function finish(bool $success, string $message, array $errors = [], array $old = []): never
    {
        if ($this->isAjax()) {
            $this->json(['success' => $success, 'message' => $message, 'errors' => $errors], $success ? 200 : 422);
        }
        if (!$success) {
            set_old($old);
        }
        flash($success ? 'success' : 'error', $message);
        $this->redirect('/bao-gia-cong-trinh');
    }
}
