<?php
header("Content-Type: application/json");

// PATH PYTHON
$python = realpath(__DIR__ . "/../../../.venv/Scripts/python.exe");
$script = realpath(__DIR__ . "/auto_worker_userbot.py");

// Cek file
if (!file_exists($python)) {
    echo json_encode(["stop" => true, "log" => "ERROR: Python tidak ditemukan!"]);
    exit;
}
if (!file_exists($script)) {
    echo json_encode(["stop" => true, "log" => "ERROR: Script Python tidak ditemukan!"]);
    exit;
}

// Jalankan Python dan ambil output JSON
$cmd = "\"$python\" \"$script\" 2>&1";
$output = shell_exec($cmd);

// Pastikan output valid JSON
$json = json_decode($output, true);
if ($json === null) {
    // Jika JSON gagal decode, tampilkan sebagai error
    $log = "Output Python tidak valid JSON!\n\nRAW OUTPUT:\n$output";
    echo json_encode(["stop" => true, "log" => $log]);
    exit;
}

// Kirim balik JSON ke JS
echo json_encode($json);
