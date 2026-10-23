<!-- Footer -->
<footer class="footer">
    <div class="footer__trust">
        <div class="container trust-grid">
            <div class="trust-item"><?= icon('shield') ?><div><strong>100% Chính hãng</strong><span>CO/CQ đầy đủ từ Panasonic Việt Nam</span></div></div>
            <div class="trust-item"><?= icon('truck') ?><div><strong>Giao nhanh 2h – 4h</strong><span>Miễn phí giao hàng đơn từ <?= money(config('free_ship_from')) ?></span></div></div>
            <div class="trust-item"><?= icon('tag') ?><div><strong>Chiết khấu dự án</strong><span>Giá công trình tự động khi mua số lượng lớn</span></div></div>
            <div class="trust-item"><?= icon('award') ?><div><strong>Bảo hành chính hãng</strong><span>1 đổi 1 trong 30 ngày nếu lỗi nhà sản xuất</span></div></div>
        </div>
    </div>

    <div class="container footer__grid">
        <div class="footer__col footer__col--brand">
            <a href="<?= url('/') ?>" class="logo logo--light">
                <span class="logo__mark">Panasonic</span>
                <span class="logo__sub">Thiết Bị Điện Chính Hãng</span>
            </a>
            <p class="footer__desc"><?= e(config('company')) ?> – <?= e(config('tagline')) ?>: công tắc ổ cắm, thiết bị đóng cắt, chiếu sáng LED và giải pháp thông gió.</p>
            <ul class="footer__contact">
                <li><?= icon('map-pin') ?> <span><?= e(config('address')) ?></span></li>
                <li><?= icon('phone') ?> Hotline: <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>"><strong><?= e(config('hotline')) ?></strong></a> &nbsp;|&nbsp; Zalo: <a href="https://zalo.me/<?= e(config('zalo')) ?>" target="_blank" rel="noopener"><strong><?= e(config('zalo')) ?></strong></a></li>
                <li><?= icon('mail') ?> <a href="mailto:<?= e(config('email')) ?>"><?= e(config('email')) ?></a></li>
                <li><?= icon('file-text') ?> MST: <?= e(config('tax_code')) ?></li>
            </ul>
        </div>

        <div class="footer__col">
            <p class="footer__title">Sản phẩm</p>
            <ul>
                <li><a href="<?= url('/danh-muc/cong-tac-o-cam') ?>">Công tắc – Ổ cắm</a></li>
                <li><a href="<?= url('/danh-muc/thiet-bi-dong-cat') ?>">MCB / RCBO / ELCB</a></li>
                <li><a href="<?= url('/danh-muc/den-led-chieu-sang') ?>">Đèn LED chiếu sáng</a></li>
                <li><a href="<?= url('/danh-muc/quat-tran-quat-hut') ?>">Quạt trần & Quạt hút</a></li>
                <li><a href="<?= url('/danh-muc/o-cam-am-san-cong-nghiep') ?>">Ổ cắm âm sàn PCE</a></li>
                <li><a href="<?= url('/danh-muc/day-cap-dien-cadivi') ?>">Dây cáp điện CADIVI</a></li>
            </ul>
        </div>

        <div class="footer__col">
            <p class="footer__title">Dòng Series</p>
            <ul>
                <li><a href="<?= url('/panasonic-wide') ?>">Wide Series</a></li>
                <li><a href="<?= url('/panasonic-moderva') ?>">Moderva</a></li>
                <li><a href="<?= url('/panasonic-gen-x') ?>">Gen-X</a></li>
                <li><a href="<?= url('/panasonic-halumie') ?>">Halumie</a></li>
                <li><a href="<?= url('/panasonic-full-color') ?>">Full Color</a></li>
                <li><a href="<?= url('/panasonic-minerva') ?>">Minerva</a></li>
            </ul>
        </div>

        <div class="footer__col">
            <p class="footer__title">Hỗ trợ dự án</p>
            <ul>
                <li><a href="<?= url('/bao-gia-cong-trinh') ?>">Gửi bảng bóc tách vật tư</a></li>
                <li><a href="<?= url('/tai-catalogue') ?>">Tải Catalogue & Bảng giá</a></li>
                <li><a href="<?= url('/#tra-cuu-mcb') ?>">Tra cứu MCB theo tải</a></li>
                <li><a href="<?= url('/gio-hang') ?>">Dự toán giỏ hàng</a></li>
            </ul>
        </div>
    </div>

    <div class="footer__bottom">
        <div class="container footer__bottom-inner">
            <span>© <?= date('Y') ?> <?= e(config('name')) ?>. Panasonic® là thương hiệu đã đăng ký của Panasonic Holdings Corporation.</span>
            <span><?= e(config('working_hours')) ?></span>
        </div>
    </div>
</footer>

<!-- Floating contact (mobile) -->
<div class="fab">
    <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" class="fab__btn fab__btn--call" aria-label="Gọi hotline"><?= icon('phone') ?></a>
    <a href="https://zalo.me/<?= e(config('zalo')) ?>" class="fab__btn fab__btn--zalo" target="_blank" rel="noopener" aria-label="Chat Zalo">Zalo</a>
</div>
<button class="back-top" type="button" aria-label="Lên đầu trang" data-back-top><?= icon('chevron-down') ?></button>

<!-- Toast container -->
<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>

<!-- Thanh Giỏ Hàng Trượt Bên Phải (Right Offcanvas Cart Drawer) -->
<?php require VIEW_PATH . '/partials/cart_drawer.php'; ?>

<script src="<?= asset('js/app.js') ?>" defer></script>
<script src="<?= asset('js/quicksearch.js') ?>" defer></script>
<script src="<?= asset('js/cart.js') ?>" defer></script>
<script src="<?= asset('js/auth.js') ?>" defer></script>
</body>
</html>
