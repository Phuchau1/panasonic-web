<?php
/** @var array $order */
/** @var array $items */
/** @var array $statuses */
/** @var array $carriers */
$carriers = $carriers ?? \App\Models\Order::CARRIERS;
$allowedStatuses = $allowedStatuses ?? \App\Models\Order::allowedNextStatuses($order['status'] ?? 'pending');
$carrierKey = $order['shipping_carrier'] ?? 'ghtk';
$carrierInfo = \App\Models\Order::getCarrierInfo($carrierKey);
$trackingUrl = \App\Models\Order::getTrackingUrl($order['shipping_carrier'] ?? null, $order['tracking_code'] ?? null);
?>

<div class="admin-card" style="max-width: 950px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; display: flex; align-items: center; gap: 0.6rem;">
                Đơn hàng: #<?= e($order['order_code']) ?>
                <?php if (!empty($order['shipping_carrier']) && isset($carriers[$order['shipping_carrier']])): ?>
                    <span style="font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 700; background: <?= $carriers[$order['shipping_carrier']]['bg'] ?>; color: <?= $carriers[$order['shipping_carrier']]['color'] ?>; border: 1px solid <?= $carriers[$order['shipping_carrier']]['color'] ?>40;">
                        <?= e($carriers[$order['shipping_carrier']]['short_name']) ?>
                    </span>
                <?php endif; ?>
            </h2>
            <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?> &bull; Kênh: Web Online</small>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= url('/admin/don-hang/in-hoa-don/' . $order['id']) ?>" target="_blank" class="btn btn--primary btn--sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                <?= icon('download') ?> In Phiếu Xuất Kho
            </a>
            <a href="<?= url('/admin/don-hang') ?>" class="btn btn--outline btn--sm">&larr; Quay lại danh sách</a>
        </div>
    </div>

    <!-- Quy trình đơn hàng 4 bước (Stepper) -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
        <div style="margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #334155;">
                Tiến trình xử lý đơn hàng (Chỉ tiến &bull; Khóa lùi)
            </div>
            <span style="font-size: 0.8rem; color: #64748b;">
                Đơn: <strong><?= ($order['order_type'] ?? '') === 'project' ? 'Giá Công Trình B2B' : 'Giá Bán Lẻ' ?></strong>
            </span>
        </div>

        <?php if ($order['status'] === 'cancelled'): ?>
            <div style="padding: 0.85rem 1rem; background: #fef2f2; border: 1px dashed #fca5a5; border-radius: 6px; color: #b91c1c; font-size: 0.88rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('alert') ?> <strong>Đơn hàng đã huỷ:</strong> Toàn bộ tồn kho đã được hoàn trả, quy trình đã dừng và khóa lại.
            </div>
        <?php else: ?>
            <div id="orderStepper" data-current="<?= e($order['status']) ?>" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
                <?php 
                $steps = [
                    'pending'   => '1. Chờ xác nhận',
                    'confirmed' => '2. Đã xác nhận',
                    'shipping'  => '3. Đang giao hàng',
                    'completed' => '4. Hoàn thành',
                ];
                $stepKeys = array_keys($steps);
                $curIdx = array_search($order['status'], $stepKeys, true);
                if ($curIdx === false) $curIdx = 0;
                ?>
                <?php foreach ($stepKeys as $idx => $sKey): 
                    $isPassed = $idx < $curIdx;
                    $isCurrent = $idx === $curIdx;
                    $isDone = $isPassed || ($isCurrent && $order['status'] === 'completed');
                ?>
                    <div class="stepper-item" data-step="<?= e($sKey) ?>" data-index="<?= $idx ?>" style="display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; z-index: 2;">
                        <div class="stepper-circle" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.92rem; font-weight: 700; transition: all 0.25s;
                            <?= $isDone ? 'background: #059669; color: #fff; box-shadow: 0 2px 6px rgba(5,150,105,0.25);' : ($isCurrent ? 'background: #004098; color: #fff; box-shadow: 0 0 0 4px rgba(0,64,152,0.18);' : 'background: #f8fafc; color: #94a3b8; border: 1.5px solid #cbd5e1;') ?>">
                            <?php if ($isDone): ?>
                                <?= icon('check') ?>
                            <?php else: ?>
                                <?= $idx + 1 ?>
                            <?php endif; ?>
                        </div>
                        <span class="stepper-label" style="font-size: 0.8rem; font-weight: <?= ($isCurrent || $isDone) ? '700' : '500' ?>; color: <?= $isDone ? '#059669' : ($isCurrent ? '#004098' : '#94a3b8') ?>; margin-top: 0.45rem; text-align: center;">
                            <?= $steps[$sKey] ?>
                        </span>
                    </div>
                    <?php if ($idx < count($stepKeys) - 1): ?>
                        <div class="stepper-line" data-line="<?= $idx ?>" style="flex: 1; height: 3px; background: <?= ($idx < $curIdx || ($isCurrent && $order['status'] === 'completed')) ? '#059669' : '#e2e8f0' ?>; margin-top: -20px; position: relative; z-index: 1; transition: background 0.25s;"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ================================================================= -->
    <!-- KHỐI TÍCH HỢP ĐƠN VỊ GIAO HÀNG & XÁC NHẬN VẬN CHUYỂN THEO BƯỚC -->
    <!-- ================================================================= -->

    <?php if ($order['status'] === 'pending'): ?>
        <!-- Bước 1: Đang chờ duyệt đơn -->
        <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #d97706; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <?= icon('clock') ?>
                </div>
                <div>
                    <div style="font-weight: 700; color: #92400e; font-size: 0.98rem;">Đơn hàng đang chờ quản trị viên xác nhận</div>
                    <div style="font-size: 0.82rem; color: #b45309; margin-top: 2px;">
                        Kiểm tra số lượng tồn kho và thông tin khách hàng, sau đó bấm Xác nhận để chuyển sang bước chuẩn bị hàng & vận chuyển.
                    </div>
                </div>
            </div>
            <form action="<?= url('/admin/don-hang/trang-thai/' . $order['id']) ?>" method="post" style="margin: 0;">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn--primary" style="padding: 0.6rem 1.4rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 7px;">
                    <?= icon('check') ?> Xác Nhận Đơn Hàng Ngay ➔
                </button>
            </form>
        </div>

    <?php elseif ($order['status'] === 'confirmed'): ?>
        <!-- Bước 2: Đã xác nhận -> Sẵn sàng bàn giao cho Đơn vị giao hàng -->
        <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #dcfce7; padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 32px; height: 32px; border-radius: 6px; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <?= icon('truck') ?>
                    </div>
                    <div>
                        <strong style="font-size: 0.98rem; color: #065f46;">Tích hợp Đơn vị Giao hàng & Bàn giao Vận chuyển</strong>
                        <div style="font-size: 0.8rem; color: #047857;">Chọn hãng giao hàng, tạo mã vận đơn và chuyển giao cho shipper</div>
                    </div>
                </div>
                <span class="badge badge--success">Sẵn sàng xuất kho</span>
            </div>

            <form action="<?= url('/admin/don-hang/trang-thai/' . $order['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="shipping">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            Đơn vị vận chuyển đối tác:
                        </label>
                        <select name="shipping_carrier" id="shippingCarrierSelect" onchange="autoUpdatePrefix()" style="width: 100%; height: 40px !important; padding: 0.4rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; font-weight: 600; background: #fff;">
                            <?php foreach ($carriers as $cKey => $cVal): ?>
                                <option value="<?= e($cKey) ?>" <?= ($order['shipping_carrier'] ?? 'ghtk') === $cKey ? 'selected' : '' ?>>
                                    <?= e($cVal['name']) ?> (Hotline: <?= e($cVal['hotline']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            <span>Mã vận đơn (Tracking Code):</span>
                            <button type="button" onclick="generateTrackingCode()" style="background: none; border: none; color: #004098; font-size: 0.8rem; font-weight: 700; cursor: pointer; padding: 0; text-decoration: underline;">
                                ⚡ Tạo mã tự động
                            </button>
                        </label>
                        <div style="position: relative;">
                            <input type="text" name="tracking_code" id="trackingCodeInput" value="<?= e($order['tracking_code'] ?? '') ?>" placeholder="VD: GHTK-<?= preg_replace('/[^0-9A-Z]/', '', strtoupper($order['order_code'])) ?>" style="width: 100%; height: 40px; padding: 0.4rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; font-family: monospace; font-weight: 700; box-sizing: border-box;">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                        Ghi chú gửi đơn vị vận chuyển & giao nhận:
                    </label>
                    <input type="text" name="shipping_note" value="<?= e($order['shipping_note'] ?? 'Hàng thiết bị điện chính hãng Panasonic. Cho khách đồng kiểm trước khi nhận.') ?>" placeholder="Ghi chú giao hàng..." style="width: 100%; height: 38px; padding: 0.4rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; box-sizing: border-box;">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; border-top: 1px dashed #cbd5e1; padding-top: 1rem;">
                    <button type="submit" class="btn btn--primary" style="background: #059669; border-color: #059669; height: 42px; padding: 0 1.5rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 7px; box-shadow: 0 2px 6px rgba(5,150,105,0.25);">
                        <?= icon('truck') ?> 🚀 Bàn giao đơn vị vận chuyển & Bắt đầu giao hàng ➔
                    </button>
                    <small style="color: #64748b;">Hệ thống sẽ ghi nhận thời điểm xuất kho và gửi mã tra cứu đến khách hàng.</small>
                </div>
            </form>
        </div>

    <?php elseif ($order['status'] === 'shipping'): ?>
        <!-- Bước 3: Đang giao hàng -> Theo dõi hành trình & Xác nhận giao hàng thành công -->
        <div style="background: #eff6ff; border: 1.5px solid #93c5fd; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #dbeafe; padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 34px; height: 34px; border-radius: 6px; background: #004098; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <?= icon('truck') ?>
                    </div>
                    <div>
                        <strong style="font-size: 1rem; color: #1e3a8a;">Đang Trong Quá Trình Vận Chuyển</strong>
                        <div style="font-size: 0.8rem; color: #2563eb;">
                            Xuất kho bàn giao lúc: <strong><?= !empty($order['shipped_at']) ? date('d/m/Y H:i', strtotime($order['shipped_at'])) : 'Đã bàn giao' ?></strong>
                        </div>
                    </div>
                </div>
                <span class="badge" style="background: #2563eb; color: #fff; font-weight: 700; padding: 0.35rem 0.75rem;">
                    Đang trên đường giao
                </span>
            </div>

            <!-- Thẻ thông tin vận đơn hiện tại -->
            <div style="background: #fff; border: 1px solid #bfdbfe; border-radius: 7px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: center;">
                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Hãng vận chuyển:</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: <?= $carrierInfo['color'] ?? '#0f172a' ?>; margin-top: 2px;">
                            <?= e($carrierInfo['name'] ?? $order['shipping_carrier']) ?>
                        </div>
                        <small style="color: #64748b;">Hotline CSKH: <strong><?= e($carrierInfo['hotline'] ?? '–') ?></strong></small>
                    </div>

                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Mã vận đơn tra cứu:</div>
                        <div style="font-family: monospace; font-size: 1.1rem; font-weight: 800; color: #004098; margin-top: 2px;">
                            <?= e($order['tracking_code'] ?: 'PNS-' . $order['order_code']) ?>
                        </div>
                        <?php if (!empty($order['shipping_note'])): ?>
                            <small style="color: #64748b; display: block; margin-top: 2px;">Ghi chú: <?= e($order['shipping_note']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div style="text-align: right;">
                        <?php if (!empty($trackingUrl)): ?>
                            <a href="<?= e($trackingUrl) ?>" target="_blank" class="btn btn--outline btn--sm" style="color: <?= $carrierInfo['color'] ?? '#004098' ?>; border-color: <?= $carrierInfo['color'] ?? '#004098' ?>; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                🔍 Tra cứu website hãng &nearr;
                            </a>
                        <?php else: ?>
                            <span style="font-size: 0.82rem; color: #64748b;">Theo dõi qua hotline nội bộ</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- XÁC NHẬN GIAO HÀNG THÀNH CÔNG -->
            <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 7px; padding: 1.1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-weight: 700; color: #065f46; font-size: 0.95rem;">
                        ✓ Xác nhận giao hàng & Thu tiền hoàn tất
                    </div>
                    <div style="font-size: 0.82rem; color: #047857; margin-top: 2px;">
                        Bấm nút bên cạnh khi shipper/tài xế đã bàn giao sản phẩm tới tay khách hàng và ký biên bản nhận hàng.
                    </div>
                </div>
                <form action="<?= url('/admin/don-hang/trang-thai/' . $order['id']) ?>" method="post" style="margin: 0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn" style="background: #059669; color: #fff; font-weight: 700; padding: 0.65rem 1.5rem; border-radius: 7px; border: none; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 3px 8px rgba(5,150,105,0.3); font-size: 0.92rem;">
                        <?= icon('check') ?> Xác Nhận Đã Giao Hàng Thành Công
                    </button>
                </form>
            </div>
        </div>

    <?php elseif ($order['status'] === 'completed'): ?>
        <!-- Bước 4: Hoàn tất -> Thẻ xác nhận giao hàng thành công -->
        <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; padding: 1.25rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid #d1fae5; padding-bottom: 0.85rem; margin-bottom: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                        <?= icon('check') ?>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #065f46; font-size: 1.05rem;">Đơn hàng đã giao nhận thành công & Hoàn tất quy trình</div>
                        <div style="font-size: 0.82rem; color: #047857; margin-top: 2px;">
                            Thời điểm hoàn tất: <strong><?= !empty($order['delivered_at']) ? date('d/m/Y H:i', strtotime($order['delivered_at'])) : date('d/m/Y H:i', strtotime($order['updated_at'] ?? 'now')) ?></strong>
                        </div>
                    </div>
                </div>
                <span class="badge badge--success" style="padding: 0.45rem 1rem; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <?= icon('check') ?> Đã Hoàn Tất
                </span>
            </div>

            <!-- Chi tiết bàn giao -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; font-size: 0.88rem;">
                <div>
                    <span style="color: #065f46; font-weight: 600;">Đơn vị vận chuyển:</span>
                    <strong style="color: <?= $carrierInfo['color'] ?? '#065f46' ?>; display: block;"><?= e($carrierInfo['name'] ?? ($order['shipping_carrier'] ?: 'Xe Kho Panasonic')) ?></strong>
                </div>
                <div>
                    <span style="color: #065f46; font-weight: 600;">Mã vận đơn:</span>
                    <strong style="font-family: monospace; display: block; color: #004098;"><?= e($order['tracking_code'] ?: 'PNS-' . $order['order_code']) ?></strong>
                </div>
                <div>
                    <span style="color: #065f46; font-weight: 600;">Thời gian xuất kho:</span>
                    <div style="color: #334155;"><?= !empty($order['shipped_at']) ? date('d/m/Y H:i', strtotime($order['shipped_at'])) : '–' ?></div>
                </div>
                <div>
                    <span style="color: #065f46; font-weight: 600;">Thời gian giao nhận:</span>
                    <div style="color: #334155;"><?= !empty($order['delivered_at']) ? date('d/m/Y H:i', strtotime($order['delivered_at'])) : '–' ?></div>
                </div>
            </div>
        </div>

    <?php elseif ($order['status'] === 'cancelled'): ?>
        <!-- Khi đơn hàng đã Huỷ: Khóa hoàn toàn -->
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #dc2626; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <?= icon('alert') ?>
                </div>
                <div>
                    <div style="font-weight: 700; color: #991b1b; font-size: 1rem;">Đơn hàng đã bị Huỷ</div>
                    <div style="font-size: 0.82rem; color: #b91c1c; margin-top: 2px;">
                        Tồn kho đã được hoàn lại. Đơn hàng kết thúc quy trình.
                    </div>
                </div>
            </div>
            <span class="badge badge--danger" style="padding: 0.45rem 0.9rem; font-size: 0.85rem; font-weight: 700;">
                Đã Huỷ
            </span>
        </div>
    <?php endif; ?>

    <!-- Menu phụ tùy chỉnh tiến bước hoặc huỷ đơn (dành cho quản trị viên) -->
    <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 0.85rem 1.25rem; border-radius: 7px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.85rem;">
            <div style="font-size: 0.82rem; color: #64748b;">
                Tùy chọn khác: <strong id="currentStatusBadge" style="color: #004098;"><?= e($statuses[$order['status']] ?? $order['status']) ?></strong>
            </div>

            <form action="<?= url('/admin/don-hang/trang-thai/' . $order['id']) ?>" method="post" style="display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap; margin: 0;">
                <?= csrf_field() ?>
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <label style="font-size: 0.82rem; font-weight: 600; color: #475569; white-space: nowrap;">Chuyển nhanh sang:</label>
                    <select name="status" required style="min-width: 220px; height: 38px !important; line-height: 1.4 !important; padding: 0.35rem 0.75rem !important; font-size: 0.85rem !important; font-weight: 600; border: 1.5px solid #004098 !important; border-radius: 6px; background: #fff !important; color: #0f172a !important; box-sizing: border-box !important;">
                        <option value="" disabled selected>-- Chọn bước tiếp theo --</option>
                        <?php foreach ($allowedStatuses as $stVal => $stText): ?>
                            <option value="<?= e($stVal) ?>" style="<?= $stVal === 'cancelled' ? 'color: #dc2626;' : 'color: #004098; font-weight: 600;' ?>">
                                <?= $stVal === 'cancelled' ? '✕ Huỷ: ' : '➔ Tiến: ' ?><?= e($stText) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn--outline" style="height: 38px; padding: 0 1rem; font-weight: 700; border-radius: 6px;">
                    Cập nhật
                </button>
            </form>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div>
            <h4 style="margin-top: 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.4rem;">Thông tin người đặt hàng</h4>
            <p><strong>Họ tên:</strong> <?= e($order['customer_name']) ?></p>
            <p><strong>Điện thoại:</strong> <a href="tel:<?= e($order['phone']) ?>" style="color: #004098; font-weight: 700;"><?= e($order['phone']) ?></a></p>
            <p><strong>Email:</strong> <?= e($order['email'] ?: '–') ?></p>
            <p><strong>Địa chỉ nhận hàng:</strong> <?= e($order['address']) ?></p>
            <p><strong>Loại tài khoản:</strong> 
                <?php if (!empty($order['user_id'])): ?>
                    <span class="badge badge--success">Thành viên đã đăng ký (#<?= (int)$order['user_id'] ?>)</span>
                <?php else: ?>
                    <span class="badge" style="background:#f1f5f9; color:#64748b;">Khách vãng lai</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <h4 style="margin-top: 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.4rem;">Thông tin xuất hóa đơn & Ghi chú</h4>
            <p><strong>Công ty:</strong> <?= e($order['company'] ?: 'Khách hàng cá nhân') ?></p>
            <p><strong>Mã số thuế:</strong> <?= e($order['tax_code'] ?: '–') ?></p>
            <p><strong>Hình thức TT:</strong> <?= $order['payment_method'] === 'bank_transfer' ? 'Chuyển khoản công ty (VietQR / ACB)' : 'COD (Tiền mặt khi nhận)' ?></p>
            <p><strong>Ghi chú đặt hàng:</strong> <?= e($order['note'] ?: '–') ?></p>
        </div>
    </div>

    <h4>Chi tiết danh mục thiết bị đã đặt</h4>
    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên thiết bị</th>
                    <th>Model Code</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($items as $it): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><strong><?= e($it['product_name']) ?></strong></td>
                        <td><code><?= e($it['model_code']) ?></code></td>
                        <td>
                            <?= money($it['unit_price']) ?>
                            <?php if ($it['price_type'] === 'wholesale'): ?>
                                <span class="badge badge--success font-xs">Giá CT</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$it['quantity'] ?></td>
                        <td><strong><?= money($it['line_total']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Tổng theo giá niêm yết:</td>
                    <td><?= money($order['subtotal']) ?></td>
                </tr>
                <?php if ($order['discount'] > 0): ?>
                    <tr class="text-success">
                        <td colspan="5" style="text-align: right;">Chiết khấu công trình:</td>
                        <td>-<?= money($order['discount']) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 1.1rem;">Tổng thanh toán cuối cùng:</td>
                    <td><strong class="price price--lg"><?= money($order['total']) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
function autoUpdatePrefix() {
    const carrier = document.getElementById('shippingCarrierSelect')?.value || 'ghtk';
    const trackingInput = document.getElementById('trackingCodeInput');
    if (!trackingInput || trackingInput.value.trim() !== '') return;
    generateTrackingCode();
}

function generateTrackingCode() {
    const carrierSelect = document.getElementById('shippingCarrierSelect');
    const trackingInput = document.getElementById('trackingCodeInput');
    if (!trackingInput) return;
    
    const prefixes = {
        'ghtk': 'GHTK',
        'ghn': 'GHN',
        'viettel_post': 'VTP',
        'vnpost': 'VNPOST',
        'ahamove': 'AHA',
        'panasonic_truck': 'PNS-TRUCK'
    };
    const carrier = carrierSelect ? carrierSelect.value : 'ghtk';
    const prefix = prefixes[carrier] || 'PNS';
    const code = '<?= preg_replace('/[^0-9A-Z]/', '', strtoupper($order['order_code'])) ?>';
    const rand = Math.floor(1000 + Math.random() * 9000);
    trackingInput.value = prefix + '-' + code + '-' + rand;
}
</script>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div>
            <h4 style="margin-top: 0;">Thông tin người đặt</h4>
            <p><strong>Họ tên:</strong> <?= e($order['customer_name']) ?></p>
            <p><strong>Điện thoại:</strong> <a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></p>
            <p><strong>Email:</strong> <?= e($order['email'] ?: '–') ?></p>
            <p><strong>Địa chỉ nhận hàng:</strong> <?= e($order['address']) ?></p>
            <p><strong>Loại tài khoản:</strong> 
                <?php if (!empty($order['user_id'])): ?>
                    <span class="badge badge--success">Thành viên đã đăng ký (#<?= (int)$order['user_id'] ?>)</span>
                <?php else: ?>
                    <span class="badge" style="background:#f1f5f9; color:#64748b;">Khách vãng lai</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <h4 style="margin-top: 0;">Thông tin xuất hóa đơn & Ghi chú</h4>
            <p><strong>Công ty:</strong> <?= e($order['company'] ?: 'Khách hàng cá nhân') ?></p>
            <p><strong>Mã số thuế:</strong> <?= e($order['tax_code'] ?: '–') ?></p>
            <p><strong>Hình thức TT:</strong> <?= $order['payment_method'] === 'bank_transfer' ? 'Chuyển khoản công ty' : 'COD' ?></p>
            <p><strong>Ghi chú:</strong> <?= e($order['note'] ?: '–') ?></p>
        </div>
    </div>

    <h4>Chi tiết danh mục thiết bị đã đặt</h4>
    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên thiết bị</th>
                    <th>Model Code</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($items as $it): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><strong><?= e($it['product_name']) ?></strong></td>
                        <td><code><?= e($it['model_code']) ?></code></td>
                        <td>
                            <?= money($it['unit_price']) ?>
                            <?php if ($it['price_type'] === 'wholesale'): ?>
                                <span class="badge badge--success font-xs">Giá CT</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$it['quantity'] ?></td>
                        <td><strong><?= money($it['line_total']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Tổng theo giá niêm yết:</td>
                    <td><?= money($order['subtotal']) ?></td>
                </tr>
                <?php if ($order['discount'] > 0): ?>
                    <tr class="text-success">
                        <td colspan="5" style="text-align: right;">Chiết khấu công trình:</td>
                        <td>-<?= money($order['discount']) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 1.1rem;">Tổng thanh toán cuối cùng:</td>
                    <td><strong class="price price--lg"><?= money($order['total']) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
