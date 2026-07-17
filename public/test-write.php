<?php
$path = '\\\\10.10.0.130\\spspro\\Storage\\contacts\\test.txt';
$result = file_put_contents($path, 'Test write at ' . date('Y-m-d H:i:s'));

if ($result === false) {
    echo "FAILED — PHP could not write to the network share.";
} else {
    echo "SUCCESS — wrote {$result} bytes to {$path}";
}