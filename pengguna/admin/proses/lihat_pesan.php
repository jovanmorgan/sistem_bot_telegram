<?php
include '../../../keamanan/koneksi.php';

$id     = $_GET["id"];
$tipe   = $_GET["tipe"];

$search = isset($_GET['search']) ? $_GET['search'] : "";
$from   = isset($_GET['from']) ? $_GET['from'] : "";
$to     = isset($_GET['to']) ? $_GET['to'] : "";
$user   = isset($_GET['user']) ? $_GET['user'] : "";


// ========================
//  BASE QUERY
// ========================
if ($tipe == "group") {
    $sql = "SELECT * FROM pesan_masuk WHERE chat_id='$id'";
} else {
    $sql = "SELECT * FROM pesan_masuk WHERE sender_id='$id'";
}


// ========================
//  TAMBAHKAN FILTER
// ========================

// FILTER SEARCH
if ($search !== "") {
    $search = mysqli_real_escape_string($koneksi, $search);
    $sql .= " AND pesan LIKE '%$search%'";
}

// FILTER USER
if ($user !== "") {
    $user = mysqli_real_escape_string($koneksi, $user);
    $sql .= " AND sender_name='$user'";
}

// FILTER TANGGAL
if ($from !== "") {
    $sql .= " AND DATE(waktu) >= '$from'";
}
if ($to !== "") {
    $sql .= " AND DATE(waktu) <= '$to'";
}

$sql .= " ORDER BY waktu ASC";

$q = mysqli_query($koneksi, $sql);


// ========================
//  OUTPUT CHAT
// ========================
while ($d = mysqli_fetch_assoc($q)) {

    $pesan = htmlspecialchars($d['pesan'], ENT_QUOTES, 'UTF-8');
    $pesan = nl2br($pesan);
    $pesan = str_replace("  ", "&nbsp;&nbsp;", $pesan);

    echo "
    <div class='chat-bubble'>
        <div class='chat-name'>{$d['sender_name']}</div>
        <div class='chat-text'>$pesan</div>
        <div class='chat-time'>{$d['waktu']}</div>
    </div>
    ";
}