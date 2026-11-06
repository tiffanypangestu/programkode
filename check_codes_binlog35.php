<?php
$lines = file("c:\\Users\\tiffany\\programkode\\binlog35_decoded.sql");
$found = [];
foreach ($lines as $line) {
    if (preg_match('/###   @3=\'(100\d\d|200\d\d|300\d\d)\'/', $line, $m)) {
        $found[$m[1]] = true;
    }
}
ksort($found);
print_r(array_keys($found));
