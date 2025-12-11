<?php
include "../../../keamanan/koneksi.php";

$interval = $_POST['interval'];

$now = date("Y-m-d H:i:s");

mysqli_query($conn, "UPDATE pengaturan_auto SET last_run='$now' WHERE id=1");

echo "OK";
