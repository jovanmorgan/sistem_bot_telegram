<?php
include '../../../keamanan/koneksi.php';

$token = "8517698734:AAEZtAILJ3Y3ZC8DC0q3FFg8voRo9FHOX4g";
$url = "https://api.telegram.org/bot$token/getUpdates";

$data = json_decode(file_get_contents($url), true);

if (!isset($data["result"])) {
    echo "Tidak ada update.";
    exit;
}

foreach ($data["result"] as $update) {

    if (!isset($update["message"])) continue;

    $msg = $update["message"];
    $chat = $msg["chat"];
    $from = $msg["from"];

    $chat_id = $chat["id"];
    $chat_type = $chat["type"];
    $sender_id = $from["id"];
    $sender_name = trim(($from["first_name"] ?? "") . " " . ($from["last_name"] ?? ""));
    $pesan = $msg["text"] ?? "";
    $waktu = date("Y-m-d H:i:s", $msg["date"]);

    // =========================================
    // CEGAH DUPLIKAT DATA
    // =========================================
    $cek = mysqli_query(
        $koneksi,
        "SELECT id FROM pesan_masuk 
         WHERE chat_id='$chat_id' 
         AND sender_id='$sender_id'
         AND pesan='" . mysqli_real_escape_string($koneksi, $pesan) . "'
         AND waktu='$waktu'"
    );

    if (mysqli_num_rows($cek) == 0) {

        mysqli_query(
            $koneksi,
            "INSERT INTO pesan_masuk (chat_id, chat_type, sender_id, sender_name, pesan, waktu)
            VALUES (
                '$chat_id', 
                '$chat_type', 
                '$sender_id', 
                '$sender_name', 
                '" . mysqli_real_escape_string($koneksi, $pesan) . "', 
                '$waktu'
            )"
        );
    }

    // =========================================
    // SIMPAN GRUP (jika belum ada)
    // =========================================
    if ($chat_type == "group") {
        $grup_name = $chat["title"] ?? "Tanpa Nama";
        mysqli_query(
            $koneksi,
            "INSERT IGNORE INTO daftar_grup (grup_id, nama)
            VALUES ('$chat_id', '" . mysqli_real_escape_string($koneksi, $grup_name) . "')"
        );
    }

    // =========================================
    // SIMPAN USER PRIVAT (jika belum ada)
    // =========================================
    if ($chat_type == "private") {
        mysqli_query(
            $koneksi,
            "INSERT IGNORE INTO daftar_user (user_id, nama)
            VALUES ('$sender_id', '" . mysqli_real_escape_string($koneksi, $sender_name) . "')"
        );
    }
}

echo "Update berhasil disimpan!";
