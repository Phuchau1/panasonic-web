<div class="container user-profile-page">
    <div class="user-profile-layout">
        <!-- Sidebar tài khoản -->
        <aside class="user-sidebar">
            <div class="user-sidebar__card">
                <div class="user-sidebar__profile-row">
                    <div class="user-avatar-wrap">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" class="profile-avatar">
                        <?php else: ?>
                            <div class="profile-avatar-initial"><?= mb_strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="user-sidebar__profile-meta">
                        <h2 class="profile-name"><?= e($user['name']) ?></h2>
                        <span class="profile-badge"><?= $user['provider'] !== 'local' ? ('Liên kết ' . ucfirst($user['provider'])) : 'Thành viên chính thức' ?></span>
                        <p class="profile-email"><?= e($user['email']) ?></p>
                    </div>
                </div>

                <div class="user-sidebar__nav">
                    <a href="#info" class="user-nav-item is-active" data-profile-tab="info">
                        <?= icon('user') ?> <span>Thông tin tài khoản</span>
                    </a>
                    <a href="#orders" class="user-nav-item" data-profile-tab="orders">
                        <?= icon('cart') ?> <span>Đơn hàng đã đặt (<?= count($orders) ?>)</span>
                    </a>
                    <button type="button" class="user-nav-item btn-profile-logout" id="profileLogoutBtn">
                        <?= icon('logout') ?> <span>Đăng xuất</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Nội dung chính -->
        <div class="user-main-content">
            <!-- Tab Thông tin cá nhân -->
            <section class="profile-tab-section is-active" id="profileTabInfo">
                <div class="profile-box">
                    <h3 class="profile-box-title">Thông Tin Cá Nhân & Giao Nhận</h3>
                    <p class="profile-box-desc">Cập nhật thông tin nhận hàng và số điện thoại liên hệ để đội ngũ kỹ thuật giao hàng chính xác.</p>

                    <div class="auth-alert" id="profileAlert" hidden role="alert">
                        <span class="auth-alert__icon" id="profileAlertIcon"></span>
                        <span class="auth-alert__text" id="profileAlertText"></span>
                    </div>

                    <form id="formUpdateProfile" class="profile-form">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                        <div class="profile-form-grid">
                            <div class="auth-form__group">
                                <label class="auth-form__label">Họ và tên</label>
                                <input type="text" name="name" class="auth-input" value="<?= e($user['name']) ?>" required>
                            </div>

                            <div class="auth-form__group">
                                <label class="auth-form__label">Địa chỉ Email</label>
                                <input type="email" class="auth-input input-readonly" value="<?= e($user['email']) ?>" readonly title="Email không thể thay đổi">
                            </div>

                            <div class="auth-form__group">
                                <label class="auth-form__label">Số điện thoại</label>
                                <input type="tel" name="phone" class="auth-input" value="<?= e($user['phone'] ?? '') ?>" placeholder="0909xxxxxx">
                            </div>

                            <div class="auth-form__group">
                                <label class="auth-form__label">Địa chỉ nhận hàng mặc định</label>
                                <input type="text" name="address" class="auth-input" value="<?= e($user['address'] ?? '') ?>" placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện...">
                            </div>
                        </div>

                        <div class="profile-form-footer">
                            <button type="submit" class="btn-profile-save" id="btnProfileSave">
                                <span class="btn-text">Lưu Thay Đổi</span>
                                <span class="btn-spinner" hidden></span>
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Tab Đơn hàng đã đặt -->
            <section class="profile-tab-section" id="profileTabOrders" hidden>
                <div class="profile-box">
                    <h3 class="profile-box-title">Lịch Sử Đơn Hàng</h3>
                    <p class="profile-box-desc">Theo dõi tiến độ xử lý và hành trình giao hàng thiết bị điện Panasonic của bạn.</p>

                    <?php if (empty($orders)): ?>
                        <div class="empty-orders-box">
                            <?= icon('cart', 'empty-cart-icon') ?>
                            <h4>Bạn chưa có đơn hàng nào</h4>
                            <p>Hãy khám phá các thiết bị điện công tắc, ổ cắm, MCB Panasonic chính hãng và đặt hàng ngay.</p>
                            <a href="<?= url('/san-pham') ?>" class="btn-shop-now">Khám phá sản phẩm ngay »</a>
                        </div>
                    <?php else: ?>
                        <!-- Bảng đơn hàng cho Desktop -->
                        <div class="orders-table-wrap orders-table-desktop">
                            <table class="user-orders-table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Người nhận</th>
                                        <th>Tổng tiền</th>
                                        <th>Hình thức</th>
                                        <th>Trạng thái</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $od): ?>
                                        <tr>
                                            <td><strong>#<?= e($od['order_code']) ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($od['created_at'])) ?></td>
                                            <td>
                                                <?= e($od['customer_name']) ?><br>
                                                <small class="text-muted"><?= e($od['phone']) ?></small>
                                            </td>
                                            <td><strong class="order-price"><?= money($od['total']) ?></strong></td>
                                            <td>
                                                <span class="pay-method-badge"><?= $od['payment_method'] === 'bank_transfer' ? 'Chuyển khoản' : 'COD' ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusMap = [
                                                    'pending'   => ['label' => 'Chờ xác nhận', 'cls' => 'badge-pending'],
                                                    'confirmed' => ['label' => 'Đã xác nhận', 'cls' => 'badge-confirmed'],
                                                    'shipping'  => ['label' => 'Đang giao hàng', 'cls' => 'badge-shipping'],
                                                    'completed' => ['label' => 'Hoàn tất', 'cls' => 'badge-completed'],
                                                    'cancelled' => ['label' => 'Đã hủy', 'cls' => 'badge-cancelled'],
                                                ];
                                                $st = $statusMap[$od['status']] ?? ['label' => $od['status'], 'cls' => ''];
                                                ?>
                                                <span class="order-status-badge <?= $st['cls'] ?>"><?= $st['label'] ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= url('/dat-hang-thanh-cong/' . $od['order_code']) ?>" class="btn-order-view">Xem »</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Danh sách Thẻ Đơn Hàng Tối Ưu Cho Điện Thoại (Mobile Cards) -->
                        <div class="orders-cards-mobile">
                            <?php foreach ($orders as $od): 
                                $statusMap = [
                                    'pending'   => ['label' => 'Chờ xác nhận', 'cls' => 'badge-pending'],
                                    'confirmed' => ['label' => 'Đã xác nhận', 'cls' => 'badge-confirmed'],
                                    'shipping'  => ['label' => 'Đang giao hàng', 'cls' => 'badge-shipping'],
                                    'completed' => ['label' => 'Hoàn tất', 'cls' => 'badge-completed'],
                                    'cancelled' => ['label' => 'Đã hủy', 'cls' => 'badge-cancelled'],
                                ];
                                $st = $statusMap[$od['status']] ?? ['label' => $od['status'], 'cls' => ''];
                            ?>
                                <div class="user-order-card">
                                    <div class="user-order-card__header">
                                        <div class="user-order-card__code">
                                            <span class="order-label">MÃ ĐƠN:</span>
                                            <strong>#<?= e($od['order_code']) ?></strong>
                                        </div>
                                        <span class="order-status-badge <?= $st['cls'] ?>"><?= $st['label'] ?></span>
                                    </div>
                                    
                                    <div class="user-order-card__body">
                                        <div class="user-order-card__item">
                                            <span class="card-item-label"><?= icon('calendar') ?> Ngày đặt:</span>
                                            <span class="card-item-val"><?= date('d/m/Y H:i', strtotime($od['created_at'])) ?></span>
                                        </div>
                                        <div class="user-order-card__item">
                                            <span class="card-item-label"><?= icon('user') ?> Người nhận:</span>
                                            <span class="card-item-val"><strong><?= e($od['customer_name']) ?></strong> (<?= e($od['phone']) ?>)</span>
                                        </div>
                                        <div class="user-order-card__item">
                                            <span class="card-item-label"><?= icon('shield') ?> Thanh toán:</span>
                                            <span class="pay-method-badge"><?= $od['payment_method'] === 'bank_transfer' ? 'Chuyển khoản QR' : 'COD (Tiền mặt)' ?></span>
                                        </div>
                                    </div>

                                    <div class="user-order-card__footer">
                                        <div class="user-order-card__total">
                                            <small>Tổng thanh toán</small>
                                            <strong class="order-price"><?= money($od['total']) ?></strong>
                                        </div>
                                        <a href="<?= url('/dat-hang-thanh-cong/' . $od['order_code']) ?>" class="btn-order-view-card">
                                            Chi tiết »
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
