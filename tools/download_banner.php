<?php
$b1 = file_get_contents('https://dienpanasonic.com.vn/wp-content/uploads/2022/12/banner.jpg');
echo "banner.jpg size: " . strlen($b1) . "\n";
if ($b1) {
    file_put_contents(__DIR__ . '/../public/uploads/banner-1.jpg', $b1);
    echo "Saved banner-1.jpg\n";
}
