<?php
/**
 * Cấu hình ứng dụng.
 */
return [
    'name'          => 'Thiết Bị Điện Panasonic Việt Nam',
    'company'       => 'Kho Phân Phối Thiết Bị Điện Panasonic Việt Nam',
    'tagline'       => 'Tổng kho dự án & Đại lý phân phối chính hãng',
    'hotline'       => '0838.484.885',
    'hotline_sales' => '0838.484.885',
    'zalo'          => '0838484885',
    'email'         => 'ntphau21@gmail.com',
    'address'       => 'Ấp Long Khánh, Phước Lý, Tây Ninh',
    'working_hours' => 'Thứ 2 – Chủ Nhật: 7:30 – 21:00',
    'tax_code'      => '0108669589',

    // Bật khi phát triển, TẮT khi chạy thật
    'debug'         => true,
    'timezone'      => 'Asia/Ho_Chi_Minh',

    // Bỏ trống để tự nhận diện base URL (hoạt động cả trong thư mục con của htdocs)
    'base_url'      => '',

    // Catalogue tổng 2026
    'catalog_master' => 'uploads/catalogs/panasonic-catalogue-2026.pdf',

    // Upload
    'bom_max_size'   => 10 * 1024 * 1024, // 10MB
    'bom_extensions' => ['xlsx', 'xls', 'csv', 'pdf', 'dwg', 'zip'],
    'image_max_size' => 3 * 1024 * 1024,

    'per_page'       => 12,
    'free_ship_from' => 2000000,

    // Cấu hình gửi Mail thông báo tự động (Gmail SMTP)
    'mail' => [
        'driver'                   => 'smtp',
        'host'                     => 'smtp.gmail.com',
        'port'                     => 587,
        'encryption'               => 'tls',
        'username'                 => 'ntphau21@gmail.com',
        'password'                 => 'yoaf gjxs nyqv heso', // Mật khẩu ứng dụng Gmail (App Password)
        'from_name'                => 'Thiết Bị Điện Panasonic Việt Nam',
        'from_email'               => 'ntphau21@gmail.com',
        'admin_notification_email' => 'ntphau21@gmail.com',
    ],

    // Social Login OAuth Config (Google & Facebook)
    'google_client_id' => '70678187265-3reck9eq3mgb710bvo4mh272qgl5odmq.apps.googleusercontent.com',
    'facebook_app_id'  => '123456789012345',

    // Thông tin tài khoản ngân hàng chuyển khoản VietQR Napas 24/7
    'bank' => [
        'name'         => 'Ngân hàng TMCP Á Châu (ACB)',
        'short_name'   => 'ACB',
        'bin'          => '970416',
        'account_no'   => '37051817',
        'branch'       => 'Chi nhánh TP.HCM',
    ],

    // Cấu hình Cổng Thanh Toán VNPAY (Môi trường Doanh Nghiệp Sandbox Test)
    'vnpay' => [
        'tmn_code'    => 'CGXZLS0Z',
        'hash_secret' => 'XNBCJFAKAZQSGTARRLTXCVPFOHVUXHYW',
        'url'         => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
    ],

    // Cấu hình Cổng Thanh Toán Ví MoMo (Môi trường Doanh Nghiệp Sandbox Test)
    'momo' => [
        'partner_code' => 'MOMO5RGX20191128',
        'access_key'   => 'klm05TvNBzhg7h7j',
        'secret_key'   => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2Aca',
        'endpoint'     => 'https://test-payment.momo.vn/v2/gateway/api/create',
    ],
];
