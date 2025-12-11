<?php
include "../../../keamanan/koneksi.php";

// Ambil status saat ini
$cek = mysqli_query($koneksi, "SELECT auto_kirim FROM pengaturan LIMIT 1");
$data = mysqli_fetch_assoc($cek);
$status = $data['auto_kirim'];

if ($status == "off") {
    mysqli_query($koneksi, "UPDATE pengaturan SET auto_kirim='on'");
    echo "Auto Kirim Pesan DIHIDUPKAN!";
} else {
    mysqli_query($koneksi, "UPDATE pengaturan SET auto_kirim='off'");
    echo "Auto Kirim Pesan DIMATIKAN!";
}