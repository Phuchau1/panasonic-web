<?php
/** @var float $totalRevenue */
/** @var float $monthlyRevenue */
/** @var float $todayRevenue */
/** @var int $totalOrdersCount */
/** @var int $completedOrdersCount */
/** @var int $avgOrderValue */
/** @var array $monthlyStats */
/** @var array $statusBreakdown */
/** @var array $paymentMethods */
/** @var array $topProducts */
/** @var array $lowStockProducts */

$statusLabels = [
    'pending'    => ['Chờ xác nhận', '#f59e0b', '#fef3c7'],
    'confirmed'  => ['Đã xác nhận', '#0284c7', '#e0f2fe'],
    'processing' => ['Đang đóng gói', '#6366f1', '#e0e7ff'],
    'shipping'   => ['Đang giao hàng', '#8b5cf6', '#ede9fe'],
    'completed'  => ['Hoàn thành', '#059669', '#d1fae5'],
    'cancelled'  => ['Đã hủy', '#dc2626', '#fee2e2'],
];

$paymentLabels = [
    'cod'   => 'Thu hộ tiền mặt (COD)',
    'bacs'  => 'Chuyển khoản đại lý',
    'vnpay' => 'Cổng VNPAY / QR',
    'momo'  => 'Ví MoMo',
];

$maxRevenue = !empty($monthlyStats) ? max(array_column($monthlyStats, 'revenue')) : 0;
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0;">Báo Cáo Hoạt Động & Thống Kê Doanh Thu</h1>
        <p style="font-size: 0.88rem; color: #64748b; margin: 0;">Số liệu tổng hợp kinh doanh phân phối thiết bị điện Panasonic theo thời gian thực.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <button onclick="window.print()" class="btn btn--outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem;">
            <?= icon('download') ?> In Báo Cáo
        </button>
        <a href="<?= url('/admin/don-hang') ?>" class="btn btn--primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem;">
            <?= icon('cart') ?> Quản Lý Đơn Hàng
        </a>
    </div>
</div>

<!-- KPI Cards 4 Cột -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
    <div class="admin-card" style="padding: 1.25rem; border-left: 4px solid #004098;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng Doanh Thu Lũy Kế</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #004098; margin-top: 0.25rem;"><?= money($totalRevenue) ?></div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #004098; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <?= icon('calculator') ?>
            </div>
        </div>
        <div style="font-size: 0.8rem; color: #64748b; display: flex; align-items: center; gap: 0.4rem;">
            <span>Tất cả đơn hàng thành công & đang xử lý</span>
        </div>
    </div>

    <div class="admin-card" style="padding: 1.25rem; border-left: 4px solid #059669;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Doanh Thu Tháng Này</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #059669; margin-top: 0.25rem;"><?= money($monthlyRevenue) ?></div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <?= icon('award') ?>
            </div>
        </div>
        <div style="font-size: 0.8rem; color: #64748b;">
            Hôm nay: <strong style="color: #0f172a;"><?= money($todayRevenue) ?></strong>
        </div>
    </div>

    <div class="admin-card" style="padding: 1.25rem; border-left: 4px solid #6366f1;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Quy Mô Đơn Hàng</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #4338ca; margin-top: 0.25rem;">
                    <?= number_format($totalOrdersCount) ?> <span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">đơn</span>
                </div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <?= icon('cart') ?>
            </div>
        </div>
        <div style="font-size: 0.8rem; color: #64748b;">
            Đã hoàn tất: <strong style="color: #059669;"><?= number_format($completedOrdersCount) ?> đơn</strong>
        </div>
    </div>

    <div class="admin-card" style="padding: 1.25rem; border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Giá Trị TB / Đơn (AOV)</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #b45309; margin-top: 0.25rem;"><?= money($avgOrderValue) ?></div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <?= icon('building') ?>
            </div>
        </div>
        <div style="font-size: 0.8rem; color: #64748b;">
            Quy chuẩn đơn hàng công trình & dân dụng
        </div>
    </div>
</div>

<!-- Biểu Đồ Tăng Trưởng Doanh Thu 6 Tháng Gần Nhất -->
<div class="admin-card mb-4" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
        <div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0;">
                Biểu Đồ Doanh Thu 6 Tháng Gần Nhất
            </h3>
            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">Theo dõi xu hướng đơn hàng và giá trị luân chuyển qua từng tháng.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.82rem; color: #64748b;">
            <span style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="width: 12px; height: 12px; border-radius: 3px; background: #004098; display: inline-block;"></span> Doanh số bán
            </span>
        </div>
    </div>

    <?php if (empty($monthlyStats)): ?>
        <div style="padding: 2.5rem; text-align: center; color: #94a3b8;">
            <?= icon('clock') ?><br>Chưa có dữ liệu thống kê doanh số các tháng gần đây.
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(<?= count($monthlyStats) ?>, 1fr); gap: 1.5rem; align-items: flex-end; min-height: 220px; padding: 1.5rem 0.5rem 0.5rem 0.5rem; background: #fafafa; border-radius: 8px;">
            <?php foreach ($monthlyStats as $m): 
                $pct = $maxRevenue > 0 ? max(10, round(($m['revenue'] / $maxRevenue) * 100)) : 10;
            ?>
                <div style="display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; gap: 0.5rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #004098; white-space: nowrap;">
                        <?= money($m['revenue']) ?>
                    </div>
                    <div style="font-size: 0.72rem; color: #64748b;">
                        (<?= (int)$m['order_count'] ?> đơn)
                    </div>
                    <div style="width: 100%; max-width: 60px; height: <?= $pct ?>%; min-height: 24px; background: linear-gradient(180deg, #38bdf8 0%, #004098 100%); border-radius: 6px 6px 2px 2px; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,64,152,0.15);" title="Tháng <?= e($m['month_label']) ?>: <?= money($m['revenue']) ?>"></div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #334155; margin-top: 0.25rem;">
                        <?= e($m['month_label']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Lưới 2 cột: Cơ cấu trạng thái & Phương thức thanh toán -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Cơ cấu trạng thái đơn hàng -->
    <div class="admin-card">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
            Cơ Cấu Trạng Thái Đơn Hàng
        </h3>
        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <?php if (empty($statusBreakdown)): ?>
                <div style="padding: 1.5rem; text-align: center; color: #94a3b8;">Chưa có đơn hàng nào.</div>
            <?php else: ?>
                <?php foreach ($statusBreakdown as $sb): 
                    $info = $statusLabels[$sb['status']] ?? [$sb['status'], '#64748b', '#f1f5f9'];
                    $pct = $totalOrdersCount > 0 ? round(($sb['count'] / $totalOrdersCount) * 100, 1) : 0;
                ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; font-size: 0.88rem;">
                            <span style="font-weight: 600; color: <?= $info[1] ?>; display: flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: <?= $info[1] ?>;"></span>
                                <?= e($info[0]) ?>
                            </span>
                            <span style="color: #64748b; font-size: 0.82rem;">
                                <strong><?= (int)$sb['count'] ?> đơn</strong> (<?= $pct ?>%) &bull; <?= money($sb['sum_total']) ?>
                            </span>
                        </div>
                        <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                            <div style="width: <?= $pct ?>%; height: 100%; background: <?= $info[1] ?>; border-radius: 4px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cơ cấu phương thức thanh toán -->
    <div class="admin-card">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
            Hình Thức Thanh Toán
        </h3>
        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <?php if (empty($paymentMethods)): ?>
                <div style="padding: 1.5rem; text-align: center; color: #94a3b8;">Chưa có dữ liệu thanh toán.</div>
            <?php else: ?>
                <?php foreach ($paymentMethods as $pm): 
                    $methodName = $paymentLabels[$pm['payment_method']] ?? strtoupper($pm['payment_method']);
                    $pct = $totalOrdersCount > 0 ? round(($pm['count'] / $totalOrdersCount) * 100, 1) : 0;
                ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; font-size: 0.88rem;">
                            <span style="font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 0.4rem;">
                                <?= icon('cart') ?> <?= e($methodName) ?>
                            </span>
                            <span style="color: #64748b; font-size: 0.82rem;">
                                <strong><?= (int)$pm['count'] ?> đơn</strong> (<?= $pct ?>%) &bull; <?= money($pm['sum_total']) ?>
                            </span>
                        </div>
                        <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                            <div style="width: <?= $pct ?>%; height: 100%; background: #004098; border-radius: 4px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Lưới 2 bảng: Top 10 Bán Chạy & Cảnh Báo Tồn Kho Thấp -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 1.5rem;">
    <!-- Top 10 Sản phẩm bán chạy -->
    <div class="admin-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0;">Top 10 Thiết Bị Bán Chạy Nhất</h3>
            <span class="badge badge--success">Doanh số cao</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f8fafc; color: #64748b; text-align: left; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 0.75rem 1rem;">Sản phẩm</th>
                        <th style="padding: 0.75rem; text-align: center;">Đã bán</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">Doanh số</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topProducts)): ?>
                        <tr><td colspan="3" style="padding: 2rem; text-align: center; color: #94a3b8;">Chưa có dữ liệu bán hàng.</td></tr>
                    <?php else: ?>
                        <?php foreach ($topProducts as $idx => $p): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="font-weight: 800; font-size: 0.85rem; color: <?= $idx < 3 ? '#004098' : '#94a3b8' ?>; width: 18px;">#<?= $idx + 1 ?></span>
                                    <img src="<?= media($p['thumbnail']) ?>" alt="" style="width: 38px; height: 38px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; padding: 2px;">
                                    <div>
                                        <a href="<?= url('/admin/san-pham/sua/' . $p['id']) ?>" style="font-weight: 600; color: #0f172a; text-decoration: none;" title="<?= e($p['name']) ?>">
                                            <?= e(mb_strimwidth($p['name'], 0, 38, '...')) ?>
                                        </a>
                                        <div style="font-size: 0.75rem; color: #64748b;">Mã: <?= e($p['sku'] ?: 'N/A') ?></div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: #004098;">
                                    <?= number_format((int)$p['total_qty']) ?>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #059669;">
                                    <?= money($p['total_sales']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cảnh báo tồn kho thấp -->
    <div class="admin-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0;">Cảnh Báo Tồn Kho Thấp (&lt; 20 cái)</h3>
            <span class="badge badge--danger"><?= count($lowStockProducts) ?> mặt hàng</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f8fafc; color: #64748b; text-align: left; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 0.75rem 1rem;">Sản phẩm</th>
                        <th style="padding: 0.75rem; text-align: center;">Tồn kho</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">Đơn giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lowStockProducts)): ?>
                        <tr><td colspan="3" style="padding: 2rem; text-align: center; color: #059669;"><?= icon('check-circle') ?> Kho hàng đầy đủ, không có thiết bị nào sắp hết!</td></tr>
                    <?php else: ?>
                        <?php foreach ($lowStockProducts as $lp): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="<?= media($lp['thumbnail']) ?>" alt="" style="width: 38px; height: 38px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; padding: 2px;">
                                    <div>
                                        <a href="<?= url('/admin/san-pham/sua/' . $lp['id']) ?>" style="font-weight: 600; color: #0f172a; text-decoration: none;" title="<?= e($lp['name']) ?>">
                                            <?= e(mb_strimwidth($lp['name'], 0, 38, '...')) ?>
                                        </a>
                                        <div style="font-size: 0.75rem; color: #64748b;">Mã: <?= e($lp['sku'] ?: 'N/A') ?></div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span class="badge" style="background: #fee2e2; color: #dc2626; font-weight: 700; font-size: 0.82rem; padding: 0.25rem 0.5rem;">
                                        <?= (int)$lp['stock'] ?> cái
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #475569;">
                                    <?= money($lp['price']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
