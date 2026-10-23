<?php
$projectsToFetch = [
    'waterpoint-nam-long.jpg' => [
        'https://waterpointnamlong.com.vn/wp-content/uploads/2021/04/phoi-canh-tong-the-waterpoint-nam-long.jpg',
        'https://waterpoint.com.vn/wp-content/uploads/2020/06/waterpoint-ben-luc-long-an-nam-long.jpg',
        'https://namlongvn.com/wp-content/uploads/2021/03/waterpoint-ben-luc.jpg'
    ],
    'tt-city-millennia.jpg' => [
        'https://ttcitymillennia.vn/wp-content/uploads/2022/04/tt-city-millennia-can-giuoc.jpg',
        'https://diaocnamtrungbo.vn/wp-content/uploads/2022/03/tt-city-millennia-long-an.jpg',
        'https://tt-millennia.com/wp-content/uploads/2022/05/phoi-canh-tt-city-millennia.jpg'
    ],
    'van-phuc-city.jpg' => [
        'https://vanphuccity.com.vn/wp-content/uploads/2021/05/khu-do-thi-van-phuc-city-thu-duc.jpg',
        'https://vanphuccity.net/wp-content/uploads/2021/08/khu-do-thi-van-phuc.jpg'
    ],
    'mizuki-park.jpg' => [
        'https://mizukiparknamlong.vn/wp-content/uploads/2021/05/mizuki-park-binh-chanh.jpg',
        'https://namlongvn.com/wp-content/uploads/2021/04/mizuki-park-nam-long.jpg'
    ],
    'kcn-long-hau.jpg' => [
        'https://longhau.com.vn/upload/images/kcn-long-hau-can-giuoc-long-an.jpg',
        'https://kizuna.vn/upload/images/kizuna-serviced-factory-long-an.jpg'
    ]
];

foreach ($projectsToFetch as $fileName => $urls) {
    $dest = 'public/uploads/projects/' . $fileName;
    $success = false;
    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && strlen($data) > 10000 && (str_starts_with($data, "\xFF\xD8") || str_starts_with($data, "\x89PNG") || str_starts_with($data, "RIFF"))) {
            file_put_contents($dest, $data);
            echo "Downloaded $fileName (" . strlen($data) . " bytes)\n";
            $success = true;
            break;
        }
    }
    if (!$success) {
        echo "Could not download $fileName from primaries, using fallback copy.\n";
        // Fallback: copy an existing high quality building photo
        if (file_exists('public/uploads/projects/vinhomes-ocean-park.jpg')) {
            copy('public/uploads/projects/vinhomes-ocean-park.jpg', $dest);
            echo "Created fallback for $fileName\n";
        }
    }
}
