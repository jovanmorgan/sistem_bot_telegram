<?php
include "../../../keamanan/koneksi.php";

$token   = "8517698734:AAEZtAILJ3Y3ZC8DC0q3FFg8voRo9FHOX4g";
$chat_id = "-4990657414";

echo "Auto kirim berjalan...\n\n";

while (true) {

    $detik = date("s");

    // Tunggu sampai detik "00"
    if ($detik == "00") {

        // =============================
        // CEK PENGATURAN AUTO KIRIM
        // =============================
        $cek = mysqli_query($koneksi, "SELECT auto_kirim FROM pengaturan LIMIT 1");
        $data = mysqli_fetch_assoc($cek);

        if ($data && $data['auto_kirim'] == "on") {

            // =============================
            // AMBIL PESAN TERBARU
            // =============================
            $query = mysqli_query($koneksi, "SELECT * FROM pesan_broadcast ORDER BY id DESC LIMIT 1");

            if (mysqli_num_rows($query) > 0) {
                $dataPesan = mysqli_fetch_assoc($query);
                $pesan = $dataPesan['pesan'];

                // =============================
                // KIRIM TELEGRAM
                // =============================
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
                $response = curl_exec($ch);
                curl_close($ch);

                echo "Pesan dikirim: " . date("H:i:s") . "\n";
            }
        } else {
            echo "Auto kirim OFF (" . date("H:i:s") . ")\n";
        }

        // Hindari pengiriman dobel dalam menit yang sama
        sleep(1);
    }

    // Cek waktu tiap 1 detik
    sleep(1);
}
