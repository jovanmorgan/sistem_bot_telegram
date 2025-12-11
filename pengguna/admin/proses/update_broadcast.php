<?php
include '../../../keamanan/koneksi.php';

$id = intval($_POST['id']);
$pesan = mysqli_real_escape_string($koneksi, $_POST['pesan']);

$sql = "UPDATE pesan_broadcast SET pesan='$pesan' WHERE id=$id";

echo mysqli_query($koneksi, $sql) ? "SUCCESS" : "ERROR";
