<?php
include "../../../keamanan/koneksi.php";
mysqli_query($koneksi, "UPDATE pengaturan_auto SET auto_kirim='off'");
echo "STOPPED";
