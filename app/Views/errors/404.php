<?php
/** @var string $title */
/** @var string|null $message */
?>
<div class="container section">
    <div class="empty-state">
        <span class="empty-state__icon text-danger"><?= icon('alert') ?></span>
        <h1 class="page-title"><?= e($title) ?></h1>
        <p class="page-desc"><?= e($message ?: 'Nội dung hoặc trang bạn đang tìm kiếm không tồn tại, đã đổi tên hoặc tạm thời không khả dụng.') ?></p>
        <div class="mt-4">
            <a href="<?= url('/') ?>" class="btn btn--primary"><?= icon('home') ?> Quay lại trang chủ</a>
            <a href="<?= url('/san-pham') ?>" class="btn btn--outline ml-2"><?= icon('grid') ?> Xem sản phẩm</a>
        </div>
    </div>
</div>
