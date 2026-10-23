<div class="admin-card mb-4">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; color: #0f172a;">Quản Lý Thành Viên & Khách Hàng</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                Tổng số thành viên: <strong><?= number_format($result['total']) ?></strong> khách hàng đã đăng ký hoặc đăng nhập qua Google / Facebook.
            </p>
        </div>

        <form method="get" action="<?= url('/admin/thanh-vien') ?>" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <select name="provider" class="form-control" style="width: auto; padding: 0.45rem 0.75rem;">
                <option value="">Tất cả kênh đăng nhập</option>
                <option value="local" <?= ($provider ?? '') === 'local' ? 'selected' : '' ?>>Email / Mật khẩu</option>
                <option value="google" <?= ($provider ?? '') === 'google' ? 'selected' : '' ?>>Google OAuth</option>
                <option value="facebook" <?= ($provider ?? '') === 'facebook' ? 'selected' : '' ?>>Facebook</option>
            </select>

            <input type="search" name="q" value="<?= e($keyword ?? '') ?>" placeholder="Tìm tên, email, SĐT..." class="form-control" style="width: 220px; padding: 0.45rem 0.75rem;">
            <button type="submit" class="btn btn--primary" style="padding: 0.45rem 1rem;">Tìm</button>
            <?php if (!empty($keyword) || !empty($provider)): ?>
                <a href="<?= url('/admin/thanh-vien') ?>" class="btn btn--outline" style="padding: 0.45rem 0.75rem;">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Kênh đăng nhập</th>
                    <th>Đơn mua</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result['items'])): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                            Không tìm thấy thành viên nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result['items'] as $u): ?>
                        <tr>
                            <td>#<?= (int)$u['id'] ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                    <?php if (!empty($u['avatar'])): ?>
                                        <img src="<?= e($u['avatar']) ?>" alt="<?= e($u['name']) ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #004098; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                                            <?= mb_strtoupper(mb_substr($u['name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= e($u['name']) ?></strong><br>
                                        <small class="text-muted"><?= e($u['role']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['phone'] ?: '–') ?></td>
                            <td>
                                <?php if ($u['provider'] === 'google'): ?>
                                    <span class="badge" style="background:#e8f0fe; color:#1a73e8; font-weight:600;">Google Sign-In</span>
                                <?php elseif ($u['provider'] === 'facebook'): ?>
                                    <span class="badge" style="background:#e7f3ff; color:#1877f2; font-weight:600;">Facebook</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#f1f5f9; color:#475569;">Email cục bộ</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #004098;"><?= (int)$u['order_count'] ?></strong> đơn hàng
                            </td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></small>
                            </td>
                            <td>
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge badge--success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge badge--danger">Bị khóa</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <form method="post" action="<?= url('/admin/thanh-vien/trang-thai/' . $u['id']) ?>" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này?')">
                                    <?= csrf_field() ?>
                                    <?php if ($u['status'] === 'active'): ?>
                                        <button type="submit" class="btn btn--sm" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;">Khóa tài khoản</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn--sm" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">Mở khóa</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($result['pages'] > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.35rem; margin-top: 1.5rem;">
            <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                <a href="<?= query_with(['page' => $p]) ?>" class="btn btn--sm <?= $p === $result['page'] ? 'btn--primary' : 'btn--outline' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
