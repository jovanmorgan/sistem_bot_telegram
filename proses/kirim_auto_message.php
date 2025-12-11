<?php
include "../keamanan/koneksi.php";

if (!isset($_POST['target']) || !isset($_POST['pesan'])) {
    echo "INVALID_REQUEST";
    exit;
}

$targets = json_decode($_POST['target'], true);
$pesan_list = json_decode($_POST['pesan'], true);

$targets_json = json_encode($targets);
$pesan_json   = json_encode($pesan_list);

// RESET DATA LAMA
mysqli_query($koneksi, "DELETE FROM pengaturan_auto");

// SIMPAN DATA BARU
mysqli_query($koneksi, "
    INSERT INTO pengaturan_auto(group_id, pesan_list, auto_kirim)
    VALUES('$targets_json', '$pesan_json', 'on')
");

echo "SUCCESS";
