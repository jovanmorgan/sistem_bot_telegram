<?php
include "../../../keamanan/koneksi.php";

if (!isset($_POST['id']) || !isset($_POST['mode'])) {
    echo "INVALID_REQUEST";
    exit;
}

$id = intval($_POST['id']);
$mode = $_POST['mode'];

// Ambil data pengaturan_auto
$q = mysqli_query($koneksi, "SELECT id, pesan_list FROM pengaturan_auto LIMIT 1");

if (mysqli_num_rows($q) === 0) {
    // Jika belum ada record → buat baru
    mysqli_query($koneksi, "
        INSERT INTO pengaturan_auto(group_id, pesan_list, auto_kirim)
        VALUES('[]', '[]', 'off')
    ");
    $pesan_list = [];
    $row_id = mysqli_insert_id($koneksi);
} else {
    $row = mysqli_fetch_assoc($q);
    $row_id = $row['id'];
    $pesan_list = json_decode($row['pesan_list'], true);
    if (!is_array($pesan_list)) $pesan_list = [];
}

if ($mode === "add") {
    if (!in_array($id, $pesan_list)) {
        $pesan_list[] = $id;
    }
}

if ($mode === "remove") {
    $pesan_list = array_values(array_filter($pesan_list, fn($x) => intval($x) !== $id));
}

$pesan_json = json_encode($pesan_list, JSON_UNESCAPED_UNICODE);

mysqli_query($koneksi, "
    UPDATE pengaturan_auto
    SET pesan_list = '$pesan_json'
    WHERE id = $row_id
");

echo "SUCCESS";
