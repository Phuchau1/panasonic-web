<?php
$projects = [
    'vinhomes-ocean-park.jpg' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80',
    'ecopark-grand.jpg' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
    'vinhomes-smart-city.jpg' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
    'masterise-lumiere.jpg' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80',
    'landmark-81.jpg' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&q=80',
    'muong-thanh-luxury.jpg' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80'
];

$dir = __DIR__ . '/../public/uploads/projects';
if (!is_dir($dir)) mkdir($dir, 0777, true);

foreach ($projects as $file => $url) {
    $dest = "$dir/$file";
    if (!file_exists($dest)) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $img = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code === 200 && strlen($img) > 1000) {
            file_put_contents($dest, $img);
            echo "Downloaded $file (" . strlen($img) . " bytes)\n";
        } else {
            echo "Failed $file (HTTP $code)\n";
        }
    } else {
        echo "Exists $file\n";
    }
}
