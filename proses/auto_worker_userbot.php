<?php
header("Content-Type: application/json");

// PATH PYTHON
$python = realpath(__DIR__ . "/../.venv/Scripts/python.exe");
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

// Jalankan Python
$cmd = "\"$python\" \"$script\" 2>&1";
$output = shell_exec($cmd);

// CLEAN OUTPUT (hapus BOM / spasi)
$output = trim($output);

// Coba decode JSON
$json = json_decode($output, true);

if ($json === null) {
    echo json_encode([
        "stop" => true,
        "log" => "Output Python tidak valid JSON!\n\nRAW:\n" . $output
    ]);
    exit;
}

echo json_encode($json);
