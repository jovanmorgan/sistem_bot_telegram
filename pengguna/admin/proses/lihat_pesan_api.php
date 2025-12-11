<?php

$TOKEN = "8517698734:AAEZtAILJ3Y3ZC8DC0q3FFg8voRo9FHOX4g";
$api_url = "https://api.telegram.org/bot$TOKEN/getUpdates";

$id     = $_GET["id"];
$tipe   = $_GET["tipe"];
$limit  = $_GET["limit"] ?? 30;  // ambil 30 per batch
$offset = $_GET["offset"] ?? 0; // untuk pagination

$data = json_decode(file_get_contents($api_url), true);
$result = array_reverse($data["result"]); // buat paling baru di bawah

$out = [];
$counter = 0;

foreach ($result as $row) {

    $msg = $row["message"] ?? $row["pesan"] ?? null;
    if (!$msg) continue;

    $chat = $msg["chat"];
    if ($chat["id"] != $id) continue;

    // hanya ambil teks
    $nama = trim(($msg["from"]["first_name"] ?? "") . " " . ($msg["from"]["last_name"] ?? ""));
    $teks = $msg["text"] ?? "(Non text message)";
    $time = date("Y-m-d H:i:s", $msg["date"]);

    // skip sampai mencapai offset
    if ($counter < $offset) {
        $counter++;
        continue;
    }

    // batas limit
    if (count($out) >= $limit) break;

    $out[] = [
        "nama"  => $nama,
        "text"  => nl2br(htmlspecialchars($teks)),
        "waktu" => $time
    ];

    $counter++;
}

echo json_encode($out);
