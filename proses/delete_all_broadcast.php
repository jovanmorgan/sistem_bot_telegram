<?php
include '../keamanan/koneksi.php';

if (!isset($_POST['ids'])) {
    echo "ERROR: No IDs received";
    exit;
}

$ids = json_decode($_POST['ids'], true);

if (!is_array($ids) || count($ids) === 0) {
    echo "ERROR: Invalid ID list";
    exit;
}

// Sanitasi ID
$clean_ids = array_map('intval', $ids);
$id_list = implode(",", $clean_ids);

// ===========================
// 1. HAPUS DATA PESAN
// ===========================
$sql = "DELETE FROM pesan_broadcast WHERE id IN ($id_list)";
$hapusPesan = mysqli_query($koneksi, $sql);

// ===========================
// 2. UPDATE pengaturan_auto.pesan_list
// ===========================
$q = mysqli_query($koneksi, "SELECT id, pesan_list FROM pengaturan_auto");
while ($row = mysqli_fetch_assoc($q)) {

    $list = json_decode($row['pesan_list'], true);
    if (!is_array($list)) continue;

    // Buang ID yang dihapus dari array
    $updated = array_values(array_diff($list, $clean_ids));

    // Simpan kembali JSON
    $jsonBaru = json_encode($updated, JSON_UNESCAPED_UNICODE);

    mysqli_query($koneksi, "
        UPDATE pengaturan_auto 
        SET pesan_list = '$jsonBaru' 
        WHERE id = {$row['id']}
    ");
}

echo $hapusPesan ? "SUCCESS" : "ERROR: " . mysqli_error($koneksi);
