<?php
$lines = file('c:\\xampp\\apache\\logs\\access.log');
$last30 = array_slice($lines, -30);
foreach ($last30 as $l) {
    echo $l;
}
