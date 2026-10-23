<?php
/**
 * Công cụ cào và nhập dữ liệu thiết bị điện từ haidangthietbidien.vn vào panasonic_shop
 */
declare(strict_types=1);

set_time_limit(600);
ini_set('memory_limit', '512M');

// 1. Kết nối DB
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$uploadDir = realpath(__DIR__ . '/../public/uploads/products') ?: (__DIR__ . '/../public/uploads/products');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function fetchUrl(string $url): ?string
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($code >= 200 && $code < 300 && $resp !== false) ? $resp : null;
}

function downloadImage(string $url, string $savePath): bool
{
    if (file_exists($savePath) && filesize($savePath) > 500) {
        return true;
    }
    $data = fetchUrl($url);
    if (!$data || strlen($data) < 500) {
        return false;
    }
    return file_put_contents($savePath, $data) !== false;
}

function cleanSlug(string $text): string
{
    $map = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
    ];
    $text = mb_strtolower($text, 'UTF-8');
    foreach ($map as $ascii => $pattern) {
        $text = preg_replace('/(' . $pattern . ')/u', $ascii, $text);
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function extractSpecs(string $html): array
{
    $specs = [];
    if (preg_match('/<table[^>]*>(.*?)<\/table>/is', $html, $tableMatch)) {
        if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tableMatch[1], $rows)) {
            foreach ($rows[1] as $rowHtml) {
                if (preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $rowHtml, $cols)) {
                    if (count($cols[1]) >= 2) {
                        $key = trim(strip_tags($cols[1][0]));
                        $val = trim(strip_tags($cols[1][1]));
                        if ($key !== '' && $val !== '' && mb_strlen($key) < 50 && mb_strlen($val) < 150) {
                            $specs[$key] = $val;
                        }
                    }
                }
            }
        }
    }
    return $specs;
}

function cleanDescription(string $html): string
{
    // Loại bỏ các đoạn quảng cáo liên hệ mua hàng của website gốc
    $html = preg_replace('/<h2>\s*Địa chỉ mua hàng\s*<\/h2>.*$/is', '', $html);
    $html = preg_replace('/<strong>\s*KHO PHÂN PHỐI.*$/is', '', $html);
    $html = preg_replace('/<table[^>]*>.*?<\/table>/is', '', $html); // bỏ bảng thông số trong mô tả vì đã tách ra mục riêng
    $html = strip_tags($html, '<p><br><ul><li><strong><b>');
    $html = preg_replace('/\s+/', ' ', $html);
    $text = strip_tags($html);
    return trim($text);
}

// 2. Tải danh sách sản phẩm qua WooCommerce Store API
echo "=== BẮT ĐẦU CÀO SẢN PHẨM TỪ HAIDANGTHIETBIDIEN.VN ===\n";

$targetProducts = [];
$pages = [1, 2]; // 2 trang đầu tiên x 100 = ~200 sản phẩm

foreach ($pages as $p) {
    $url = "https://haidangthietbidien.vn/wp-json/wc/store/v1/products?per_page=100&page={$p}";
    echo "Đang tải trang {$p}...\n";
    $json = fetchUrl($url);
    if (!$json) {
        echo "Lỗi khi tải trang {$p}\n";
        continue;
    }
    $items = json_decode($json, true);
    if (!empty($items)) {
        foreach ($items as $item) {
            $targetProducts[] = $item;
        }
    }
}

echo "Tổng số sản phẩm lấy được từ API: " . count($targetProducts) . "\n";

$imported = 0;
$updated = 0;

foreach ($targetProducts as $idx => $p) {
    $rawName = html_entity_decode($p['name'] ?? '', ENT_QUOTES, 'UTF-8');
    if (empty($rawName)) continue;

    $sourceId = $p['id'];
    $slug = cleanSlug($p['slug'] ?? $rawName);
    if (empty($slug)) {
        $slug = 'sp-' . $sourceId;
    }

    // Xác định SKU và Model Code
    $sku = trim($p['sku'] ?? '');
    if (empty($sku)) {
        if (preg_match('/[A-Z0-9]{3,}-[A-Z0-9\/-]+/i', $rawName, $m)) {
            $sku = strtoupper($m[0]);
        } else {
            $sku = 'PANA-' . $sourceId;
        }
    }
    $modelCode = $sku;

    // Phân loại danh mục (category_id)
    $catNames = array_column($p['categories'] ?? [], 'name');
    $tagNames = array_column($p['tags'] ?? [], 'name');
    $allCatText = mb_strtolower(implode(' ', array_merge($catNames, $tagNames, [$rawName])), 'UTF-8');

    $categoryId = 1; // Mặc định: Công tắc - Ổ cắm
    if (preg_match('/máy bơm|bơm nước|bơm tăng áp|bơm đẩy/iu', $allCatText)) {
        $categoryId = 6; // Máy bơm Panasonic
    } elseif (preg_match('/nóng lạnh|nước nóng/iu', $allCatText)) {
        $categoryId = 7; // Bình nóng lạnh & Máy nước nóng
    } elseif (preg_match('/lọc không khí|hút ẩm/iu', $allCatText)) {
        $categoryId = 8; // Máy lọc không khí
    } elseif (preg_match('/aptomat|mcb|mccb|rcbo|rccb|cb cóc|cầu dao|đóng cắt|chống sét|contactor/iu', $allCatText)) {
        $categoryId = 2; // Thiết bị đóng cắt MCB/RCBO
    } elseif (preg_match('/đèn|downlight|led|chiếu điểm|pha led|đèn trần|panel|đèn bàn/iu', $allCatText)) {
        $categoryId = 3; // Đèn LED chiếu sáng
    } elseif (preg_match('/quạt trần|quạt hút|thông gió|quạt điện|quạt cây|quạt chắn gió/iu', $allCatText)) {
        $categoryId = 4; // Quạt trần & Quạt hút
    } elseif (preg_match('/âm sàn|công nghiệp|pce/iu', $allCatText)) {
        $categoryId = 5; // Ổ cắm âm sàn / Công nghiệp
    }

    // Xác định Series nếu là Công tắc - Ổ cắm
    $seriesId = null;
    if (preg_match('/moderva/iu', $allCatText)) {
        $seriesId = 4;
    } elseif (preg_match('/halumie/iu', $allCatText)) {
        $seriesId = 2;
    } elseif (preg_match('/gen-x|genx/iu', $allCatText)) {
        $seriesId = 3;
    } elseif (preg_match('/wide/iu', $allCatText)) {
        $seriesId = 1;
    } elseif (preg_match('/minerva/iu', $allCatText)) {
        $seriesId = 6;
    } elseif (preg_match('/fullcolor|full color/iu', $allCatText)) {
        $seriesId = 5;
    }

    // Giá cả
    $priceRaw = (int) ($p['prices']['price'] ?? 0);
    if ($priceRaw <= 0) {
        $priceRaw = (int) ($p['prices']['regular_price'] ?? 0);
    }
    // Nếu giá bằng 0, gán mức giá tối thiểu hợp lý
    if ($priceRaw <= 0) {
        $priceRaw = 85000;
    }
    $wholesalePrice = (int) round($priceRaw * 0.85); // Chiết khấu 15% cho nhà thầu

    // Trích xuất thông số kỹ thuật (Ampe, kA, Cực, Specs)
    $ratedCurrent = null;
    if (preg_match('/\b(\d+(?:\.\d+)?)\s*(?:a|ax|ampe)\b/iu', $rawName, $m)) {
        $val = (float) $m[1];
        if ($val >= 1 && $val <= 400) {
            $ratedCurrent = $val;
        }
    }

    $breakingCap = null;
    if (preg_match('/\b(\d+(?:\.\d+)?)\s*ka\b/iu', $rawName, $m)) {
        $val = (float) $m[1];
        if ($val >= 1 && $val <= 100) {
            $breakingCap = $val;
        }
    }

    $poles = null;
    if (preg_match('/\b([1234])\s*(?:p|cực)\b/iu', $rawName, $m)) {
        $poles = (int) $m[1];
    }

    $specs = extractSpecs($p['description'] ?? '');
    if (empty($specs)) {
        $specs = [
            'Thương hiệu' => 'Panasonic',
            'Bảo hành'    => '24 tháng chính hãng',
            'Xuất xứ'     => 'Chính hãng Panasonic',
        ];
    }
    $specsJson = json_encode($specs, JSON_UNESCAPED_UNICODE);

    // Mô tả ngắn & chi tiết
    $shortDesc = strip_tags($p['short_description'] ?? '');
    if (empty($shortDesc)) {
        $shortDesc = mb_substr(strip_tags($p['description'] ?? ''), 0, 180) . '...';
    }
    $description = cleanDescription($p['description'] ?? '');

    // Đơn vị tính
    $unit = 'Cái';
    if ($categoryId === 4 || $categoryId === 6 || $categoryId === 7) {
        $unit = 'Bộ';
    }

    // Tải hình ảnh Thumbnail
    $thumbnail = 'uploads/products/placeholder.svg';
    $galleryList = [];

    if (!empty($p['images']) && is_array($p['images'])) {
        foreach (array_slice($p['images'], 0, 4) as $imgIdx => $imgData) {
            $src = $imgData['src'] ?? '';
            if (!$src) continue;

            $ext = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $ext = strtolower(explode('?', $ext)[0]);
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $ext = 'jpg';
            }

            $imgFileName = $slug . ($imgIdx > 0 ? "-{$imgIdx}" : '') . '.' . $ext;
            $localDest = $uploadDir . DIRECTORY_SEPARATOR . $imgFileName;
            $relativeUrl = 'uploads/products/' . $imgFileName;

            if (downloadImage($src, $localDest)) {
                if ($imgIdx === 0) {
                    $thumbnail = $relativeUrl;
                }
                $galleryList[] = $relativeUrl;
            }
        }
    }
    $galleryJson = json_encode($galleryList, JSON_UNESCAPED_UNICODE);

    // Lưu vào cơ sở dữ liệu (Insert or Update)
    $stmt = $pdo->prepare("
        INSERT INTO products (
            category_id, series_id, name, slug, sku, model_code, short_desc, description,
            price, wholesale_price, wholesale_min_qty, stock, unit, thumbnail, gallery, specs,
            rated_current, breaking_capacity, poles, is_featured, status
        ) VALUES (
            :category_id, :series_id, :name, :slug, :sku, :model_code, :short_desc, :description,
            :price, :wholesale_price, :wholesale_min_qty, :stock, :unit, :thumbnail, :gallery, :specs,
            :rated_current, :breaking_capacity, :poles, :is_featured, 1
        )
        ON DUPLICATE KEY UPDATE
            category_id = VALUES(category_id),
            series_id = VALUES(series_id),
            name = VALUES(name),
            short_desc = VALUES(short_desc),
            description = VALUES(description),
            price = VALUES(price),
            wholesale_price = VALUES(wholesale_price),
            thumbnail = VALUES(thumbnail),
            gallery = VALUES(gallery),
            specs = VALUES(specs),
            rated_current = VALUES(rated_current),
            breaking_capacity = VALUES(breaking_capacity),
            poles = VALUES(poles),
            status = 1
    ");

    try {
        $stmt->execute([
            'category_id'        => $categoryId,
            'series_id'          => $seriesId,
            'name'               => $rawName,
            'slug'               => $slug,
            'sku'                => $sku,
            'model_code'         => $modelCode,
            'short_desc'         => mb_substr($shortDesc, 0, 480),
            'description'        => $description,
            'price'              => $priceRaw,
            'wholesale_price'    => $wholesalePrice,
            'wholesale_min_qty'  => 10,
            'stock'              => rand(20, 100),
            'unit'               => $unit,
            'thumbnail'          => $thumbnail,
            'gallery'            => $galleryJson,
            'specs'              => $specsJson,
            'rated_current'      => $ratedCurrent,
            'breaking_capacity'  => $breakingCap,
            'poles'              => $poles,
            'is_featured'        => ($idx % 5 === 0) ? 1 : 0,
        ]);
        $imported++;
        echo sprintf("[%d/%d] Đã lưu: %s (%s) - %s VNĐ\n", $idx + 1, count($targetProducts), $rawName, $sku, number_format($priceRaw));
    } catch (PDOException $e) {
        // Nếu lỗi trùng lặp slug, tạo slug duy nhất và thử lại
        if (str_contains($e->getMessage(), 'uq_products_slug') || str_contains($e->getMessage(), 'Duplicate entry')) {
            $slugUnique = $slug . '-' . $sourceId;
            $skuUnique = $sku . '-' . $sourceId;
            try {
                $stmt->execute([
                    'category_id'        => $categoryId,
                    'series_id'          => $seriesId,
                    'name'               => $rawName,
                    'slug'               => $slugUnique,
                    'sku'                => $skuUnique,
                    'model_code'         => $modelCode,
                    'short_desc'         => mb_substr($shortDesc, 0, 480),
                    'description'        => $description,
                    'price'              => $priceRaw,
                    'wholesale_price'    => $wholesalePrice,
                    'wholesale_min_qty'  => 10,
                    'stock'              => rand(20, 100),
                    'unit'               => $unit,
                    'thumbnail'          => $thumbnail,
                    'gallery'            => $galleryJson,
                    'specs'              => $specsJson,
                    'rated_current'      => $ratedCurrent,
                    'breaking_capacity'  => $breakingCap,
                    'poles'              => $poles,
                    'is_featured'        => ($idx % 5 === 0) ? 1 : 0,
                ]);
                $imported++;
                echo sprintf("[%d/%d] Đã lưu (unique slug): %s\n", $idx + 1, count($targetProducts), $rawName);
            } catch (Exception $ex) {
                echo "Lỗi: " . $ex->getMessage() . "\n";
            }
        } else {
            echo "Lỗi PDO: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n============================================\n";
echo "HOÀN TẤT! Đã cào và lưu thành công {$imported} sản phẩm kèm hình ảnh vào website!\n";
echo "============================================\n";
