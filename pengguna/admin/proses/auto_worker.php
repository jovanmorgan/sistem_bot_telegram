<?php
include "../../../keamanan/koneksi.php";

$token = "8517698734:AAEZtAILJ3Y3ZC8DC0q3FFg8voRo9FHOX4g";
$url   = "https://api.telegram.org/bot$token/sendMessage";

while (true) {

    $q = mysqli_query($koneksi, "SELECT * FROM pengaturan_auto LIMIT 1");
    $d = mysqli_fetch_assoc($q);

    if (!$d || $d['auto_kirim'] == "off") {
        exit; // stop loop
    }

    $group_id = $d['group_id'];
    $pesan_array = json_decode($d['pesan_list'], true);

    foreach ($pesan_array as $idPesan) {

        $idPesan = intval($idPesan);

        $pQ = mysqli_query($koneksi, "SELECT pesan FROM pesan_broadcast WHERE id=$idPesan");
        if (mysqli_num_rows($pQ) == 0) continue;

        $pD = mysqli_fetch_assoc($pQ);
        $pesan = htmlspecialchars($pD['pesan'], ENT_QUOTES);

        $post = [
            "chat_id"    => $group_id,
            "text"       => $pesan,
            "parse_mode" => "HTML"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        usleep(350000);
    }

    // tunggu 1 menit
    sleep(60);
}
