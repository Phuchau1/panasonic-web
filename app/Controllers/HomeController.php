<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\Project;
use Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $product = new Product();

        $sections = [
            [
                'id'       => 'downlight',
                'title'    => 'ĐÈN DOWNLIGHT PANASONIC',
                'link'     => url('/danh-muc/den-led-chieu-sang?q=downlight'),
                'bullets'  => [
                    'Đèn âm trần Color Rim đổi màu 3 chế độ & Neo Slim 2G viền vàng/bạc siêu mỏng',
                    'Tiết kiệm 80% điện năng, chip LED Panasonic tuổi thọ 25.000 giờ không suy giảm quang thông',
                ],
                'items'    => $product->getSection('downlight', 5),
            ],
            [
                'id'       => 'lighting_other',
                'title'    => 'ĐÈN BÀN HỌC & ĐÈN TRẦN PANASONIC',
                'link'     => url('/danh-muc/den-led-chieu-sang'),
                'bullets'  => [
                    'Đèn bàn LED chống cận thị chuẩn RG0, cảm ứng đa cấp độ sáng bảo vệ mắt học sinh',
                    'Đèn Panel văn phòng 600×600 và đèn gắn tường trang trí ngoài trời chống nước IP65',
                ],
                'items'    => $product->getSection('lighting_other', 5),
            ],
            [
                'id'       => 'filament',
                'title'    => 'BÓNG ĐÈN FILAMENT & CHIẾU SÁNG TRANG TRÍ',
                'link'     => url('/danh-muc/den-led-chieu-sang?q=filament'),
                'bullets'  => [
                    'Bóng LED Filament thủy tinh Edison phong cách cổ điển đui xoắn E27 / E14',
                    'Góc chiếu 360 độ, chỉ số hoàn màu CRI > 80 cho không gian cafe, nhà hàng, biệt thự',
                ],
                'items'    => $product->getSection('filament', 5),
            ],
            [
                'id'       => 'switches',
                'title'    => 'CÔNG TẮC – Ổ CẮM PANASONIC',
                'link'     => url('/danh-muc/cong-tac-o-cam'),
                'bullets'  => [
                    'Đầy đủ các dòng cao cấp Moderva, Minerva, Wide Series, Halumie, Gen-X',
                    'Tiếp điểm hợp kim bạc chịu tải 16AX, vật liệu Urea Resin chống cháy và chống phóng điện',
                ],
                'items'    => $product->getSection('switches', 5),
            ],
            [
                'id'       => 'breakers',
                'title'    => 'THIẾT BỊ ĐÓNG CẮT (MCB, MCCB, RCBO & TỦ ĐIỆN)',
                'link'     => url('/danh-muc/thiet-bi-dong-cat'),
                'bullets'  => [
                    'Aptomat MCB 1P-4P dòng cắt 6kA, MCCB công nghiệp 35kA – 50kA tiêu chuẩn IEC 60947-2',
                    'Aptomat chống giật RCBO/ELB 30mA, tủ điện âm tường kim loại mặt kính cao cấp',
                ],
                'items'    => $product->getSection('breakers', 5),
            ],
            [
                'id'       => 'fans',
                'title'    => 'QUẠT TRẦN & QUẠT HÚT THÔNG GIÓ PANASONIC',
                'link'     => url('/danh-muc/quat-tran-quat-hut'),
                'bullets'  => [
                    'Quạt trần 5 cánh DC Inverter thế hệ mới, công nghệ tạo gió tự nhiên 1/f Yuragi',
                    'Quạt hút âm trần nối ống gió & quạt chắn gió cửa ra vào vận hành êm ái, bền bỉ',
                ],
                'items'    => $product->getSection('fans', 5),
            ],
            [
                'id'       => 'industrial',
                'title'    => 'PHÍCH CẮM & Ổ CẮM CÔNG NGHIỆP PCE',
                'link'     => url('/danh-muc/o-cam-am-san-cong-nghiep'),
                'bullets'  => [
                    'Tiêu chuẩn quốc tế IEC 60309-1/2, cấp bảo vệ kín nước IP44 & IP67',
                    'Chịu nhiệt độ từ -25°C đến 80°C, chống ăn mòn hóa chất và va đập cơ học cực cao',
                ],
                'items'    => $product->getSection('industrial', 5),
            ],
            [
                'id'       => 'pumps',
                'title'    => 'MÁY BƠM NƯỚC & BÌNH NÓNG LẠNH PANASONIC',
                'link'     => url('/danh-muc/may-bom-panasonic'),
                'bullets'  => [
                    'Máy bơm tăng áp điện tử, bơm đẩy cao công suất 200W – lưu lượng 45 lít/phút',
                    'Bình nước nóng gián tiếp ruột bình tráng men Titanium bảo vệ an toàn 9 cấp độ',
                ],
                'items'    => $product->getSection('pumps', 5),
            ],
            [
                'id'       => 'cadivi',
                'title'    => 'DÂY & CÁP ĐIỆN CADIVI CHÍNH HÃNG (VCm, VCmd, CV, CXV)',
                'link'     => url('/danh-muc/day-cap-dien-cadivi'),
                'bullets'  => [
                    'Ruột đồng tinh chất 99.99%, vỏ bọc PVC/XLPE chống cháy lan chuẩn TCVN / IEC',
                    'Đầy đủ chủng loại dân dụng và công trình: VCm đơn mềm, VCmd dẹp, cáp điện lực CV, cáp ngầm CXV',
                ],
                'items'    => $product->getSection('cadivi', 5),
            ],
        ];

        $this->render('home/index', [
            'title'           => 'THIẾT BỊ ĐIỆN PANASONIC VIỆT NAM | TỔNG KHO DỰ ÁN & ĐẠI LÝ',
            'metaDescription' => 'Tổng phân phối thiết bị điện Panasonic chính hãng: Công tắc ổ cắm, Đèn LED Downlight, MCB/RCBO, Quạt trần, Ổ cắm PCE. Báo giá công trình chiết khấu cao nhất.',
            'categories'      => (new Category())->active(),
            'series'          => (new ProductSeries())->active(),
            'sections'        => $sections,
            'featured'        => $product->featured(10),
            'breakers'        => $product->breakers(),
            'projects'        => (new Project())->featured(10),
        ]);
    }

    public function projects(): void
    {
        $projectModel = new Project();
        $allProjects = $projectModel->featured(20);

        $this->render('projects/index', [
            'title'           => 'Dự Án Tiêu Biểu Thiết Bị Điện Panasonic | TP.HCM & Long An',
            'metaDescription' => 'Tổng hợp các công trình và đại dự án tiêu biểu cung cấp 100% thiết bị điện Panasonic chính hãng tại TP. Hồ Chí Minh và Long An: Landmark 81, Waterpoint, Masterise, Vạn Phúc City, T&T Millennia, Mizuki Park, KCN Long Hậu.',
            'projects'        => $allProjects,
        ]);
    }

    public function projectDetail(string $slug): void
    {
        $projectModel = new Project();
        $project = $projectModel->findBySlug($slug);

        if (!$project) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Không tìm thấy bài viết dự án'], 'default');
            return;
        }

        $related = $projectModel->featured(4);

        $this->render('projects/detail', [
            'title'           => $project['title'] . ' | Dự Án Thiết Bị Điện Panasonic',
            'metaDescription' => 'Bài viết thực tế thi công lắp đặt thiết bị điện Panasonic chính hãng tại ' . $project['title'] . ' (' . $project['location'] . ').',
            'project'         => $project,
            'related'         => $related,
        ]);
    }

    public function contact(): void
    {
        $this->render('home/contact', [
            'title'           => 'Liên Hệ & Bản Đồ Tổng Kho Thiết Bị Điện Panasonic | TP.HCM & Long An',
            'metaDescription' => 'Thông tin liên hệ, bản đồ Google Maps và địa chỉ tổng kho thiết bị điện Panasonic tại TP. Hồ Chí Minh và Long An. Hotline tư vấn báo giá dự án 24/7.',
        ]);
    }

    public function submitContact(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Phiên làm việc hết hạn, vui lòng gửi lại.');
            header('Location: ' . url('/lien-he'));
            return;
        }

        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$name || !$phone) {
            flash('error', 'Vui lòng nhập họ tên và số điện thoại liên hệ.');
            header('Location: ' . url('/lien-he'));
            return;
        }

        try {
            (new \App\Models\Contact())->create([
                'name'       => $name,
                'phone'      => $phone,
                'email'      => $email,
                'address'    => $address,
                'message'    => $message ?: 'Khách hàng yêu cầu liên hệ tư vấn thiết bị điện.',
                'status'     => 'new',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);
        } catch (\Throwable $e) {}

        flash('success', "Cảm ơn Quý khách {$name}! Bộ phận kỹ sư dự án Panasonic tại TP.HCM & Long An đã tiếp nhận thông tin và sẽ liên hệ hỗ trợ trong vòng 15 phút.");
        header('Location: ' . url('/lien-he'));
    }

    /**
     * Tra cứu hành trình đơn hàng & đơn vị vận chuyển
     * GET/POST /tra-cuu-don-hang
     */
    public function trackOrder(): void
    {
        $query = trim((string)($this->input('q') ?? $this->input('code') ?? ''));
        $order = null;
        $items = [];
        $carrierInfo = null;
        $trackingUrl = null;
        $searched = false;

        if ($query !== '') {
            $searched = true;
            $orderModel = new \App\Models\Order();
            $order = $orderModel->findByTrackingOrCode($query);
            if ($order) {
                $items = $orderModel->items((int)$order['id']);
                $carrierInfo = \App\Models\Order::getCarrierInfo($order['shipping_carrier'] ?? null);
                $trackingUrl = \App\Models\Order::getTrackingUrl($order['shipping_carrier'] ?? null, $order['tracking_code'] ?? null);
            }
        }

        $this->render('home/tracking', [
            'title'       => 'Tra cứu đơn hàng & Hành trình vận chuyển | Thiết bị điện Panasonic',
            'query'       => $query,
            'searched'    => $searched,
            'order'       => $order,
            'items'       => $items,
            'carrierInfo' => $carrierInfo,
            'trackingUrl' => $trackingUrl,
            'carriers'    => \App\Models\Order::CARRIERS,
        ]);
    }
}
