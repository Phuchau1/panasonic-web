<?php
/** @var string $title */
/** @var string $query */
/** @var bool $searched */
/** @var ?array $order */
/** @var array $items */
/** @var ?array $carrierInfo */
/** @var ?string $trackingUrl */
/** @var array $carriers */
?>

<div class="container tracking-page" style="padding-top: 1.5rem; padding-bottom: 3.5rem; max-width: 950px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Đường dẫn" style="margin-bottom: 1.25rem;">
        <a href="<?= url('/') ?>">Trang chủ</a>
        <span>/</span>
        <span aria-current="page">Tra cứu đơn hàng</span>
    </nav>

    <!-- Header -->
    <div style="text-align: center; margin-bottom: 2rem;">
        <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: #e0f2fe; color: #004098; font-size: 0.8rem; font-weight: 800; padding: 4px 12px; border-radius: 99px; text-transform: uppercase; margin-bottom: 0.5rem;">
            <?= icon('truck') ?> TÍCH HỢP HỆ THỐNG VẬN CHUYỂN TOÀN QUỐC
        </span>
        <h1 style="font-size: 1.85rem; font-weight: 900; color: #004098; margin: 0 0 0.5rem; letter-spacing: -0.5px;">
            TRA CỨU TIẾN TRÌNH ĐƠN HÀNG & HÀNH TRÌNH VẬN ĐƠN
        </h1>
        <p style="color: #64748b; font-size: 0.95rem; max-width: 650px; margin: 0 auto;">
            Nhập <strong>Mã đơn hàng</strong> (VD: PN24...), <strong>Mã vận đơn</strong> (VD: GHTK-..., GHN-..., VTP-...) hoặc <strong>Số điện thoại</strong> để theo dõi trực tiếp tình trạng giao hàng.
        </p>
    </div>

    <!-- Khối tìm kiếm -->
    <div style="background: #fff; border: 1.5px solid #004098; border-radius: 10px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(0,64,152,0.08); margin-bottom: 2rem;">
        <form action="<?= url('/tra-cuu-don-hang') ?>" method="get" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 260px; position: relative;">
                <input type="text" name="q" value="<?= e($query) ?>" placeholder="Nhập mã đơn hàng / mã vận đơn / số điện thoại đặt hàng..." required style="width: 100%; height: 46px; padding: 0.5rem 1rem; border: 1.5px solid #cbd5e1; border-radius: 7px; font-size: 0.95rem; font-weight: 600; box-sizing: border-box;">
            </div>
            <button type="submit" class="btn btn--primary" style="height: 46px; padding: 0 1.75rem; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 7px; background: #004098;">
                <?= icon('search') ?> Tra Cứu Ngay
            </button>
        </form>
    </div>

    <?php if ($searched && !$order): ?>
        <!-- Không tìm thấy kết quả -->
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 10px; padding: 2rem; text-align: center; margin-bottom: 2rem;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem;">
                <?= icon('alert') ?>
            </div>
            <h3 style="color: #991b1b; margin: 0 0 0.5rem;">Không tìm thấy thông tin đơn hàng!</h3>
            <p style="color: #b91c1c; font-size: 0.92rem; max-width: 550px; margin: 0 auto 1.25rem;">
                Không có đơn hàng nào khớp với từ khóa "<strong><?= e($query) ?></strong>". Vui lòng kiểm tra lại mã đơn hàng hoặc số điện thoại Quý khách đã sử dụng khi đặt hàng.
            </p>
            <div style="display: inline-flex; gap: 0.75rem;">
                <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline', '0798284321'))) ?>" class="btn btn--primary btn--sm" style="background: #dc2626; border-color: #dc2626;">
                    <?= icon('phone') ?> Hotline hỗ trợ: <?= e(config('hotline', '0798 284 321')) ?>
                </a>
                <a href="https://zalo.me/<?= e(config('zalo', '0798284321')) ?>" target="_blank" class="btn btn--outline btn--sm">
                    <?= icon('chat') ?> Nhắn tin Zalo
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <!-- HIỂN THỊ KẾT QUẢ ĐƠN HÀNG TÌM THẤY -->
        <?php
        $st = $order['status'] ?? 'pending';
        $statusMap = [
            'pending'   => ['label' => 'Chờ xác nhận', 'color' => '#d97706', 'bg' => '#fffbeb', 'step' => 1],
            'confirmed' => ['label' => 'Đã xác nhận & Chuẩn bị kho', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'step' => 2],
            'shipping'  => ['label' => 'Đang giao hàng', 'color' => '#004098', 'bg' => '#eff6ff', 'step' => 3],
            'completed' => ['label' => 'Đã giao thành công', 'color' => '#059669', 'bg' => '#ecfdf5', 'step' => 4],
            'cancelled' => ['label' => 'Đã huỷ', 'color' => '#dc2626', 'bg' => '#fef2f2', 'step' => 0],
        ];
        $curSt = $statusMap[$st] ?? ['label' => $st, 'color' => '#64748b', 'bg' => '#f1f5f9', 'step' => 1];
        ?>

        <div style="background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 1.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <!-- Tiêu đề & Trạng thái hiện tại -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Đơn hàng:</div>
                    <h2 style="font-size: 1.4rem; color: #004098; margin: 0.2rem 0 0 0; font-family: monospace;">
                        #<?= e($order['order_code']) ?>
                    </h2>
                    <small style="color: #64748b;">Thời gian đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                </div>
                <div>
                    <span style="display: inline-block; padding: 0.5rem 1.25rem; border-radius: 99px; font-weight: 800; font-size: 0.95rem; background: <?= $curSt['bg'] ?>; color: <?= $curSt['color'] ?>; border: 1.5px solid <?= $curSt['color'] ?>40;">
                        <?= $curSt['label'] ?>
                    </span>
                </div>
            </div>

            <!-- Thanh tiến trình 4 bước -->
            <?php if ($st !== 'cancelled'): ?>
                <div style="margin-bottom: 2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; position: relative;">
                        <?php 
                        $stepList = [
                            1 => 'Chờ xác nhận',
                            2 => 'Đã xác nhận',
                            3 => 'Đang giao hàng',
                            4 => 'Hoàn tất',
                        ];
                        $curNum = $curSt['step'];
                        ?>
                        <?php foreach ($stepList as $stepNum => $stepName): 
                            $isDone = ($curNum > $stepNum) || ($curNum === 4 && $stepNum === 4);
                            $isCur  = ($curNum === $stepNum);
                        ?>
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; z-index: 2;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem;
                                    <?= $isDone ? 'background: #059669; color: #fff;' : ($isCur ? 'background: #004098; color: #fff; box-shadow: 0 0 0 4px rgba(0,64,152,0.18);' : 'background: #fff; color: #94a3b8; border: 1.5px solid #cbd5e1;') ?>">
                                    <?= $isDone ? icon('check') : $stepNum ?>
                                </div>
                                <span style="font-size: 0.8rem; font-weight: <?= ($isCur || $isDone) ? '700' : '500' ?>; color: <?= $isDone ? '#059669' : ($isCur ? '#004098' : '#94a3b8') ?>; margin-top: 0.4rem; text-align: center;">
                                    <?= $stepName ?>
                                </span>
                            </div>
                            <?php if ($stepNum < 4): ?>
                                <div style="flex: 1; height: 3px; background: <?= ($curNum > $stepNum || $curNum === 4) ? '#059669' : '#e2e8f0' ?>; margin-top: -18px; position: relative; z-index: 1;"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- THÔNG TIN VẬN ĐƠN & ĐƠN VỊ VẬN CHUYỂN -->
            <?php if (!empty($order['shipping_carrier']) || !empty($order['tracking_code'])): ?>
                <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid #dbeafe; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background: #004098; color: #fff; display: flex; align-items: center; justify-content: center;">
                                <?= icon('truck') ?>
                            </div>
                            <strong style="color: #1e3a8a; font-size: 0.98rem;">Thông Tin Đối Tác Vận Chuyển</strong>
                        </div>
                        <?php if (!empty($carrierInfo['hotline'])): ?>
                            <span style="font-size: 0.82rem; color: #1e40af;">
                                Hotline vận chuyển: <strong><?= e($carrierInfo['hotline']) ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: center;">
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Đơn vị giao hàng:</div>
                            <div style="font-size: 1rem; font-weight: 800; color: <?= $carrierInfo['color'] ?? '#004098' ?>; margin-top: 2px;">
                                <?= e($carrierInfo['name'] ?? $order['shipping_carrier']) ?>
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Mã vận đơn:</div>
                            <div style="font-family: monospace; font-size: 1.15rem; font-weight: 800; color: #004098; margin-top: 2px;">
                                <?= e($order['tracking_code'] ?: 'PNS-' . $order['order_code']) ?>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <?php if (!empty($trackingUrl)): ?>
                                <a href="<?= e($trackingUrl) ?>" target="_blank" class="btn btn--primary btn--sm" style="background: <?= $carrierInfo['color'] ?? '#004098' ?>; border-color: <?= $carrierInfo['color'] ?? '#004098' ?>; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;">
                                    🔍 Tra cứu trên <?= e($carrierInfo['short_name'] ?? 'Website hãng') ?> &nearr;
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.85rem; color: #047857; font-weight: 600;">
                                    <?= icon('check') ?> Đội xe nội bộ giao hàng
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($order['shipped_at']) || !empty($order['delivered_at']) || !empty($order['shipping_note'])): ?>
                        <div style="border-top: 1px dashed #bfdbfe; margin-top: 1rem; padding-top: 0.75rem; font-size: 0.85rem; color: #334155; display: flex; flex-wrap: wrap; gap: 1.5rem;">
                            <?php if (!empty($order['shipped_at'])): ?>
                                <div>Xuất kho: <strong><?= date('d/m/Y H:i', strtotime($order['shipped_at'])) ?></strong></div>
                            <?php endif; ?>
                            <?php if (!empty($order['delivered_at'])): ?>
                                <div style="color: #059669;">Giao thành công: <strong><?= date('d/m/Y H:i', strtotime($order['delivered_at'])) ?></strong></div>
                            <?php endif; ?>
                            <?php if (!empty($order['shipping_note'])): ?>
                                <div>Ghi chú: <em><?= e($order['shipping_note']) ?></em></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Thông tin người nhận & Địa chỉ giao hàng -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem; font-size: 0.9rem;">
                <div>
                    <h4 style="margin: 0 0 0.5rem; font-size: 0.95rem; color: #004098;">Người nhận hàng</h4>
                    <p style="margin: 0.2rem 0;"><strong>Họ tên:</strong> <?= e($order['customer_name']) ?></p>
                    <p style="margin: 0.2rem 0;"><strong>Điện thoại:</strong> <?= e($order['phone']) ?></p>
                    <p style="margin: 0.2rem 0;"><strong>Địa chỉ:</strong> <?= e($order['address']) ?></p>
                </div>
                <div>
                    <h4 style="margin: 0 0 0.5rem; font-size: 0.95rem; color: #004098;">Thanh toán & Ghi chú</h4>
                    <p style="margin: 0.2rem 0;"><strong>Hình thức TT:</strong> <?= $order['payment_method'] === 'bank_transfer' ? 'Chuyển khoản QR ngân hàng' : 'Thanh toán COD khi nhận hàng' ?></p>
                    <p style="margin: 0.2rem 0;"><strong>Trạng thái TT:</strong> <?= ($order['payment_status'] ?? '') === 'paid' ? '<span style="color:#059669; font-weight:700;">Đã thanh toán</span>' : '<span style="color:#d97706; font-weight:700;">Chưa thanh toán</span>' ?></p>
                    <?php if (!empty($order['note'])): ?>
                        <p style="margin: 0.2rem 0;"><strong>Ghi chú:</strong> <?= e($order['note']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- BẢN ĐỒ GIAO HÀNG & ĐỊNH VỊ CÔNG TRÌNH KHÁCH HÀNG (GOOGLE MAPS) -->
            <div style="background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; margin-bottom: 1.75rem; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #004098; font-size: 1.1rem; display: inline-flex; align-items: center;"><?= icon('map-pin') ?></span>
                        <div>
                            <strong style="color: #004098; font-size: 0.95rem;">Bản Đồ Giao Hàng & Định Vị Điểm Đến</strong>
                            <div style="font-size: 0.78rem; color: #64748b;">Điểm nhận hàng: <strong><?= e($order['address']) ?></strong></div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                        <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($order['address']) ?>" target="_blank" class="btn btn--outline btn--sm" style="font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                            📍 Mở Bản Đồ Lớn &nearr;
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&origin=<?= urlencode(config('address', 'Tổng Kho Panasonic')) ?>&destination=<?= urlencode($order['address']) ?>&travelmode=driving" target="_blank" class="btn btn--primary btn--sm" style="font-size: 0.8rem; font-weight: 700; background: #004098; display: inline-flex; align-items: center; gap: 0.25rem;">
                            🚗 Lộ Trình Từ Kho Tổng &nearr;
                        </a>
                    </div>
                </div>
                <div style="height: 280px; width: 100%; background: #f1f5f9; position: relative;">
                    <iframe 
                        src="https://maps.google.com/maps?q=<?= urlencode($order['address']) ?>&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                        width="100%" 
                        height="100%" 
                        style="border: 0; display: block;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bản đồ giao hàng #<?= e($order['order_code']) ?>">
                    </iframe>
                </div>
            </div>

            <!-- Bảng danh mục thiết bị đã đặt -->
            <h4 style="margin: 0 0 0.75rem; font-size: 0.95rem; color: #004098;">Danh mục thiết bị điện trong đơn hàng</h4>
            <div class="table-scroll" style="overflow-x: auto;">
                <table class="spec-table" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                            <th style="padding: 8px 10px;">STT</th>
                            <th style="padding: 8px 10px;">Tên sản phẩm thiết bị điện</th>
                            <th style="padding: 8px 10px;">Mã Model</th>
                            <th style="padding: 8px 10px; text-align: right;">Đơn giá</th>
                            <th style="padding: 8px 10px; text-align: center;">SL</th>
                            <th style="padding: 8px 10px; text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $idx = 1; foreach ($items as $it): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 8px 10px;"><?= $idx++ ?></td>
                                <td style="padding: 8px 10px;"><strong><?= e($it['product_name']) ?></strong></td>
                                <td style="padding: 8px 10px;"><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;"><?= e($it['model_code']) ?></code></td>
                                <td style="padding: 8px 10px; text-align: right;"><?= money($it['unit_price']) ?></td>
                                <td style="padding: 8px 10px; text-align: center; font-weight: 700;"><?= (int)$it['quantity'] ?></td>
                                <td style="padding: 8px 10px; text-align: right; font-weight: 700;"><?= money($it['line_total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: right; padding: 10px; font-weight: 700; font-size: 1rem; color: #004098;">Tổng tiền thanh toán:</td>
                            <td style="text-align: right; padding: 10px; font-weight: 800; font-size: 1.15rem; color: #004098;"><?= money($order['total']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Khối giới thiệu các đối tác vận chuyển chính thức -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.75rem;">
        <h3 style="font-size: 1.1rem; color: #004098; margin: 0 0 1rem; text-align: center;">
            HỆ THỐNG ĐỐI TÁC VẬN CHUYỂN ỦY QUYỀN
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <?php foreach ($carriers as $cKey => $c): ?>
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; text-align: center;">
                    <div style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: <?= $c['color'] ?>; margin-bottom: 0.3rem;"></div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a;"><?= e($c['short_name']) ?></div>
                    <small style="color: #64748b; font-size: 0.78rem; display: block; margin-top: 4px;">Hotline: <?= e($c['hotline']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
