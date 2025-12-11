<?php
include '../../../keamanan/koneksi.php';

$q = mysqli_query($koneksi, "SELECT * FROM pesan_broadcast ORDER BY id DESC");
$data = [];

while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
}

echo json_encode($data);