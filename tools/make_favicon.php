<?php
// Simple 16x16 / 32x32 blue Panasonic icon
// A standard minimal valid ICO binary
$icoHeader = pack('vvv', 0, 1, 1); // reserved, type 1 (icon), 1 image
$width = 16;
$height = 16;
$colorCount = 0;
$reserved = 0;
$planes = 1;
$bitCount = 32;
$imageSize = 40 + ($width * $height * 4); // BMP info header + RGBA data
$imageOffset = 22; // 6 (header) + 16 (directory entry)

$icoDir = pack('CCCCvvVV', $width, $height, $colorCount, $reserved, $planes, $bitCount, $imageSize, $imageOffset);

// BMP Info Header
$bmpHeader = pack('VVVvvVVVVVV', 40, $width, $height * 2, $planes, $bitCount, 0, $width * $height * 4, 0, 0, 0, 0);

// Pixels (16x16, BGRA)
$pixelData = '';
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        // Panasonic Navy: R: 0, G: 64, B: 152 (#004098)
        $pixelData .= chr(152) . chr(64) . chr(0) . chr(255);
    }
}

$icoContent = $icoHeader . $icoDir . $bmpHeader . $pixelData;

file_put_contents('public/favicon.ico', $icoContent);
file_put_contents('c:\\xampp\\htdocs\\favicon.ico', $icoContent);
echo "favicon.ico created successfully in public/ and htdocs/ root!\n";
