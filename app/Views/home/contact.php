<?php
/** @var string $title */
/** @var string $metaDescription */
?>

<div class="container contact-page-wrap" style="padding-top: 1.5rem; padding-bottom: 3.5rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Đường dẫn">
        <a href="<?= url('/') ?>">Trang chủ</a>
        <span>/</span>
        <span aria-current="page">Liên hệ & Bản đồ</span>
    </nav>

    <!-- Page Header -->
    <div class="contact-page-head" style="margin-bottom: 2.25rem;">
        <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: #e0f2fe; color: #004098; font-size: 0.825rem; font-weight: 800; padding: 4px 12px; border-radius: 99px; text-transform: uppercase; margin-bottom: 0.75rem;">
            <?= icon('map-pin') ?> HỆ THỐNG KHO & CHI NHÁNH CHÍNH HÃNG
        </span>
        <h1 style="font-size: 2rem; font-weight: 900; color: #004098; margin: 0 0 0.85rem; letter-spacing: -0.5px;">
            LIÊN HỆ & BẢN ĐỒ TỔNG KHO THIẾT BỊ ĐIỆN PANASONIC (TP.HCM & LONG AN)
        </h1>
        <p style="font-size: 1.05rem; line-height: 1.6; color: var(--slate-600); max-width: 850px; margin: 0;">
            Tổng kho phân phối thiết bị điện Panasonic chính hãng cam kết cung cấp hàng hóa đầy đủ CO/CQ, VAT, bảo hành 24 tháng và giao hàng hỏa tốc trong 2h – 4h tới mọi công trình tại <strong>TP. Hồ Chí Minh</strong> và <strong>Tỉnh Long An</strong>.
        </p>
    </div>

    <!-- Khối thẻ thông tin liên hệ 3 cột -->
    <div class="contact-info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <!-- Thẻ Trụ Sở & Tổng Kho -->
        <div class="contact-card" style="background: #ffffff; border: 1.5px solid #004098; border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: 0 8px 24px rgba(0,64,152,0.08); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; background: #004098; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-bottom-left-radius: 8px; text-transform: uppercase;">
                Trụ Sở Chính
            </div>
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1rem;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: #e0f2fe; color: #004098; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <?= icon('building') ?>
                </div>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0;">TỔNG KHO PHÂN PHỐI</h3>
                    <small style="color: var(--slate-500); font-weight: 600;">Cung cấp thiết bị điện & dây cáp cho dự án</small>
                </div>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0 0 1.25rem; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.925rem; color: var(--slate-700);">
                <li><strong><?= icon('map-pin') ?> Địa chỉ:</strong> <?= e(config('address')) ?></li>
                <li><strong><?= icon('phone') ?> Hotline dự án:</strong> <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" style="color: #004098; font-weight: 800; text-decoration: none;"><?= e(config('hotline')) ?></a></li>
                <li><strong><?= icon('chat') ?> Zalo kỹ thuật:</strong> <a href="https://zalo.me/<?= e(config('zalo')) ?>" target="_blank" style="color: #0284c7; font-weight: 700; text-decoration: none;"><?= e(config('zalo')) ?> (Hỗ trợ 24/7)</a></li>
                <li><strong><?= icon('mail') ?> Email:</strong> <a href="mailto:<?= e(config('email')) ?>" style="color: #004098; font-weight: 700; text-decoration: none;"><?= e(config('email')) ?></a></li>
                <li><strong><?= icon('clock') ?> Thời gian:</strong> <?= e(config('working_hours')) ?></li>
            </ul>
            <button type="button" onclick="switchMap('tayninh')" style="width: 100%; padding: 0.65rem; background: #004098; color: #fff; border: none; border-radius: var(--radius-md); font-weight: 700; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                <?= icon('map-pin') ?> Xem vị trí bản đồ Tây Ninh
            </button>
        </div>

        <!-- Thẻ Kho Hàng Điều Phối -->
        <div class="contact-card" style="background: #ffffff; border: 1.5px solid #059669; border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: 0 8px 24px rgba(5,150,105,0.08); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; background: #059669; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-bottom-left-radius: 8px; text-transform: uppercase;">
                Giao Hàng Dự Án
            </div>
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1rem;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <?= icon('truck') ?>
                </div>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #059669; margin: 0;">GIAO NHANH CHÂN CÔNG TRÌNH</h3>
                    <small style="color: var(--slate-500); font-weight: 600;">Hỏa tốc trong 2h – 4h tới mọi công trình</small>
                </div>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0 0 1.25rem; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.925rem; color: var(--slate-700);">
                <li><strong><?= icon('map-pin') ?> Điểm kho:</strong> <?= e(config('address')) ?></li>
                <li><strong><?= icon('truck') ?> Phạm vi giao:</strong> Tây Ninh, TP.HCM, Long An và toàn quốc</li>
                <li><strong><?= icon('phone') ?> Hotline điều xe:</strong> <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" style="color: #059669; font-weight: 800; text-decoration: none;"><?= e(config('hotline')) ?></a></li>
                <li><strong><?= icon('clock') ?> Thời gian:</strong> 7:30 – 18:30 (Giao nhanh mọi ngày)</li>
            </ul>
            <a href="https://zalo.me/<?= e(config('zalo')) ?>" target="_blank" rel="noopener" style="width: 100%; padding: 0.65rem; background: #059669; color: #fff; border: none; border-radius: var(--radius-md); font-weight: 700; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem; text-decoration: none;">
                <?= icon('chat') ?> Nhắn Zalo đặt xe giao hàng
            </a>
        </div>

        <!-- Thẻ Báo Giá & Hỗ Trợ Dự Án -->
        <div class="contact-card" style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1rem;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <?= icon('file-text') ?>
                </div>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0;">BÁO GIÁ & HỒ SƠ THẦU</h3>
                    <small style="color: var(--slate-500); font-weight: 600;">Tiếp nhận bóc tách khối lượng dự toán</small>
                </div>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0 0 1.25rem; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.925rem; color: var(--slate-700);">
                <li><strong><?= icon('mail') ?> Email dự án:</strong> <a href="mailto:<?= e(config('email')) ?>" style="color: #004098; font-weight: 700;"><?= e(config('email')) ?></a></li>
                <li><strong><?= icon('shield') ?> Cam kết chất lượng:</strong> 100% chính hãng Panasonic & CADIVI có CO/CQ, VAT</li>
                <li><strong><?= icon('tag') ?> Mức chiết khấu:</strong> 15% – 35% theo bậc thang khối lượng</li>
                <li><strong><?= icon('clock') ?> Trả kết quả:</strong> Trong 30 phút – 2 giờ làm việc</li>
            </ul>
            <a href="<?= url('/bao-gia-cong-trinh') ?>" style="width: 100%; padding: 0.65rem; background: #e0f2fe; color: #004098; border: 1.5px solid #004098; border-radius: var(--radius-md); font-weight: 800; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                <?= icon('upload') ?> Gửi bảng bóc tách vật tư »
            </a>
        </div>
    </div>

    <!-- KHỐI BẢN ĐỒ GOOGLE MAPS TƯƠNG TÁC & FORM LIÊN HỆ -->
    <div style="display: grid; grid-template-columns: 1.25fr 0.75fr; gap: 2rem; align-items: start;">
        <!-- Cột Trái: Google Maps tương tác -->
        <div style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                <h3 id="currentMapTitle" style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <?= icon('map-pin') ?> Bản Đồ: Tổng Kho Tây Ninh
                </h3>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" id="btnMapTayninh" onclick="switchMap('tayninh')" style="padding: 5px 14px; border-radius: 99px; border: none; background: #004098; color: #fff; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                        Kho Tây Ninh
                    </button>
                    <button type="button" id="btnMapTphcm" onclick="switchMap('tphcm')" style="padding: 5px 14px; border-radius: 99px; border: 1px solid var(--slate-300); background: #f8fafc; color: var(--slate-700); font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                        Kho TP.HCM
                    </button>
                </div>
            </div>

            <!-- Iframe Bản đồ Google Maps -->
            <div style="position: relative; width: 100%; aspect-ratio: 16/10; min-height: 400px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--slate-200);">
                <iframe id="googleMapIframe" 
                        src="https://www.google.com/maps?q=Ấp+Long+Khánh,+Phước+Lý,+Tây+Ninh&output=embed" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div id="mapAddressDesc" style="margin-top: 1rem; font-size: 0.875rem; color: var(--slate-600); display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('map-pin') ?> <span><strong>Vị trí kho chính:</strong> Ấp Long Khánh, Phước Lý, Tây Ninh.</span>
            </div>
        </div>

        <!-- Cột Phải: Form gửi liên hệ nhanh -->
        <div style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #004098; margin: 0 0 0.5rem;">
                GỬI YÊU CẦU TƯ VẤN & BÁO GIÁ
            </h3>
            <p style="font-size: 0.875rem; color: var(--slate-600); margin: 0 0 1.25rem; line-height: 1.5;">
                Điền thông tin bên dưới, kỹ sư Panasonic tại TP.HCM & Long An sẽ gọi lại tư vấn và gửi báo giá chiết khấu ưu đãi nhất.
            </p>

            <form action="<?= url('/lien-he') ?>" method="post">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 0.35rem;">
                        Họ và tên của Quý khách <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Ví dụ: Nguyễn Văn A" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--slate-300); border-radius: var(--radius-md); font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 0.35rem;">
                        Số điện thoại / Zalo <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="tel" name="phone" required placeholder="Ví dụ: 0912 345 678" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--slate-300); border-radius: var(--radius-md); font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 0.35rem;">
                        Email tiếp nhận báo giá
                    </label>
                    <input type="email" name="email" placeholder="example@company.com" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--slate-300); border-radius: var(--radius-md); font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 0.35rem;">
                        Khu vực công trình của bạn
                    </label>
                    <select name="address" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--slate-300); border-radius: var(--radius-md); font-size: 0.9rem; background: #fff;">
                        <option value="TP. Hồ Chí Minh">Khu vực TP. Hồ Chí Minh (Nội thành & Ngoại thành)</option>
                        <option value="Long An - Bến Lức">Khu vực Bến Lức, Long An (Waterpoint, các KCN)</option>
                        <option value="Long An - Cần Giuộc">Khu vực Cần Giuộc, Long An (KCN Long Hậu, T&T Millennia)</option>
                        <option value="Long An - Đức Hòa">Khu vực Đức Hòa, Long An (KCN Tân Đức, Hải Sơn)</option>
                        <option value="Tỉnh thành khác">Tỉnh thành lân cận khác</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 0.35rem;">
                        Sản phẩm / Khối lượng dự án cần hỗ trợ
                    </label>
                    <textarea name="message" rows="3" placeholder="Ví dụ: Báo giá 200 bộ công tắc Moderva, 50 aptomat MCB 2P 32A và đèn LED Downlight cho công trình căn hộ..." style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--slate-300); border-radius: var(--radius-md); font-size: 0.9rem; resize: vertical;"></textarea>
                </div>

                <button type="submit" style="width: 100%; padding: 0.85rem; background: #004098; color: #fff; border: none; border-radius: var(--radius-md); font-size: 0.95rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(0,64,152,0.25);">
                    <?= icon('arrow-right') ?> GỬI YÊU CẦU LIÊN HỆ NGAY
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function switchMap(target) {
    const iframe = document.getElementById('googleMapIframe');
    const title = document.getElementById('currentMapTitle');
    const desc = document.getElementById('mapAddressDesc');
    const btnTayninh = document.getElementById('btnMapTayninh');
    const btnTphcm = document.getElementById('btnMapTphcm');

    if (target === 'tphcm') {
        iframe.src = "https://www.google.com/maps?q=4491+Nguyen+Cuu+Phu,+Tan+Tao+A,+Binh+Tan,+TP+Ho+Chi+Minh&output=embed";
        title.innerHTML = '<?= icon('map-pin') ?> Bản Đồ: Điểm Giao Hàng TP. Hồ Chí Minh';
        desc.innerHTML = '<?= icon('map-pin') ?> <span><strong>Điểm giao TP.HCM:</strong> 4491 Nguyễn Cửu Phú, P. Tân Tạo A, Quận Bình Tân, TP. Hồ Chí Minh.</span>';
        
        btnTphcm.style.background = '#004098';
        btnTphcm.style.color = '#fff';
        btnTphcm.style.border = 'none';

        btnTayninh.style.background = '#f8fafc';
        btnTayninh.style.color = '#334155';
        btnTayninh.style.border = '1px solid #cbd5e1';
    } else {
        iframe.src = "https://www.google.com/maps?q=Ấp+Long+Khánh,+Phước+Lý,+Tây+Ninh&output=embed";
        title.innerHTML = '<?= icon('map-pin') ?> Bản Đồ: Tổng Kho Tây Ninh';
        desc.innerHTML = '<?= icon('map-pin') ?> <span><strong>Vị trí kho chính:</strong> Ấp Long Khánh, Phước Lý, Tây Ninh.</span>';
        
        btnTayninh.style.background = '#004098';
        btnTayninh.style.color = '#fff';
        btnTayninh.style.border = 'none';

        btnTphcm.style.background = '#f8fafc';
        btnTphcm.style.color = '#334155';
        btnTphcm.style.border = '1px solid #cbd5e1';
    }
}
</script>
