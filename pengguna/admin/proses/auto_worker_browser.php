<?php
// =====================
// FIX TIMEZONE DENPASAR BALI (WITA)
// =====================
date_default_timezone_set("Asia/Makassar");

include "../../../keamanan/koneksi.php";
header("Content-Type: application/json");


// CEK STATUS AUTO KIRIM
$cek = mysqli_query($koneksi, "SELECT auto_kirim, group_id, pesan_list FROM pengaturan_auto LIMIT 1");
$data = mysqli_fetch_assoc($cek);

// Jika auto kirim OFF → hentikan worker
if (!$data || $data['auto_kirim'] !== "on") {
    echo json_encode([
        "stop" => true,
        "log"  => "AUTO KIRIM OFF\n"
    ]);
    exit;
}

// Ambil target group/user
$targets = json_decode($data['group_id'], true);

// Ambil daftar pesan
$pesan_list = json_decode($data['pesan_list'], true);

// Validasi
if (!is_array($targets) || count($targets) == 0) {
    echo json_encode([
        "stop" => true,
        "log" => "TIDAK ADA TARGET TUJUAN!\n"
    ]);
    exit;
}

if (!is_array($pesan_list) || count($pesan_list) == 0) {
    echo json_encode([
        "stop" => true,
        "log" => "TIDAK ADA PESAN YANG TERDAFTAR!\n"
    ]);
    exit;
}

// TOKEN TELEGRAM
$token = "8517698734:AAEZtAILJ3Y3ZC8DC0q3FFg8voRo9FHOX4g";

// =============================
// KIRIM SEMUA PESAN DALAM pesan_list
// =============================
foreach ($pesan_list as $pesan_id) {

    // Ambil isi pesan
    $q = mysqli_query($koneksi, "SELECT pesan FROM pesan_broadcast WHERE id='$pesan_id'");
    $d = mysqli_fetch_assoc($q);

    if (!$d) continue; // Jika id tidak valid, skip

    $pesan = $d['pesan'];

    // Kirim ke semua target
    foreach ($targets as $chat_id) {

        $url = "https://api.telegram.org/bot$token/sendMessage";
        $post = [
            "chat_id" => $chat_id,
            "text"    => $pesan
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

// =============================
// OUTPUT LOG DENGAN WAKTU WIB
// =============================
echo json_encode([
    "stop" => false,
    "log"  => "Berhasil mengirim " . count($pesan_list) . " pesan ke " . count($targets) . " tujuan pada " . date("H:i:s") . "\n"
]);
