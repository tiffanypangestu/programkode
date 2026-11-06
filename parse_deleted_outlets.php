<?php
$lines = file("c:\\Users\\tiffany\\programkode\\binlog37_decoded.sql");
$inOutletDelete = false;
$outletRows = [];
$currentRow = [];

foreach ($lines as $line) {
    if (strpos($line, 'DELETE FROM `programkode`.`outlet`') !== false) {
        $inOutletDelete = true;
        continue;
    }
    if ($inOutletDelete) {
        if (strpos($line, '### WHERE') !== false) {
            if (!empty($currentRow)) {
                $outletRows[] = $currentRow;
                $currentRow = [];
            }
        } elseif (preg_match('/###   @(\d+)=(.*)/', $line, $m)) {
            $colIdx = (int)$m[1];
            $val = trim($m[2]);
            $currentRow[$colIdx] = $val;
        } elseif (strpos($line, '### DELETE FROM') !== false || strpos($line, '# at ') !== false || strpos($line, 'COMMIT') !== false) {
            if (!empty($currentRow)) {
                $outletRows[] = $currentRow;
                $currentRow = [];
            }
            if (strpos($line, 'DELETE FROM `programkode`.`outlet`') === false) {
                $inOutletDelete = false;
            }
        }
    }
}
if (!empty($currentRow)) {
    $outletRows[] = $currentRow;
}

echo "Found " . count($outletRows) . " deleted outlet rows:\n";
foreach ($outletRows as $row) {
    echo json_encode($row) . "\n";
}
