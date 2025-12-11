<?php
$TOKEN = "8517698734:AAEZtAILJ3Y3ZC8DC0q3FFg8voRo9FHOX4g";
$api_url = "https://api.telegram.org/bot$TOKEN/getUpdates";

$data = json_decode(file_get_contents($api_url), true);

// Cek apakah API OK
if (!isset($data["result"])) {
    die("Gagal mengambil data dari Telegram API");
}

$result = $data["result"];

// ========================
// KUMPULKAN GRUP, CHANNEL, USER
// ========================
$grup = [];
$channel = [];
$user = [];

foreach ($result as $row) {

    // ------- Pesan dari chat biasa (message) -------
    if (isset($row["message"])) {
        $msg = $row["message"];
        $chat = $msg["chat"];
        $type = $chat["type"];

        if ($type === "group" || $type === "supergroup") {
            $grup[$chat["id"]] = $chat["title"];
        } elseif ($type === "private") {
            $nama = trim(($chat["first_name"] ?? "") . " " . ($chat["last_name"] ?? ""));
            $user[$chat["id"]] = $nama ?: "Tanpa Nama";
        }
    }

    // ------- Pesan dari CHANNEL (channel_post) -------
    if (isset($row["channel_post"])) {
        $msg = $row["channel_post"];
        $chat = $msg["chat"];

        if ($chat["type"] === "channel") {
            $channel[$chat["id"]] = $chat["title"] ?? "(Tanpa Nama)";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data API Telegram</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fc;
            font-family: 'Segoe UI', sans-serif;
        }

        /* HEADER */
        .header-box {
            background: linear-gradient(135deg, #4e73df, #6f42c1);
            padding: 28px;
            border-radius: 16px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(72, 97, 235, 0.25);
        }

        .header-box h2 {
            margin: 0;
            font-weight: 600;
        }

        /* CARD GRUP */
        .tg-card {
            border-radius: 14px;
            padding: 16px;
            background: white;
            margin-bottom: 14px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
            cursor: pointer;
            transition: 0.25s;
        }

        .tg-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .tg-title {
            font-size: 17px;
            font-weight: bold;
            color: #333;
        }

        .tg-sub {
            font-size: 13px;
            color: #777;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        /* BUTTON SUPER KEREN */
        .btn-main {
            margin-top: 30px;
            padding: 14px 34px;
            font-size: 17px;
            border-radius: 30px;
            font-weight: bold;
            background: linear-gradient(135deg, #4e73df, #6f42c1);
            border: none;
            color: white;
            box-shadow: 0 6px 18px rgba(78, 115, 223, 0.35);
            transition: 0.3s;
        }

        .btn-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(78, 115, 223, 0.45);
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .tg-card {
                padding: 14px;
            }

            .section-title {
                font-size: 18px;
            }
        }

        .chat-bubble {
            background: #ffe3e3;
            padding: 14px;
            margin-bottom: 15px;
            border-radius: 14px;
            width: 100%;
        }

        .chat-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .chat-time {
            font-size: 12px;
            color: #777;
            text-align: right;
        }

        /* BUBBLE STYLE TELEGRAM */
        .bubble {
            max-width: 75%;
            padding: 10px 14px;
            margin-bottom: 10px;
            border-radius: 12px;
            font-size: 15px;
            line-height: 1.4;
            position: relative;
            white-space: pre-line;
        }

        .bubble.me {
            background: #d1f0ff;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .bubble.other {
            background: #f1f1f1;
            margin-right: auto;
            border-bottom-left-radius: 0;
        }

        .bubble-time {
            font-size: 11px;
            color: gray;
            margin-top: 4px;
            text-align: right;
        }

        /* AREA INPUT TELEGRAM */
        .chat-input-container {
            display: flex;
            gap: 10px;
            padding: 12px;
            border-top: 1px solid #ddd;
            background: #fff;
            position: sticky;
            bottom: 0;
        }

        .chat-textarea {
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 10px 15px;
            resize: none;
            height: 48px;
            outline: none;
            transition: 0.2s;
        }

        .chat-textarea:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px #cce1ff;
        }

        .btn-send {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            background: #0d6efd;
            color: white;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* SCROLL AREA */
        #listPesanBroadcast {
            overflow-y: auto;
            padding: 15px;
        }

        .btn.active {
            outline: 2px solid #00000040;
            box-shadow: 0 0 10px #000000c7 inset;
        }
    </style>

</head>

<body class="bg-light">

    <div class="container py-4">

        <!-- HEADER -->
        <div class="header-box">
            <h2>📩 Data Chat API Telegram</h2>
            <p class="mt-2 mb-0">Menampilkan daftar grup, channel & user yang pernah mengirim pesan ke bot Anda.</p>
        </div>

        <div class="row">

            <!-- ======================= -->
            <!-- GRUP -->
            <!-- ======================= -->
            <div class="col-md-4 mb-4">
                <div class="section-title">📌 Grup Telegram</div>

                <?php if (count($grup) == 0) { ?>
                    <div class="alert alert-warning">Belum ada grup ditemukan</div>
                <?php } ?>

                <?php foreach ($grup as $id => $nama) { ?>
                    <div class="tg-card" onclick="lihatPesan('<?= $id ?>','group','<?= $nama ?>')">
                        <div class="tg-title"><?= $nama ?></div>
                        <div class="tg-sub">ID: <?= $id ?></div>
                    </div>
                <?php } ?>
            </div>



            <!-- ======================= -->
            <!-- CHANNEL -->
            <!-- ======================= -->
            <div class="col-md-4 mb-4">
                <div class="section-title">📡 Channel Telegram</div>

                <?php if (count($channel) == 0) { ?>
                    <div class="alert alert-warning">Belum ada channel ditemukan</div>
                <?php } ?>

                <?php foreach ($channel as $id => $nama) { ?>
                    <div class="tg-card" onclick="lihatPesan('<?= $id ?>','channel','<?= $nama ?>')">
                        <div class="tg-title"><?= $nama ?></div>
                        <div class="tg-sub">ID: <?= $id ?></div>
                    </div>
                <?php } ?>
            </div>



            <!-- ======================= -->
            <!-- USER -->
            <!-- ======================= -->
            <div class="col-md-4 mb-4">
                <div class="section-title">👤 User Telegram</div>

                <?php if (count($user) == 0) { ?>
                    <div class="alert alert-warning">Belum ada user ditemukan</div>
                <?php } ?>

                <?php foreach ($user as $id => $nama) { ?>
                    <div class="tg-card" onclick="lihatPesan('<?= $id ?>','private','<?= $nama ?>')">
                        <div class="tg-title"><?= $nama ?></div>
                        <div class="tg-sub">ID: <?= $id ?></div>
                    </div>
                <?php } ?>
            </div>

        </div>

        <!-- TOMBOL MODAL -->
        <button class="btn-main" data-bs-toggle="modal" data-bs-target="#modalAutoMessage">
            🚀 Kirim Pesan
        </button>

    </div>

    <!-- MODAL -->
    <div class="modal fade" id="modalPesan" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="judulModal"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <!-- FILTER -->
                        <div class="col-md-4 border-end">

                            <h5>🔍 Filter</h5>

                            <div class="mb-3">
                                <label class="form-label">Filter User</label>
                                <select id="filterUser" class="form-control" onchange="filterChat()">
                                    <option value="">Semua User</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cari Teks</label>
                                <input type="text" id="filterText" onkeyup="filterChat()" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" id="fromDate" onchange="filterChat()" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dari Waktu (HH:MM:SS)</label>
                                <input type="time" id="fromTime" step="1" onchange="filterChat()" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" id="toDate" onchange="filterChat()" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sampai Waktu (HH:MM:SS)</label>
                                <input type="time" id="toTime" step="1" onchange="filterChat()" class="form-control">
                            </div>

                            <button class="btn btn-secondary w-100" onclick="resetFilter()">Reset</button>

                        </div>

                        <!-- VIEW PESAN -->
                        <div class="col-md-8">
                            <div id="isiPesan" style="height:70vh; overflow-y:auto;"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let chatData = [];
        let offset = 0;
        let limit = 30;
        let activeID = "";
        let activeType = "";
        let loading = false;

        function lihatPesan(id, type, name) {

            activeID = id;
            activeType = type;
            offset = 0;
            chatData = [];

            document.getElementById("judulModal").innerHTML = "Pesan dari: " + name;

            // Tampilkan modal dulu
            let modal = new bootstrap.Modal(document.getElementById("modalPesan"));
            modal.show();

            // Loading
            document.getElementById("isiPesan").innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 60vh;">
            <div class="text-center">
                <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;"></div>
                <p class="mt-3">Mengambil data chat...</p>
            </div>
        </div>
    `;

            // Load batch pertama
            loadChatBatch();
        }

        function loadChatBatch() {
            if (loading) return;
            loading = true;

            fetch(`proses/lihat_pesan_api.php?id=${activeID}&tipe=${activeType}&limit=${limit}&offset=${offset}`)
                .then(res => res.json())
                .then(json => {

                    if (json.length === 0) {
                        loading = false;
                        return;
                    }

                    // Tambah offset
                    offset += json.length;

                    // Simpan ke array utama
                    chatData = [...json, ...chatData];

                    // Render chat
                    renderChatPrepend(json);

                    // Build filter hanya pertama
                    if (offset === json.length) buildUserFilter(chatData);

                    loading = false;
                })
                .catch(e => {
                    loading = false;
                    console.log("Error load batch:", e);
                });
        }

        // Render chat dengan PREPEND (menambah ke atas)
        // Render chat dengan posisi pesan terbaru di BAWAH
        function renderChatPrepend(newItems) {
            let container = document.getElementById("isiPesan");

            // Jika batch pertama → langsung kosongkan container
            if (offset === newItems.length) {
                container.innerHTML = "";
            }

            let oldScrollHeight = container.scrollHeight;

            // Karena ingin pesan terbaru di BAWAH → kita append ke bawah
            newItems.forEach(d => {
                let div = document.createElement("div");
                div.classList.add("chat-bubble");
                div.innerHTML = `
            <div class="chat-name">${d.nama}</div>
            <div class="chat-text">${d.text}</div>
            <div class="chat-time">${d.waktu}</div>
        `;
                container.appendChild(div);
            });

            // Batch pertama → scroll ke paling bawah otomatis
            if (offset === newItems.length) {
                container.scrollTop = container.scrollHeight;
            } else {
                // Saat load batch lama (scroll ke atas sebelumnya)
                // pertahankan posisi scroll agar tidak "lompat"
                container.scrollTop = container.scrollHeight - oldScrollHeight;
            }
        }

        function buildUserFilter(data) {
            let select = document.getElementById("filterUser");
            select.innerHTML = `<option value="">Semua User</option>`;
            let unique = [...new Set(data.map(d => d.nama))];
            unique.forEach(u => {
                select.innerHTML += `<option value="${u}">${u}</option>`;
            });
        }

        // Infinite scroll: ketika discroll ke atas → ambil lagi
        document.addEventListener("DOMContentLoaded", () => {
            let box = document.getElementById("isiPesan");

            box.addEventListener("scroll", function() {
                if (box.scrollTop === 0) {
                    loadChatBatch();
                }
            });
        });
    </script>

    <!-- MODAL AUTO MESSAGE -->
    <div class="modal fade" id="modalAutoMessage" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0">

                <!-- HEADER -->
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-robot"></i> Auto Message Control</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body bg-light">

                    <div class="row">

                        <!-- LEFT SIDEBAR -->
                        <div class="col-md-4 border-end">

                            <!-- MODE SWITCH -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">

                                    <label class="form-label fw-bold">Mode</label>

                                    <div class="btn-group w-100">

                                        <!-- ADD -->
                                        <button id="btnModeAdd" class="btn btn-primary" onclick="setMode('add')"
                                            title="Add Mode">
                                            <i class="bi bi-plus-circle fs-5"></i>
                                        </button>

                                        <!-- DELETE -->
                                        <button id="btnModeDelete" class="btn btn-danger" onclick="setMode('delete')"
                                            title="Delete Mode">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>

                                        <!-- EDIT -->
                                        <button id="btnModeEdit" class="btn btn-warning" onclick="setMode('edit')"
                                            title="Edit Mode">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>

                                        <!-- AUTO -->
                                        <button id="btnModeAuto" class="btn btn-success" onclick="setMode('auto')"
                                            title="Auto Mode">
                                            <i class="bi bi-check2-circle fs-5"></i>
                                        </button>

                                    </div>

                                </div>
                            </div>

                            <!-- waktu loop auto massage -->
                            <div class="card shadow-sm mb-3" id="waktuLoop" style="display: none;">
                                <div class="card-body">

                                    <label class="form-label fw-bold">Waktu Loop Auto Message</label>

                                    <div class="btn-group w-100">

                                        <!-- cek detik -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="loopDetik" value="detik"
                                                onchange="toggleSelect('detik')">
                                            <label class="form-check-label" for="loopDetik">Detik</label>
                                        </div>

                                        <!-- cek menit -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="loopMenit" value="menit"
                                                onchange="toggleSelect('menit')">
                                            <label class="form-check-label" for="loopMenit">Menit</label>
                                        </div>

                                        <!-- cek jam -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="loopJam" value="jam"
                                                onchange="toggleSelect('jam')">
                                            <label class="form-check-label" for="loopJam">Jam</label>
                                        </div>

                                        <!-- cek hari -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="loopHari" value="hari"
                                                onchange="toggleSelect('hari')">
                                            <label class="form-check-label" for="loopHari">Hari</label>
                                        </div>

                                    </div>

                                    <!-- tempat muncul select option -->
                                    <div id="containerSelectLoop" class="mt-3"></div>

                                </div>
                            </div>

                            <script>
                                function toggleSelect(type) {
                                    let container = document.getElementById("containerSelectLoop");
                                    let checkbox = document.getElementById("loop" + capitalize(type));

                                    // Jika dicentang → buat select
                                    if (checkbox.checked) {
                                        createSelect(type);
                                    } else {
                                        // hilangkan select jika tidak dicentang
                                        let oldSelect = document.getElementById("select_" + type);
                                        if (oldSelect) oldSelect.remove();
                                    }
                                }

                                // Membuat select option
                                function createSelect(type) {
                                    let container = document.getElementById("containerSelectLoop");

                                    // Cek kalau sudah ada, jangan duplikasi
                                    if (document.getElementById("select_" + type)) return;

                                    let label = capitalize(type);

                                    // jumlah maksimum
                                    let max = 60;
                                    if (type === "jam") max = 24;
                                    if (type === "hari") max = 30;

                                    let html = `
        <div class="mb-2" id="select_${type}">
            <label class="form-label fw-bold">${label}</label>
            <select class="form-select">
                ${generateOptions(type, max)}
            </select>
        </div>
    `;

                                    container.insertAdjacentHTML("beforeend", html);
                                }

                                // generate isi option
                                function generateOptions(type, max) {
                                    let opts = "";
                                    for (let i = 1; i <= max; i++) {
                                        opts += `<option value="${i}">${i} ${type}</option>`;
                                    }
                                    return opts;
                                }

                                function capitalize(str) {
                                    return str.charAt(0).toUpperCase() + str.slice(1);
                                }
                            </script>


                            <!-- CARD SETTINGS -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">

                                    <h5 class="fw-bold mb-3"><i class="bi bi-gear"></i> Pengaturan</h5>

                                    <!-- PILIH GRUP -->
                                    <div id="groupSelectBox" class="mb-3" style="display: none;">
                                        <label class="form-label">Pilih Tujuan Pengiriman</label>

                                        <!-- GRUP -->
                                        <div id="listGroupRadio">
                                            <h6 class="fw-bold">📌 Grup Telegram</h6>

                                            <?php if (count($grup) > 0): ?>
                                                <?php foreach ($grup as $id => $nama): ?>
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input" type="checkbox" name="target[]"
                                                            value="<?= $id ?>" id="grup<?= $id ?>">
                                                        <label class="form-check-label" for="grup<?= $id ?>">
                                                            <?= htmlspecialchars($nama) ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="text-muted">Belum ada grup ditemukan…</div>
                                            <?php endif; ?>
                                        </div>

                                        <hr>

                                        <!-- CHANNEL -->
                                        <div id="listChannelRadio">
                                            <h6 class="fw-bold">📡 Channel Telegram</h6>

                                            <?php if (count($channel) > 0): ?>
                                                <?php foreach ($channel as $id => $nama): ?>
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input" type="checkbox" name="target[]"
                                                            value="<?= $id ?>" id="channel<?= $id ?>">
                                                        <label class="form-check-label" for="channel<?= $id ?>">
                                                            <?= htmlspecialchars($nama) ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="text-muted">Belum ada channel ditemukan…</div>
                                            <?php endif; ?>
                                        </div>

                                        <hr>

                                        <!-- USER -->
                                        <div id="listUserRadio">
                                            <h6 class="fw-bold">👤 User Telegram</h6>

                                            <?php if (count($user) > 0): ?>
                                                <?php foreach ($user as $id => $nama): ?>
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input" type="checkbox" name="target[]"
                                                            value="<?= $id ?>" id="user<?= $id ?>">
                                                        <label class="form-check-label" for="user<?= $id ?>">
                                                            <?= htmlspecialchars($nama) ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="text-muted">Belum ada user ditemukan…</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- SEARCH MESSAGE -->
                                    <div id="searchBox" class="mb-3" style="display:none;">
                                        <label class="form-label">Cari Pesan Broadcast</label>
                                        <input type="text" id="searchMessage" class="form-control"
                                            placeholder="ketik untuk mencari...">
                                    </div>

                                    <button id="btnDeleteAll" class="btn btn-danger w-100"
                                        onclick="deleteSelectedBroadcast()" style="display:none;">
                                        <i class="bi bi-trash"></i> Hapus Yang Dicentang
                                    </button>


                                    <!-- ========================================================= -->
                                    <!-- BOX EDIT PESAN (RAPI & MODERN) -->
                                    <!-- ========================================================= -->
                                    <div id="editBox" class="edit-box hidden">

                                        <label class="form-label fw-bold mb-2">Edit Pesan</label>

                                        <!-- PREVIEW PESAN -->
                                        <div id="editChatPreview" class="chat-preview">
                                            <div class="text-muted text-center mt-5">Mulai edit pesan…</div>
                                        </div>

                                        <!-- TEXTAREA + BUTTON -->
                                        <div class="input-wrapper">
                                            <textarea id="editPesanBaru" class="input-textarea"
                                                placeholder="Tulis pesan…"></textarea>

                                            <button id="btnSendEdit" class="btn-send" onclick="updateBroadcast()">
                                                <i class="bi bi-send-fill"></i>
                                            </button>
                                        </div>

                                    </div>

                                    <style>
                                        /* SMOOTH OPEN ANIMATION */
                                        .edit-box {
                                            animation: fadeIn 0.25s ease;
                                        }

                                        .hidden {
                                            display: none;
                                        }

                                        @keyframes fadeIn {
                                            from {
                                                opacity: 0;
                                                transform: translateY(10px);
                                            }

                                            to {
                                                opacity: 1;
                                                transform: translateY(0);
                                            }
                                        }

                                        /* PREVIEW CHAT */
                                        .chat-preview {
                                            height: 250px;
                                            overflow-y: auto;
                                            background: white;
                                            border-radius: 12px;
                                            padding: 12px;
                                            border: 1px solid #d1d5db;
                                        }

                                        /* TEXTAREA WRAPPER */
                                        .input-wrapper {
                                            position: relative;
                                            margin-top: 10px;
                                        }

                                        /* TEXTAREA STYLE */
                                        .input-textarea {
                                            width: 100%;
                                            min-height: 80px;
                                            max-height: 200px;
                                            padding: 15px 60px 15px 15px;
                                            border-radius: 15px;
                                            border: 2px solid #3b82f6;
                                            font-size: 16px;
                                            line-height: 22px;
                                            resize: none;
                                            outline: none;
                                            overflow-y: hidden;
                                        }

                                        /* CUSTOM SCROLLBAR */
                                        .input-textarea::-webkit-scrollbar {
                                            width: 6px;
                                        }

                                        .input-textarea::-webkit-scrollbar-thumb {
                                            background: #9ca3af;
                                            border-radius: 10px;
                                        }

                                        /* SEND BUTTON */
                                        .btn-send {
                                            position: absolute;
                                            right: 10px;
                                            bottom: 12px;

                                            width: 45px;
                                            height: 45px;
                                            background: #3b82f6;
                                            border: none;
                                            color: white;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;

                                            font-size: 20px;
                                            cursor: pointer;
                                            transition: 0.2s;
                                            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                                        }

                                        .btn-send:hover {
                                            background: #2563eb;
                                        }
                                    </style>

                                    <!-- INPUT PESAN BARU -->
                                    <div id="inputAddBox" style="display:none;">
                                        <label class="form-label fw-bold mb-2">Pesan Baru</label>

                                        <div id="addChatPreview" style="height:250px; overflow-y:auto;"
                                            class="mb-2 p-2 bg-white rounded border">
                                            <div class="text-muted text-center mt-5">Mulai buat pesan…</div>
                                        </div>

                                        <div class="chat-input-container">
                                            <textarea class="chat-textarea" id="inputPesanBaru"
                                                placeholder="Tulis pesan…"></textarea>
                                            <button class="btn-send" onclick="simpanBroadcast()" id="btnSendTambah">
                                                <i class="bi bi-send-fill"></i>
                                            </button>
                                        </div>

                                    </div>
                                    <script>
                                        // ==============================
                                        // SETTING MAX HEIGHT
                                        // ==============================
                                        const MAX_HEIGHT = 200; // <-- ubah sesuai kebutuhan

                                        // ==============================
                                        // TEXTAREA TAMBAH
                                        // ==============================
                                        const inputPesanBaru = document.getElementById("inputPesanBaru");
                                        const btnSendTambah = document.getElementById("btnSendTambah");

                                        inputPesanBaru.addEventListener("input", () => {

                                            // Reset agar tidak error saat resize
                                            inputPesanBaru.style.height = "auto";

                                            // Jika konten masih di bawah batas → auto resize
                                            if (inputPesanBaru.scrollHeight <= MAX_HEIGHT) {
                                                inputPesanBaru.style.overflowY = "hidden";
                                                inputPesanBaru.style.height = inputPesanBaru
                                                    .scrollHeight + "px";
                                            }
                                            // Jika konten melebihi batas → kunci di max height + aktifkan scroll
                                            else {
                                                inputPesanBaru.style.height = MAX_HEIGHT + "px";
                                                inputPesanBaru.style.overflowY = "auto";
                                            }

                                            btnSendTambah.style.display = "block";
                                        });

                                        // ======================================================
                                        // TEXTAREA AUTO RESIZE (EDIT)
                                        // ======================================================
                                        const editTextarea = document.getElementById("editPesanBaru");
                                        const btnSendEdit = document.getElementById("btnSendEdit");

                                        editTextarea.addEventListener("input", () => {

                                            editTextarea.style.height = "auto";

                                            if (editTextarea.scrollHeight <= MAX_HEIGHT) {
                                                editTextarea.style.overflowY = "hidden";
                                                editTextarea.style.height = editTextarea.scrollHeight + "px";
                                            } else {
                                                editTextarea.style.height = MAX_HEIGHT + "px";
                                                editTextarea.style.overflowY = "auto";
                                            }

                                            btnSendEdit.style.display = "block";
                                        });
                                    </script>

                                    <!-- TOMBOL KIRIM AUTO MESSAGE -->
                                    <div id="autoSendBox" class="mt-4" style="display:none;">
                                        <div id="autoTerminal" style="background:#000;color:#0f0;padding:10px;height:200px;
     overflow:auto;font-family:monospace;display:none;">
                                        </div>


                                        <button class="btn btn-success w-100" onclick="kirimAutoMessage()">
                                            <i class="bi bi-telegram"></i> Kirim Auto Message
                                        </button>

                                        <!-- stop auto message -->
                                        <button class="btn btn-danger w-100 mt-2" onclick="stopAutoMessage()">
                                            <i class="bi bi-stop-circle"></i> Stop Auto Message
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- RIGHT CONTENT -->
                        <div class="col-md-8">

                            <!-- LIST PESAN -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-white fw-bold">
                                    <i class="bi bi-chat-left-text"></i> Daftar Pesan Broadcast

                                    <div class="float-end">
                                        <!-- chack all semua pesan -->
                                        <div class="form-check form-switch" style="display: none;" id="chackAllGrup">
                                            <input class="form-check-input" type="checkbox" id="checkAllPesan">
                                            <label class="form-check-label" for="checkAllPesan">Pilih
                                                Semua</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="listPesanBroadcast" style="height:70vh;" class="bg-white">
                                    <div class="text-center text-muted mt-5">
                                        <i class="bi bi-inboxes" style="font-size:40px;"></i>
                                        <p class="mt-2">Belum ada data…</p>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
        let currentMode = "auto";
        let autoLoop = null;

        function getLoopInterval() {
            let totalMs = 0;

            // cek detik
            let s = document.querySelector("#select_detik select");
            if (s) totalMs += parseInt(s.value) * 1000;

            // cek menit
            let m = document.querySelector("#select_menit select");
            if (m) totalMs += parseInt(m.value) * 60000;

            // cek jam
            let j = document.querySelector("#select_jam select");
            if (j) totalMs += parseInt(j.value) * 3600000;

            // cek hari
            let h = document.querySelector("#select_hari select");
            if (h) totalMs += parseInt(h.value) * 86400000;

            return totalMs;
        }

        // ==============================
        // AUTO LOOP WORKER (MODIFIED)
        // ==============================
        function startAutoLoop() {
            if (autoLoop !== null) return;

            // ambil waktu dari user
            let interval = getLoopInterval();

            if (interval <= 0) {
                alert("Silakan pilih waktu loop terlebih dahulu!");
                return;
            }

            console.log("Auto Loop berjalan setiap " + interval + " ms");

            autoLoop = setInterval(() => {
                fetch("proses/auto_worker_userbot.php")
                    .then(r => r.json())
                    .then(res => {
                        let term = document.getElementById("autoTerminal");
                        term.innerHTML += res.log.replace(/\n/g, "<br>");
                        term.scrollTop = term.scrollHeight;

                        if (res.stop) {
                            clearInterval(autoLoop);
                            autoLoop = null;
                            term.innerHTML +=
                                "<span style='color:red;'>Auto message dihentikan</span><br>";
                        }
                    });
            }, interval);
        }


        function stopAutoMessage() {
            fetch("proses/stop_auto_message.php")
                .then(r => r.text())
                .then(res => {
                    alert("Auto message dihentikan!");
                });
        }

        // ==============================
        // MODE SWITCHER
        // ==============================
        function setMode(mode) {
            currentMode = mode;

            document.getElementById("btnModeAdd").classList.remove("active");
            document.getElementById("btnModeDelete").classList.remove("active");
            document.getElementById("btnModeAuto").classList.remove("active");

            document.getElementById("btnMode" + capitalize(mode)).classList.add("active");

            document.getElementById("searchBox").style.display = "none";
            document.getElementById("inputAddBox").style.display = "none";
            document.getElementById("autoSendBox").style.display = "none";
            document.getElementById("groupSelectBox").style.display = "none";
            document.getElementById("editBox").style.display = "none";
            document.getElementById("btnDeleteAll").style.display = "none";
            document.getElementById("chackAllGrup").style.display = "none";
            document.getElementById("waktuLoop").style.display = "none";

            if (mode === "add") document.getElementById("inputAddBox").style.display = "block";
            if (mode === "delete") document.getElementById("searchBox").style.display = "block";
            if (mode === "delete") document.getElementById("btnDeleteAll").style.display = "block";
            if (mode === "delete") document.getElementById("chackAllGrup").style.display = "block";
            if (mode === "auto") document.getElementById("autoSendBox").style.display = "block";
            if (mode === "auto") document.getElementById("groupSelectBox").style.display = "block";
            if (mode === "auto") document.getElementById("chackAllGrup").style.display = "block";
            if (mode === "auto") document.getElementById("waktuLoop").style.display = "block";
            if (mode === "edit") document.getElementById("editBox").style.display = "block";

            document.querySelectorAll(".btn-group button").forEach(btn => {
                btn.classList.remove("active");
            });

            document.getElementById("btnMode" +
                mode.charAt(0).toUpperCase() + mode.slice(1)).classList.add("active");

            loadBroadcastList();
        }

        function capitalize(s) {
            return s.charAt(0).toUpperCase() + s.slice(1);
        }

        // ==============================
        // CHECK ALL PESAN (MASTER CONTROL)
        // ==============================
        document.getElementById("checkAllPesan").addEventListener("change", function() {
            let status = this.checked;
            document.querySelectorAll("input[name='selectBroadcast']").forEach(cb => {
                cb.checked = status;
            });
        });

        // ==============================
        // LOAD BROADCAST LIST
        // ==============================
        function loadBroadcastList() {
            fetch("proses/get_broadcast.php")
                .then(r => r.json())
                .then(data => {
                    let box = document.getElementById("listPesanBroadcast");

                    if (data.length === 0) {
                        box.innerHTML = `
                <div class='text-center text-muted mt-5'>
                    <i class="bi bi-inboxes" style="font-size:40px;"></i>
                    <p class="mt-2">Tidak ada pesan</p>
                </div>`;
                        return;
                    }

                    let html = "";

                    data.forEach(d => {
                        html += `
                <div class="p-2 border-bottom">

                    ${currentMode === "auto" || currentMode === "delete" ?
                    `<input type='checkbox' class='form-check-input me-2'
                        name='selectBroadcast' value='${d.id}'>`
                    : ""}

                    <div class="bubble other d-inline-block">
                        ${d.pesan}
                        <div class="bubble-time">${d.waktu_kirim}</div>
                    </div>

                    ${currentMode === "edit" ?
                    `<button class="btn btn-primary btn-sm ms-2" onclick="editBroadcast(${d.id})">
                        <i class="bi bi-pencil"></i>
                    </button>`
                    : ""}

                    ${currentMode === "delete" ?
                    `<button class="btn btn-danger btn-sm ms-2" onclick="hapusBroadcast(${d.id})">
                        <i class="bi bi-trash"></i>
                    </button>`
                    : ""}
                </div>
                `;
                    });

                    box.innerHTML = html;
                    box.scrollTop = box.scrollHeight;

                    // ===========================================
                    // SET CHECKBOX MENGIKUTI MASTER SWITCH
                    // ===========================================
                    let master = document.getElementById("checkAllPesan");
                    document.querySelectorAll("input[name='selectBroadcast']").forEach(cb => {
                        cb.checked = master.checked;
                    });

                });
        }
        // =======================================
        // FUNGSI EDIT BROADCAST
        // =======================================
        function editBroadcast(id) {

            // tampilkan box edit
            document.getElementById("editBox").style.display = "block";

            // simpan ID yang sedang diedit
            window.currentEditID = id;

            // ambil data pesan berdasarkan id
            fetch("proses/get_broadcast_by_id.php?id=" + id)
                .then(r => r.json())
                .then(res => {

                    let txt = document.getElementById("editPesanBaru");
                    let preview = document.getElementById("editChatPreview");

                    // masukkan pesan ke textarea
                    txt.value = res.pesan;

                    // resize textarea otomatis
                    txt.style.height = "auto";
                    txt.style.height = txt.scrollHeight + "px";

                    // tampilkan preview
                    preview.innerHTML = `
                <div class="bubble other d-inline-block">
                    ${res.pesan}
                    <div class="bubble-time">Editing…</div>
                </div>
            `;

                    // tampilkan tombol kirim edit
                    document.getElementById("btnSendEdit").style.display = "block";
                });
        }

        function updateBroadcast() {
            let newText = editTextarea.value;

            if (!newText.trim()) {
                alert("Pesan tidak boleh kosong!");
                return;
            }

            let form = new FormData();
            form.append("id", window.currentEditID);
            form.append("pesan", newText);

            fetch("proses/update_broadcast.php", {
                    method: "POST",
                    body: form
                })
                .then(r => r.text())
                .then(res => {
                    if (res === "SUCCESS") {

                        document.getElementById("editBox").classList.add("hidden");

                        loadBroadcastList();

                    } else {
                        alert("Gagal mengupdate pesan!");
                    }
                });
        }

        // ==============================
        // SIMPAN PESAN BARU
        // ==============================
        function simpanBroadcast() {
            let pesan = document.getElementById("inputPesanBaru").value.trim();
            if (pesan === "") return alert("Isi pesan terlebih dahulu");

            fetch("proses/add_broadcast.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "pesan=" + encodeURIComponent(pesan)
                })
                .then(r => r.text())
                .then(res => {
                    document.getElementById("inputPesanBaru").value = "";
                    loadBroadcastList();
                });
        }

        // ==============================
        // HAPUS 1 PESAN
        // ==============================
        function hapusBroadcast(id) {
            if (!confirm("Yakin hapus pesan ini?")) return;

            fetch("proses/delete_broadcast.php?id=" + id)
                .then(r => r.json())
                .then(res => {
                    if (res.status === "success") {
                        alert("Pesan berhasil dihapus!");
                    } else {
                        alert("Gagal menghapus:\n" + res.msg);
                    }
                    loadBroadcastList();
                })
                .catch(err => {
                    alert("Terjadi kesalahan koneksi!");
                });
        }

        // ==============================
        // WORKER AUTO MESSAGE
        // ==============================
        function runAutoWorker() {
            fetch("proses/auto_worker_userbot.php")
                .then(r => r.json())
                .then(res => {
                    let term = document.getElementById("autoTerminal");
                    term.innerHTML += res.log.replace(/\n/g, "<br>");
                    term.scrollTop = term.scrollHeight;

                    if (res.stop) {
                        clearInterval(autoLoop);
                        autoLoop = null;
                        term.innerHTML += "<span style='color:red;'>Auto message dihentikan</span><br>";
                    }
                });
        }

        // ==============================
        // KIRIM AUTO MESSAGE
        // ==============================
        function kirimAutoMessage() {
            let checks = document.querySelectorAll("input[name='selectBroadcast']:checked");
            if (checks.length === 0) return alert("Pilih pesan terlebih dahulu!");

            let pesanDipilih = [];
            checks.forEach(c => pesanDipilih.push(c.value));

            let checksTarget = document.querySelectorAll("input[name='target[]']:checked");
            if (checksTarget.length === 0) return alert("Pilih grup / channel / user terlebih dahulu!");

            let targetSelected = [];
            checksTarget.forEach(c => targetSelected.push(c.value));

            if (!confirm("Aktifkan AUTO MESSAGE?")) return;

            fetch("proses/kirim_auto_message.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "target=" + encodeURIComponent(JSON.stringify(targetSelected)) +
                        "&pesan=" + encodeURIComponent(JSON.stringify(pesanDipilih))
                })
                .then(r => r.text())
                .then(res => {

                    if (res.includes("SUCCESS")) {
                        let term = document.getElementById("autoTerminal");
                        term.style.display = "block";
                        term.innerHTML = "<b>Auto Message dimulai...</b><br>";

                        runAutoWorker();
                        startAutoLoop();

                        alert("Auto Message berjalan!");
                    } else {
                        alert("Gagal mengirim:\n" + res);
                    }

                });
        }

        // ==============================
        // HAPUS PESAN TERPILIH
        // ==============================
        function deleteSelectedBroadcast() {
            let checks = document.querySelectorAll("input[name='selectBroadcast']:checked");

            if (checks.length === 0) {
                return alert("Pilih pesan yang ingin dihapus!");
            }

            let ids = [];
            checks.forEach(c => ids.push(c.value));

            if (!confirm(`Hapus ${ids.length} pesan yang dipilih?`)) return;

            fetch("proses/delete_all_broadcast.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "ids=" + encodeURIComponent(JSON.stringify(ids))
                })
                .then(r => r.text())
                .then(res => {
                    if (res.includes("SUCCESS")) {
                        alert("Pesan berhasil dihapus!");
                    } else {
                        alert("Gagal menghapus:\n" + res);
                    }

                    loadBroadcastList();
                })
                .catch(err => alert("Terjadi kesalahan koneksi!"));
        }
    </script>


</body>

</html>