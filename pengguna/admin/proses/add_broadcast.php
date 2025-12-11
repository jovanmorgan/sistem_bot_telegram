<?php
include '../../../keamanan/koneksi.php';
$pesan = $_POST['pesan'] ?? "";

if (trim($pesan) == "") {
    echo json_encode(["status" => "error", "msg" => "Pesan tidak boleh kosong"]);
    exit;
}

$now = date("Y-m-d H:i:s");

$sql = "INSERT INTO pesan_broadcast (pesan, waktu_kirim, status)
        VALUES ('$pesan', '$now', 'pending')";

if (mysqli_query($koneksi, $sql)) {
    echo json_encode(["status" => "success", "msg" => "Pesan berhasil ditambahkan"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Gagal menambah pesan"]);
}
