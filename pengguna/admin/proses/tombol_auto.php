<?php
include "../../../keamanan/koneksi.php";

// Ambil status awal
$cek = mysqli_query($koneksi, "SELECT auto_kirim FROM pengaturan LIMIT 1");
$data = mysqli_fetch_assoc($cek);
$status = $data['auto_kirim'];
?>

<button onclick="ubahStatus()" class="btn btn-primary">
    Auto Kirim: <?= strtoupper($status) ?>
</button>

<script>
    function ubahStatus() {
        fetch("auto_status.php")
            .then(r => r.text())
            .then(res => {
                alert(res);
                location.reload(); // Reload untuk update tampilan tombol
            });
    }
</script>