<?php
/** @var array $stats */
/** @var array $latestQuotes */
/** @var array $latestOrders */
/** @var array $latestUsers */
/** @var array $latestReviews */
?>

<!-- Hàng thống kê tổng quan (8 Cards Đồng Bộ Toàn Bộ Web) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('grid') ?> Sản phẩm đang bán</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #004098;"><?= (int)$stats['products'] ?></div>
        <div style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">(<?= (int)$stats['low_stock'] ?> sắp hết hàng)</div>
        <a href="<?= url('/admin/san-pham') ?>" style="font-size: 0.75rem; color: #004098; margin-top: 0.35rem; display: block;">Quản lý kho &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('cart') ?> Đơn hàng chờ xử lý</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #d97706;"><?= (int)$stats['pending'] ?></div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Tổng đơn: <?= (int)$stats['orders'] ?></div>
        <a href="<?= url('/admin/don-hang?status=pending') ?>" style="font-size: 0.75rem; color: #d97706; margin-top: 0.35rem; display: block;">Xử lý đơn ngay &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('upload') ?> Báo giá BOM công trình</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #059669;"><?= (int)$stats['new_quotes'] ?></div>
        <div style="font-size: 0.75rem; color: #059669; margin-top: 0.25rem;">Yêu cầu mới gửi</div>
        <a href="<?= url('/admin/bao-gia?status=new') ?>" style="font-size: 0.75rem; color: #059669; margin-top: 0.35rem; display: block;">Xem bóc tách vật tư &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('award') ?> Doanh số hoàn tất</div>
        <div style="font-size: 1.6rem; font-weight: 700; color: #0f172a;"><?= money($stats['revenue']) ?></div>
        <div style="font-size: 0.75rem; color: #059669; margin-top: 0.25rem;">Doanh thu thực tế</div>
        <a href="<?= url('/admin/don-hang?status=completed') ?>" style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block;">Xem lịch sử &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('users') ?> Khách hàng & Thành viên</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #2563eb;"><?= (int)$stats['users'] ?></div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Tài khoản Web / Google / FB</div>
        <a href="<?= url('/admin/thanh-vien') ?>" style="font-size: 0.75rem; color: #2563eb; margin-top: 0.35rem; display: block;">Danh sách thành viên &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('building') ?> Dự án tiêu biểu</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #7c3aed;"><?= (int)$stats['projects'] ?></div>
        <div style="font-size: 0.75rem; color: #7c3aed; margin-top: 0.25rem;">Hồ sơ TP.HCM & Long An</div>
        <a href="<?= url('/admin/du-an') ?>" style="font-size: 0.75rem; color: #7c3aed; margin-top: 0.35rem; display: block;">Quản lý dự án &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('star') ?> Đánh giá sản phẩm</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: #eab308;"><?= (int)$stats['reviews'] ?></div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Đánh giá thực tế từ khách</div>
        <a href="<?= url('/admin/danh-gia') ?>" style="font-size: 0.75rem; color: #ca8a04; margin-top: 0.35rem; display: block;">Kiểm duyệt đánh giá &rarr;</a>
    </div>

    <div class="admin-card">
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;"><?= icon('settings') ?> Cấu hình Website</div>
        <div style="font-size: 1.1rem; font-weight: 700; color: #004098; margin-top: 0.5rem;"><?= e(config('hotline')) ?></div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Tổng kho phân phối</div>
        <a href="<?= url('/admin/cai-dat') ?>" style="font-size: 0.75rem; color: #004098; margin-top: 0.35rem; display: block;">Thay đổi cài đặt &rarr;</a>
    </div>
</div>

<!-- Lưới 2 Cột: Báo Giá & Đơn Hàng -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Bảng Báo Giá Mới Nhất -->
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;"><?= icon('upload') ?> Báo giá công trình mới nhất</h3>
            <a href="<?= url('/admin/bao-gia') ?>" style="font-size: 0.85rem;">Tất cả &rarr;</a>
        </div>
        <div class="table-scroll">
            <table class="spec-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Dự án / SĐT</th>
                        <th>File BOM</th>
                        <th>T/Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestQuotes)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Chưa có yêu cầu mới</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestQuotes as $q): ?>
                            <tr>
                                <td>
                                    <strong><?= e($q['customer_name']) ?></strong><br>
                                    <small class="text-muted"><?= e($q['company'] ?: 'Cá nhân') ?></small>
                                </td>
                                <td>
                                    <?= e($q['phone']) ?><br>
                                    <small><?= e($q['project_name'] ?: '–') ?></small>
                                </td>
                                <td>
                                    <?php if ($q['file_bom_path']): ?>
                                        <a href="<?= url('/admin/bao-gia/tai-file/' . $q['id']) ?>" class="badge badge--primary"><?= icon('download') ?> Tải BOM</a>
                                    <?php else: ?>
                                        <span class="text-muted">–</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge--warning"><?= e($q['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bảng Đơn Hàng Mới Nhất -->
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;"><?= icon('cart') ?> Đơn đặt hàng mới</h3>
            <a href="<?= url('/admin/don-hang') ?>" style="font-size: 0.85rem;">Tất cả &rarr;</a>
        </div>
        <div class="table-scroll">
            <table class="spec-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>T/Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestOrders)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Chưa có đơn hàng</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestOrders as $o): ?>
                            <tr>
                                <td><a href="<?= url('/admin/don-hang/' . $o['id']) ?>"><strong>#<?= e($o['order_code']) ?></strong></a></td>
                                <td><?= e($o['customer_name']) ?><br><small><?= e($o['phone']) ?></small></td>
                                <td><strong class="price"><?= money($o['total']) ?></strong></td>
                                <td><span class="badge badge--outline"><?= e($o['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Lưới 2 Cột: Thành Viên Mới & Đánh Giá Mới -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Thành viên mới đăng ký -->
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;"><?= icon('users') ?> Khách hàng & Thành viên mới</h3>
            <a href="<?= url('/admin/thanh-vien') ?>" style="font-size: 0.85rem;">Tất cả &rarr;</a>
        </div>
        <div class="table-scroll">
            <table class="spec-table">
                <thead>
                    <tr>
                        <th>Họ tên</th>
                        <th>Email / SĐT</th>
                        <th>Kênh đăng nhập</th>
                        <th>Đơn mua</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestUsers)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Chưa có thành viên đăng ký</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestUsers as $u): ?>
                            <tr>
                                <td>
                                    <strong><?= e($u['name']) ?></strong><br>
                                    <small class="badge badge--sm <?= $u['status'] === 'active' ? 'badge--success' : 'badge--danger' ?>"><?= e($u['status']) ?></small>
                                </td>
                                <td>
                                    <?= e($u['email']) ?><br>
                                    <small><?= e($u['phone'] ?: '–') ?></small>
                                </td>
                                <td>
                                    <?php if ($u['provider'] === 'google'): ?>
                                        <span style="color:#4285F4; font-weight:600;">Google</span>
                                    <?php elseif ($u['provider'] === 'facebook'): ?>
                                        <span style="color:#1877F2; font-weight:600;">Facebook</span>
                                    <?php else: ?>
                                        <span style="color:#475569;">Email/Mật khẩu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= (int)($u['order_count'] ?? 0) ?></strong> đơn
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Đánh giá mới nhất -->
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;"><?= icon('star') ?> Đánh giá sản phẩm mới</h3>
            <a href="<?= url('/admin/danh-gia') ?>" style="font-size: 0.85rem;">Tất cả &rarr;</a>
        </div>
        <div class="table-scroll">
            <table class="spec-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Sao</th>
                        <th>Nội dung</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestReviews)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Chưa có đánh giá nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($latestReviews as $rev): ?>
                            <tr>
                                <td>
                                    <strong><?= e($rev['model_code'] ?? 'SP') ?></strong><br>
                                    <small class="text-muted"><?= mb_strimwidth($rev['product_name'] ?? '', 0, 25, '...') ?></small>
                                </td>
                                <td>
                                    <?= e($rev['author_name']) ?><br>
                                    <small><?= e($rev['author_phone'] ?: '–') ?></small>
                                </td>
                                <td>
                                    <span style="color:#f59e0b; font-weight:700;">★ <?= (int)$rev['rating'] ?></span>
                                </td>
                                <td>
                                    <small><?= mb_strimwidth($rev['comment'] ?? '', 0, 40, '...') ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
