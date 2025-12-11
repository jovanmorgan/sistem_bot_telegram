<?php
$cmd = 'python "' . __DIR__ . '/proses/get_chats_userbot.py" 2>&1';
$out = shell_exec($cmd);

$data = json_decode($out, true);

if (!$data) {
    die("<b>Gagal decode JSON!</b><br><pre>$out</pre>");
}

$grup = $data["grup"] ?? [];
$channel = $data["channel"] ?? [];
$user = $data["user"] ?? [];
$error = $data["error"] ?? null;

if ($error) {
    echo "<div class='alert alert-danger'>ERROR: $error</div>";
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
    <link rel="stylesheet" href="bot_cnet.css">

</head>

<body class="bg-light">

    <div class="container py-4">

        <!-- HEADER -->
        <div class="header-box">
            <h2>📩 Data Chat API Telegram</h2>
            <p class="mt-2 mb-0">Menampilkan daftar grup, channel & user yang pernah mengirim pesan ke bot Anda.</p>
        </div>

        <div class="row">

            <!-- GRUP -->
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

            <!-- CHANNEL -->
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

            <!-- USER -->
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

                                    <!-- TOMBOL KIRIM AUTO MESSAGE -->
                                    <div id="autoSendBox" class="mt-4 mb-5" style="display:none; margin-bottom: 20px;">
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
    <script src="bot_cnet.js"></script>
</body>

</html>