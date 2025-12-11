<?php
include '../../../keamanan/koneksi.php';

$id = intval($_GET['id']);

$q = mysqli_query($koneksi, "SELECT * FROM pesan_broadcast WHERE id = $id");

$row = mysqli_fetch_assoc($q);

echo json_encode($row);
