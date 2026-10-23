<?php
require __DIR__ . '/probe_api.php';
// Let's test MCB searches
echo "MCB 1P: " . testSearch("MCB 1P") . "\n";
echo "MCB: " . testSearch("MCB") . "\n";
echo "BBD: " . testSearch("BBD") . "\n";
echo "BBDE: " . testSearch("BBDE") . "\n";
echo "Aptomat: " . testSearch("Aptomat") . "\n";
echo "F-60: " . testSearch("F-60") . "\n";
echo "FV-24: " . testSearch("FV-24") . "\n";
echo "Downlight: " . testSearch("Downlight 9W") . "\n";
