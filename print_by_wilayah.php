<?php
$outlets = json_decode(file_get_contents("c:\\Users\\tiffany\\programkode\\original_outlets.json"), true);

echo "Total outlets: " . count($outlets) . "\n\n";

$byWilayah = [];
foreach ($outlets as $o) {
    // schema: outlet_id (1), wilayah_id (2), kode_outlet (3), nama_outlet (4), alamat_outlet (5), tipe_outlet (6), status_outlet (7), created_at (8), updated_at (9)
    $wId = $o[2];
    $kode = trim($o[3], "'");
    $nama = trim($o[4], "'");
    $alamat = trim($o[5], "'");
    $tipe = trim($o[6] ?? '', "'");
    $status = trim($o[7] ?? '', "'");
    $byWilayah[$wId][] = compact('kode', 'nama', 'alamat', 'tipe', 'status');
}

foreach ($byWilayah as $wId => $list) {
    echo "=== Wilayah ID $wId (" . count($list) . " outlet) ===\n";
    foreach ($list as $item) {
        echo "  [{$item['kode']}] {$item['nama']} ({$item['tipe']}) - {$item['alamat']}\n";
    }
    echo "\n";
}
