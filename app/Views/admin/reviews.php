<div class="admin-card mb-4">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; color: #0f172a;">Quản Lý Đánh Giá Sản Phẩm</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                Tổng cộng <strong><?= number_format($result['total']) ?></strong> lượt đánh giá từ khách hàng thực tế trên toàn bộ sản phẩm.
            </p>
        </div>

        <form method="get" action="<?= url('/admin/danh-gia') ?>" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <select name="rating" class="form-control" style="width: auto; padding: 0.45rem 0.75rem;">
                <option value="">Tất cả mức sao</option>
                <?php for ($s = 5; $s >= 1; $s--): ?>
                    <option value="<?= $s ?>" <?= ($rating ?? 0) === $s ? 'selected' : '' ?>><?= $s ?> sao (★★★★★)</option>
                <?php endfor; ?>
            </select>

            <input type="search" name="q" value="<?= e($keyword ?? '') ?>" placeholder="Tìm nội dung, tên khách, model..." class="form-control" style="width: 220px; padding: 0.45rem 0.75rem;">
            <button type="submit" class="btn btn--primary" style="padding: 0.45rem 1rem;">Tìm</button>
            <?php if (!empty($keyword) || !empty($rating)): ?>
                <a href="<?= url('/admin/danh-gia') ?>" class="btn btn--outline" style="padding: 0.45rem 0.75rem;">Xóa lọc</a>
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
                    <th>Sản phẩm Panasonic</th>
                    <th>Khách hàng</th>
                    <th>Số sao</th>
                    <th>Nhận xét chi tiết</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right; width: 140px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result['items'])): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                            Không có đánh giá nào phù hợp với bộ lọc.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result['items'] as $rev): ?>
                        <tr id="review-row-<?= (int)$rev['id'] ?>" data-review-id="<?= (int)$rev['id'] ?>" data-author="<?= e($rev['author_name']) ?>" data-product="<?= e($rev['model_code'] ?? 'SP') ?>" data-rating="<?= (int)$rev['rating'] ?>">
                            <td>#<?= (int)$rev['id'] ?></td>
                            <td>
                                <strong><?= e($rev['model_code'] ?? 'SP') ?></strong><br>
                                <a href="<?= url('/san-pham/' . ($rev['product_slug'] ?? '')) ?>" target="_blank" style="font-size: 0.8rem; color: #004098;">
                                    <?= mb_strimwidth($rev['product_name'] ?? '', 0, 35, '...') ?>
                                </a>
                            </td>
                            <td>
                                <strong><?= e($rev['author_name']) ?></strong><br>
                                <small class="text-muted"><?= e($rev['author_phone'] ?: '–') ?></small>
                            </td>
                            <td>
                                <span style="color: #f59e0b; font-weight: 700;">
                                    <?= str_repeat('★', (int)$rev['rating']) ?>
                                    <span style="color: #cbd5e1;"><?= str_repeat('★', 5 - (int)$rev['rating']) ?></span>
                                </span>
                            </td>
                            <td>
                                <div class="review-customer-comment" style="max-width: 340px; font-size: 0.85rem; line-height: 1.45; color: #1e293b;">
                                    <?= nl2br(e($rev['comment'])) ?>
                                </div>
                                <div id="reply-container-<?= (int)$rev['id'] ?>" style="margin-top: 8px;">
                                    <?php if (!empty($rev['admin_reply'])): ?>
                                        <div class="admin-reply-box" style="background: #f0fdf4; border-left: 3px solid #16a34a; padding: 6px 10px; border-radius: 4px; font-size: 0.8rem; color: #166534;">
                                            <div style="font-weight: 700; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                                                <span>💬 Phản hồi của QTV:</span>
                                                <small style="color: #64748b; font-weight: normal;"><?= !empty($rev['admin_reply_at']) ? date('d/m/Y H:i', strtotime($rev['admin_reply_at'])) : '' ?></small>
                                            </div>
                                            <div class="reply-text-content" style="white-space: pre-line;"><?= e($rev['admin_reply']) ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="no-reply-tag" style="display: inline-block; font-size: 0.73rem; color: #94a3b8; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 2px 8px; border-radius: 4px;">
                                            Chưa có phản hồi
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></small>
                            </td>
                            <td>
                                <?php if ((int)$rev['status'] === 1): ?>
                                    <span class="badge badge--success">Hiển thị</span>
                                <?php else: ?>
                                    <span class="badge badge--danger">Đã ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" 
                                    class="btn btn--sm btn--primary btn-open-reply" 
                                    style="background: #004098; border-color: #004098;"
                                    data-id="<?= (int)$rev['id'] ?>"
                                    data-author="<?= e($rev['author_name']) ?>"
                                    data-product="<?= e(($rev['model_code'] ?? 'SP') . ' - ' . ($rev['product_name'] ?? '')) ?>"
                                    data-comment="<?= e($rev['comment']) ?>"
                                    data-reply="<?= e($rev['admin_reply'] ?? '') ?>">
                                    <?= !empty($rev['admin_reply']) ? '✏ Sửa trả lời' : '💬 Trả lời' ?>
                                </button>

                                <form method="post" action="<?= url('/admin/danh-gia/trang-thai/' . $rev['id']) ?>" style="display: inline-block; margin-left: 4px;">
                                    <?= csrf_field() ?>
                                    <?php if ((int)$rev['status'] === 1): ?>
                                        <button type="submit" class="btn btn--sm btn--outline" title="Ẩn đánh giá khỏi trang sản phẩm">Ẩn</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn--sm" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;" title="Hiện đánh giá lên trang sản phẩm">Hiện</button>
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

<!-- MODAL TRẢ LỜI ĐÁNH GIÁ (AJAX) -->
<div id="replyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.65); z-index:99999; backdrop-filter:blur(3px); align-items:center; justify-content:center;">
    <div style="background:#ffffff; width:100%; max-width:580px; border-radius:12px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden; animation:modalPop 0.25s ease;">
        <div style="background:#004098; color:#ffffff; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.2rem;">💬</span>
                <h3 style="margin:0; font-size:1.1rem; font-weight:700; color:#ffffff;" id="replyModalTitle">Trả Lời Đánh Giá Khách Hàng</h3>
            </div>
            <button type="button" onclick="closeReplyModal()" style="background:transparent; border:none; color:#ffffff; font-size:1.5rem; cursor:pointer; line-height:1;">&times;</button>
        </div>

        <form id="replyReviewForm" style="padding:20px;">
            <?= csrf_field() ?>
            <input type="hidden" name="review_id" id="modalReviewId" value="">

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:0.875rem;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <strong style="color:#0f172a;" id="modalAuthorName">Khách hàng</strong>
                    <span style="color:#64748b; font-size:0.8rem;" id="modalProductCode">Model</span>
                </div>
                <div style="color:#334155; line-height:1.45; font-style:italic;" id="modalCustomerComment">
                    "Nội dung nhận xét"
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:0.875rem; font-weight:700; color:#0f172a; margin-bottom:6px;">
                    Nội dung phản hồi từ Quản trị viên (Admin):
                </label>
                <textarea name="reply" id="modalReplyText" rows="4" class="form-control" placeholder="Ví dụ: Cảm ơn Quý khách đã tin tưởng và lựa chọn thiết bị điện chính hãng Panasonic. Tổng Kho luôn sẵn sàng hỗ trợ bảo hành chính hãng và tư vấn kỹ thuật tốt nhất cho Quý khách!" style="width:100%; border:1.5px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:0.9rem; line-height:1.5; resize:vertical;"></textarea>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; border-top:1px solid #e2e8f0; padding-top:14px;">
                <button type="button" id="btnDeleteReply" onclick="clearReplyContent()" style="background:transparent; border:none; color:#ef4444; font-size:0.85rem; cursor:pointer; text-decoration:underline;">
                    Xóa câu trả lời
                </button>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn btn--outline" onclick="closeReplyModal()" style="padding:0.5rem 1rem;">Đóng</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitReply" style="background:#004098; padding:0.5rem 1.25rem;">
                        Lưu Phản Hồi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
let currentReplyBtn = null;

function openReplyModal(btn) {
    currentReplyBtn = btn;
    const id = btn.dataset.id;
    const author = btn.dataset.author;
    const product = btn.dataset.product;
    const comment = btn.dataset.comment;
    const reply = btn.dataset.reply || '';

    document.getElementById('modalReviewId').value = id;
    document.getElementById('modalAuthorName').textContent = author;
    document.getElementById('modalProductCode').textContent = product;
    document.getElementById('modalCustomerComment').textContent = `"${comment}"`;
    document.getElementById('modalReplyText').value = reply;

    const modalTitle = document.getElementById('replyModalTitle');
    if (modalTitle) {
        modalTitle.textContent = reply ? 'Chỉnh Sửa Phản Hồi Đánh Giá' : 'Trả Lời Đánh Giá Khách Hàng';
    }

    const delBtn = document.getElementById('btnDeleteReply');
    if (delBtn) {
        delBtn.style.display = reply ? 'inline-block' : 'none';
    }

    const modal = document.getElementById('replyModal');
    modal.style.display = 'flex';
    document.getElementById('modalReplyText').focus();
}

function closeReplyModal() {
    const modal = document.getElementById('replyModal');
    if (modal) modal.style.display = 'none';
}

function clearReplyContent() {
    if (confirm('Bạn có chắc muốn xóa câu trả lời của đánh giá này?')) {
        document.getElementById('modalReplyText').value = '';
        document.getElementById('replyReviewForm').requestSubmit();
    }
}

// Bắt sự kiện click mở modal
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-open-reply');
    if (btn) {
        openReplyModal(btn);
    }
});

// Bắt sự kiện submit form trả lời qua AJAX
document.getElementById('replyReviewForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const id = document.getElementById('modalReviewId').value;
    const replyText = document.getElementById('modalReplyText').value.trim();
    const submitBtn = document.getElementById('btnSubmitReply');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang lưu...';
    }

    try {
        const formData = new FormData(form);
        const res = await fetch(`<?= url('/admin/danh-gia/tra-loi/') ?>${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (res.ok && data.success) {
            closeReplyModal();

            // Cập nhật DOM của dòng đánh giá tương ứng
            const replyContainer = document.getElementById(`reply-container-${id}`);
            if (replyContainer) {
                if (data.reply) {
                    replyContainer.innerHTML = `
                        <div class="admin-reply-box" style="background: #f0fdf4; border-left: 3px solid #16a34a; padding: 6px 10px; border-radius: 4px; font-size: 0.8rem; color: #166534; animation: fadeIn 0.3s ease;">
                            <div style="font-weight: 700; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                                <span>💬 Phản hồi của QTV:</span>
                                <small style="color: #64748b; font-weight: normal;">${data.reply_at || 'Vừa xong'}</small>
                            </div>
                            <div class="reply-text-content" style="white-space: pre-line;">${escapeHtml(data.reply)}</div>
                        </div>
                    `;
                } else {
                    replyContainer.innerHTML = `
                        <span class="no-reply-tag" style="display: inline-block; font-size: 0.73rem; color: #94a3b8; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 2px 8px; border-radius: 4px;">
                            Chưa có phản hồi
                        </span>
                    `;
                }
            }

            if (currentReplyBtn) {
                currentReplyBtn.dataset.reply = data.reply || '';
                currentReplyBtn.textContent = data.reply ? '✏ Sửa trả lời' : '💬 Trả lời';
            }

            if (typeof showAdminToast === 'function') {
                showAdminToast(data.message, 'success');
            } else {
                alert(data.message);
            }
        } else {
            alert(data.message || 'Không thể lưu phản hồi.');
        }
    } catch (err) {
        alert('Có lỗi xảy ra khi kết nối máy chủ.');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Lưu Phản Hồi';
        }
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
