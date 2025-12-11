<?php
include '../../../keamanan/koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(["status" => "error", "msg" => "ID tidak valid"]);
    exit;
}

// ===========================
// 1. HAPUS PESAN
// ===========================
$sql = "DELETE FROM pesan_broadcast WHERE id = $id";
$hapus = mysqli_query($koneksi, $sql);

// ===========================
// 2. UPDATE pengaturan_auto.pesan_list
// ===========================
$q = mysqli_query($koneksi, "SELECT id, pesan_list FROM pengaturan_auto");
while ($row = mysqli_fetch_assoc($q)) {

    $list = json_decode($row['pesan_list'], true);
    if (!is_array($list)) continue;

    // Hapus ID dari array JSON
    $updated = array_values(array_filter($list, fn($v) => intval($v) !== $id));

    $jsonBaru = json_encode($updated, JSON_UNESCAPED_UNICODE);

    mysqli_query($koneksi, "
        UPDATE pengaturan_auto
        SET pesan_list = '$jsonBaru'
        WHERE id = {$row['id']}
    ");
}


if ($hapus) {
    echo json_encode(["status" => "success", "msg" => "Pesan berhasil dihapus"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Gagal menghapus pesan"]);
}
