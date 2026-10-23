<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Xử lý upload file an toàn: kiểm tra lỗi, dung lượng, phần mở rộng, MIME thực tế (finfo),
 * đặt tên ngẫu nhiên để tránh ghi đè & thực thi mã.
 */
final class Uploader
{
    private const MIME_MAP = [
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
        'xls'  => ['application/vnd.ms-excel', 'application/octet-stream', 'application/CDFV2', 'application/x-ole-storage'],
        'csv'  => ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'],
        'pdf'  => ['application/pdf'],
        'dwg'  => ['image/vnd.dwg', 'application/acad', 'application/octet-stream', 'image/x-dwg'],
        'zip'  => ['application/zip', 'application/x-zip-compressed'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
        'svg'  => ['image/svg+xml', 'text/xml', 'text/plain'],
    ];

    /**
     * @return array{path:string, original:string, size:int}
     * @throws \RuntimeException khi file không hợp lệ
     */
    public static function store(array $file, string $targetDir, array $allowedExt, int $maxSize, string $prefix = ''): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \RuntimeException('Dữ liệu file không hợp lệ.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('File vượt quá dung lượng cho phép.');
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('Chưa chọn file.');
            default:
                throw new \RuntimeException('Tải file thất bại, vui lòng thử lại.');
        }

        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('File vượt quá ' . round($maxSize / 1048576) . 'MB.');
        }

        $original = basename((string) $file['name']);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            throw new \RuntimeException('Định dạng không hỗ trợ. Chấp nhận: ' . strtoupper(implode(', ', $allowedExt)));
        }

        // Kiểm tra MIME thực tế (cần bật extension=fileinfo trong php.ini)
        if (class_exists(\finfo::class)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = (string) $finfo->file($file['tmp_name']);
            $allowedMimes = self::MIME_MAP[$ext] ?? [];
            if ($allowedMimes && !in_array($mime, $allowedMimes, true)) {
                throw new \RuntimeException('Nội dung file không khớp định dạng .' . $ext);
            }
        }

        // Ảnh SVG có thể chứa script -> từ chối nếu phát hiện
        if ($ext === 'svg') {
            $content = (string) file_get_contents($file['tmp_name']);
            if (preg_match('/<script|on\w+\s*=|javascript:/i', $content)) {
                throw new \RuntimeException('File SVG chứa mã không an toàn.');
            }
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Không thể tạo thư mục lưu trữ.');
        }

        $name = $prefix . date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('Không thể lưu file.');
        }
        @chmod($dest, 0644);

        return ['path' => $name, 'original' => mb_substr($original, 0, 200), 'size' => (int) $file['size']];
    }
}
