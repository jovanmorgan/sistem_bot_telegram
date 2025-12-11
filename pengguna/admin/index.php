<?php
include '../../keamanan/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Pesan Telegram</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .chat-bubble {
            background: #ffe3e3;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 18px;
            width: 100%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .chat-name {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .chat-text {
            font-size: 15px;
            white-space: normal;
            word-break: break-word;
        }

        .chat-time {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
            text-align: right;
        }

        #isiPesan {
            background: #fafafa;
            padding-right: 10px;
        }

        .chat-bubble {
            transition: 0.2s;
        }

        .chat-bubble:hover {
            background: #ffdcdc;
        }
    </style>

</head>

<body class="bg-light">

    <div class="container py-4">
        <h2 class="mb-4">📩 Daftar Grup & User Telegram</h2>

        <div class="row">

            <!-- GRUP -->
            <div class="col-md-6">
                <h4>📌 Grup Telegram</h4>
                <div class="list-group">

                    <?php
                    $grups = mysqli_query($koneksi, "SELECT * FROM daftar_grup");
                    while ($g = mysqli_fetch_assoc($grups)) {

                        $gid = $g['grup_id'];

                        $count = mysqli_fetch_assoc(mysqli_query(
                            $koneksi,
                            "SELECT COUNT(*) AS jml FROM pesan_masuk WHERE chat_id='$gid'"
                        ))["jml"];
                    ?>

                        <button class="list-group-item list-group-item-action"
                            onclick="lihatPesan('<?= $gid ?>', 'group', '<?= $g['nama'] ?>')">
                            <b><?= $g['nama'] ?></b>
                            <span class="badge bg-primary float-end"><?= $count ?> Pesan</span>
                        </button>

                    <?php } ?>

                </div>
            </div>

            <!-- USER -->
            <div class="col-md-6">
                <h4>👤 User (Private Chat)</h4>
                <div class="list-group">

                    <?php
                    $users = mysqli_query($koneksi, "SELECT * FROM daftar_user");
                    while ($u = mysqli_fetch_assoc($users)) {

                        $uid = $u['user_id'];

                        $count = mysqli_fetch_assoc(mysqli_query(
                            $koneksi,
                            "SELECT COUNT(*) AS jml FROM pesan_masuk WHERE sender_id='$uid' AND chat_type='private'"
                        ))["jml"];
                    ?>

                        <button class="list-group-item list-group-item-action"
                            onclick="lihatPesan('<?= $uid ?>', 'private', '<?= $u['nama'] ?>')">
                            <b><?= $u['nama'] ?></b>
                            <span class="badge bg-success float-end"><?= $count ?> Pesan</span>
                        </button>

                    <?php } ?>

                </div>
            </div>

        </div>
    </div>


    <!-- MODAL POPUP -->
    <div class="modal fade" id="modalPesan" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <!-- diperbesar -->
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="judulModal"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <!-- FILTER KIRI -->
                        <div class="col-md-4 border-end">

                            <h5>🔍 Filter Pesan</h5>
                            <div class="mb-3">
                                <label class="form-label">Filter User</label>
                                <select id="filterUser" onchange="filterChat()" class="form-control">
                                    <option value="">Semua</option>

                                    <?php
                                    $usr = mysqli_query($koneksi, "SELECT DISTINCT sender_name FROM pesan_masuk ORDER BY sender_name ASC");
                                    while ($u = mysqli_fetch_assoc($usr)) {
                                        echo "<option value='{$u['sender_name']}'>{$u['sender_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Cari Teks</label>
                                <input type="text" id="filterText" onkeyup="filterChat()" class="form-control"
                                    placeholder="Cari dalam pesan...">
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


                            <button class="btn btn-secondary w-100" onclick="resetFilter()">Reset Filter</button>

                        </div>

                        <!-- CHAT KANAN -->
                        <div class="col-md-8">
                            <div id="isiPesan" style="height:70vh; overflow-y:auto;">
                                Memuat...
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentID = "";
        let currentType = "";
        let currentName = "";
        let originalChat = "";

        function buildDateTime(date, time) {
            if (!date) return "";
            if (!time) time = "00:00:00";
            return date + "T" + time;
        }

        function lihatPesan(id, tipe, nama) {
            currentID = id;
            currentType = tipe;
            currentName = nama;

            document.getElementById("judulModal").innerHTML = "Pesan dari: " + nama;

            loadChat();

            new bootstrap.Modal(document.getElementById("modalPesan")).show();
        }

        function loadChat() {
            let search = document.getElementById("filterText").value;
            let user = document.getElementById("filterUser").value;

            let fromDate = document.getElementById("fromDate").value;
            let fromTime = document.getElementById("fromTime").value;
            let toDate = document.getElementById("toDate").value;
            let toTime = document.getElementById("toTime").value;

            let from = buildDateTime(fromDate, fromTime);
            let to = buildDateTime(toDate, toTime);

            fetch(
                    `proses/lihat_pesan.php?id=${currentID}&tipe=${currentType}&search=${search}&from=${from}&to=${to}&user=${user}`
                )
                .then(res => res.text())
                .then(html => {
                    originalChat = html;
                    document.getElementById("isiPesan").innerHTML = html;
                });
        }

        function convertToISO(dt) {
            return dt.replace(" ", "T");
        }

        function filterChat() {
            let txt = document.getElementById("filterText").value.toLowerCase();
            let user = document.getElementById("filterUser").value;

            let fromDate = document.getElementById("fromDate").value;
            let fromTime = document.getElementById("fromTime").value;
            let toDate = document.getElementById("toDate").value;
            let toTime = document.getElementById("toTime").value;

            let from = buildDateTime(fromDate, fromTime);
            let to = buildDateTime(toDate, toTime);

            let parser = new DOMParser();
            let doc = parser.parseFromString(originalChat, "text/html");
            let bubbles = doc.querySelectorAll(".chat-bubble");

            let result = "";

            bubbles.forEach(bubble => {
                let content = bubble.innerText.toLowerCase();
                let waktuText = bubble.querySelector(".chat-time").innerText.trim();
                let namaPengirim = bubble.querySelector(".chat-name").innerText.trim();

                let waktuISO = convertToISO(waktuText);

                if (txt && !content.includes(txt)) return;
                if (user && user !== namaPengirim) return;
                if (from && waktuISO < from) return;
                if (to && waktuISO > to) return;

                result += bubble.outerHTML;
            });

            document.getElementById("isiPesan").innerHTML =
                result === "" ? "<p class='text-muted'>Tidak ada hasil.</p>" : result;
        }

        function resetFilter() {
            document.getElementById("filterText").value = "";
            document.getElementById("filterUser").value = "";
            document.getElementById("fromDate").value = "";
            document.getElementById("fromTime").value = "";
            document.getElementById("toDate").value = "";
            document.getElementById("toTime").value = "";

            document.getElementById("isiPesan").innerHTML = originalChat;
        }
    </script>


</body>

</html>