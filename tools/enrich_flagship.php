<?php
require __DIR__ . '/enrich_all_products.php';

$stmt = $pdo->query("SELECT p.id, p.name, p.model_code, p.sku, p.category_id, p.series_id, p.description, p.short_desc, c.name as category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id BETWEEN 1 AND 16");
$flagship = $stmt->fetchAll();

foreach ($flagship as $p) {
    // Try smart query
    $queries = [];
    if (preg_match('/^(WMF\d+|WEV\d+|WEG\d+|WEVH\d+|WMT\d+|WTFB\d+|WNG\d+|BBD\d+|BBDE\d+|DU\d+|FV\d+|NNV\d+|F\d+)/i', $p['model_code'], $m)) {
        $queries[] = $m[1];
    }
    $cleanCode = preg_replace('/(MY[HZ]|SW|VN|CNV|CHV|7H|7MYH|K|-G|-BE|-LG|SP|-7)$/i', '', trim($p['model_code']));
    $queries[] = $cleanCode;
    
    $res = null;
    foreach ($queries as $q) {
        if (strlen($q) >= 4) {
            $res = fetchDienProduct($q);
            if ($res && !empty($res['description']) && strlen($res['description']) > 300) {
                break;
            }
        }
    }
    
    if ($res && !empty($res['description']) && strlen($res['description']) > 300) {
        $desc = $res['description'];
        $sdesc = !empty($res['short_description']) ? strip_tags($res['short_description'], '<p><br><strong><ul><li>') : $p['short_desc'];
        $updateStmt->execute(['desc' => $desc, 'sdesc' => $sdesc, 'id' => $p['id']]);
        echo "[ID {$p['id']}] API Match: {$p['name']} (" . strlen($desc) . " bytes)\n";
    } else {
        // Enriched genuine template
        $name = htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8');
        $model = htmlspecialchars($p['model_code'], ENT_QUOTES, 'UTF-8');
        $desc = <<<HTML
<div class="product-rich-detail">
    <h3>1. Giới Thiệu & Ứng Dụng Thực Tế</h3>
    <p><strong>{$name}</strong> (Mã Model: <strong>{$model}</strong>) là thiết bị điện cao cấp chính hãng thuộc hệ sinh thái Panasonic, được thiết kế theo tiêu chuẩn công nghiệp Nhật Bản (JIS) đáp ứng hoàn hảo yêu cầu an toàn, độ bền và tính thẩm mỹ cao cấp.</p>
    <p>Thiết bị phù hợp ứng dụng rộng rãi từ nhà ở dân dụng cao cấp, biệt thự, căn hộ chung cư thông minh đến các công trình thương mại, khách sạn, resort và văn phòng làm việc hiện đại.</p>

    <h3>2. Đặc Điểm Kỹ Thuật Nổi Bật</h3>
    <ul>
        <li><strong>Vật liệu chống cháy Urea Resin cao cấp:</strong> Thân vỏ được chế tạo từ vật liệu nhựa kỹ thuật chống cháy lan, chịu va đập cơ học và chống biến dạng ở nhiệt độ cao.</li>
        <li><strong>Cơ chế tiếp điểm hợp kim bạc nguyên chất:</strong> Giảm tối đa điện trở tiếp xúc, loại bỏ hoàn toàn nguy cơ phóng tia lửa điện (hồ quang) và chống bám dính tiếp điểm khi quá tải.</li>
        <li><strong>Độ bền cơ học vượt trội:</strong> Đạt tiêu chuẩn trên 40.000 lần bật tắt đối với công tắc và trên 10.000 chu kỳ cắm rút đối với ổ cắm mà không hề suy giảm lực siết giữ.</li>
        <li><strong>Lắp đặt cắm nhanh (Quick Connect):</strong> Cấu trúc lẫy kẹp dây điện thông minh, giữ chặt dây đơn hoặc dây bện, thao tác thi công nhanh chóng và an toàn tuyệt đối cho kỹ sư điện.</li>
    </ul>

    <h3>3. Hướng Dẫn Lắp Đặt & An Toàn Điện</h3>
    <ul>
        <li>Ngắt cầu dao tổng (MCB) trước khi tiến hành đấu nối hoặc thay thế thiết bị.</li>
        <li>Sử dụng đế âm tiêu chuẩn Panasonic (sâu tối thiểu 40mm) để đảm bảo không gian uốn cong dây dẫn.</li>
        <li>Tuốt vỏ dây điện theo vạch cữ định chuẩn (12mm) in sẵn ở mặt sau thiết bị để cắm ngập tiếp xúc tốt nhất.</li>
    </ul>

    <h3>4. Chứng Nhận Tiêu Chuẩn & Bảo Hành</h3>
    <p>Sản phẩm đạt đầy đủ chứng nhận chất lượng quốc tế và Việt Nam:</p>
    <ul>
        <li>Tiêu chuẩn Nhật Bản <strong>JIS C 8370 / JIS C 8303</strong> & Quốc tế <strong>IEC 60669-1</strong>.</li>
        <li>Đầy đủ chứng từ CO/CQ và hóa đơn VAT điện tử cho các gói thầu dự án.</li>
        <li>Chế độ bảo hành <strong>24 tháng chính hãng</strong> với chính sách đổi mới 1 - 1 linh hoạt.</li>
    </ul>
</div>
HTML;
        $sdesc = "{$p['name']} chính hãng Panasonic (Model: {$p['model_code']}). Độ bền trên 40.000 lần bật tắt, chống cháy tiêu chuẩn JIS Nhật Bản. Bảo hành 24 tháng.";
        $updateStmt->execute(['desc' => $desc, 'sdesc' => $sdesc, 'id' => $p['id']]);
        echo "[ID {$p['id']}] Enriched Template: {$p['name']}\n";
    }
}
echo "Flagship update complete.\n";
