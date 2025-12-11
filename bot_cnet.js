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

function startAutoLoop() {
  if (autoLoop !== null) return;

  let interval = getLoopInterval();

  if (interval <= 0) {
    alert("Silakan pilih waktu loop terlebih dahulu!");
    return;
  }

  console.log("Auto Loop berjalan setiap " + interval + " ms");

  autoLoop = setInterval(() => {
    fetch("proses/auto_worker_userbot.php")
      .then((r) => r.json())
      .then((res) => {
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

function runAutoWorkerOnce() {
  fetch("proses/auto_worker_userbot.php")
    .then((r) => r.json())
    .then((res) => {
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
}

function stopAutoMessage() {
  fetch("proses/stop_auto_message.php")
    .then((r) => r.text())
    .then((res) => {
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

  if (mode === "add")
    document.getElementById("inputAddBox").style.display = "block";
  if (mode === "delete")
    document.getElementById("searchBox").style.display = "block";
  if (mode === "delete")
    document.getElementById("btnDeleteAll").style.display = "block";
  if (mode === "delete")
    document.getElementById("chackAllGrup").style.display = "block";
  if (mode === "auto")
    document.getElementById("autoSendBox").style.display = "block";
  if (mode === "auto")
    document.getElementById("groupSelectBox").style.display = "block";
  if (mode === "auto")
    document.getElementById("chackAllGrup").style.display = "block";
  if (mode === "auto")
    document.getElementById("waktuLoop").style.display = "block";
  if (mode === "edit")
    document.getElementById("editBox").style.display = "block";

  document.querySelectorAll(".btn-group button").forEach((btn) => {
    btn.classList.remove("active");
  });

  document
    .getElementById("btnMode" + mode.charAt(0).toUpperCase() + mode.slice(1))
    .classList.add("active");

  loadBroadcastList();
}

function capitalize(s) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}

// ==============================
// CHECK ALL PESAN (MASTER CONTROL)
// ==============================
document
  .getElementById("checkAllPesan")
  .addEventListener("change", function () {
    let status = this.checked;
    document.querySelectorAll("input[name='selectBroadcast']").forEach((cb) => {
      cb.checked = status;
    });
  });

// ==============================
// LOAD BROADCAST LIST
// ==============================
function loadBroadcastList() {
  fetch("proses/get_broadcast.php")
    .then((r) => r.json())
    .then((data) => {
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

      data.forEach((d) => {
        html += `
                <div class="p-2 border-bottom">

                    ${
                      currentMode === "auto" || currentMode === "delete"
                        ? `<input type='checkbox' class='form-check-input me-2'
                        name='selectBroadcast' value='${d.id}'>`
                        : ""
                    }

                    <div class="bubble other d-inline-block">
                        ${d.pesan}
                        <div class="bubble-time">${d.waktu_kirim}</div>
                    </div>

                    ${
                      currentMode === "edit"
                        ? `<button class="btn btn-primary btn-sm ms-2" onclick="editBroadcast(${d.id})">
                        <i class="bi bi-pencil"></i>
                    </button>`
                        : ""
                    }

                    ${
                      currentMode === "delete"
                        ? `<button class="btn btn-danger btn-sm ms-2" onclick="hapusBroadcast(${d.id})">
                        <i class="bi bi-trash"></i>
                    </button>`
                        : ""
                    }
                </div>
                `;
      });

      box.innerHTML = html;
      box.scrollTop = box.scrollHeight;

      // ===========================================
      // SET CHECKBOX MENGIKUTI MASTER SWITCH
      // ===========================================
      let master = document.getElementById("checkAllPesan");
      document
        .querySelectorAll("input[name='selectBroadcast']")
        .forEach((cb) => {
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
    .then((r) => r.json())
    .then((res) => {
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
    body: form,
  })
    .then((r) => r.text())
    .then((res) => {
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
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "pesan=" + encodeURIComponent(pesan),
  })
    .then((r) => r.text())
    .then((res) => {
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
    .then((r) => r.json())
    .then((res) => {
      if (res.status === "success") {
        alert("Pesan berhasil dihapus!");
      } else {
        alert("Gagal menghapus:\n" + res.msg);
      }
      loadBroadcastList();
    })
    .catch((err) => {
      alert("Terjadi kesalahan koneksi!");
    });
}

function kirimAutoMessage() {
  let checks = document.querySelectorAll(
    "input[name='selectBroadcast']:checked"
  );
  if (checks.length === 0) return alert("Pilih pesan terlebih dahulu!");

  let pesanDipilih = [];
  checks.forEach((c) => pesanDipilih.push(c.value));

  let checksTarget = document.querySelectorAll(
    "input[name='target[]']:checked"
  );
  if (checksTarget.length === 0)
    return alert("Pilih grup / channel / user terlebih dahulu!");

  let targetSelected = [];
  checksTarget.forEach((c) => targetSelected.push(c.value));

  if (!confirm("Aktifkan AUTO MESSAGE?")) return;

  fetch("proses/kirim_auto_message.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body:
      "target=" +
      encodeURIComponent(JSON.stringify(targetSelected)) +
      "&pesan=" +
      encodeURIComponent(JSON.stringify(pesanDipilih)),
  })
    .then((r) => r.text())
    .then((res) => {
      if (res.includes("SUCCESS")) {
        let term = document.getElementById("autoTerminal");
        term.style.display = "block";
        term.innerHTML = "<b>Auto Message dimulai...</b><br>";

        // 🔥 JALANKAN SEKALI SAAT INI (langsung kirim)
        runAutoWorkerOnce();

        // 🔥 SETELAH 1 DETIK → mulai loop
        setTimeout(() => {
          startAutoLoop();
        }, 1000);

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
  let checks = document.querySelectorAll(
    "input[name='selectBroadcast']:checked"
  );

  if (checks.length === 0) {
    return alert("Pilih pesan yang ingin dihapus!");
  }

  let ids = [];
  checks.forEach((c) => ids.push(c.value));

  if (!confirm(`Hapus ${ids.length} pesan yang dipilih?`)) return;

  fetch("proses/delete_all_broadcast.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "ids=" + encodeURIComponent(JSON.stringify(ids)),
  })
    .then((r) => r.text())
    .then((res) => {
      if (res.includes("SUCCESS")) {
        alert("Pesan berhasil dihapus!");
      } else {
        alert("Gagal menghapus:\n" + res);
      }

      loadBroadcastList();
    })
    .catch((err) => alert("Terjadi kesalahan koneksi!"));
}

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

  fetch(
    `proses/lihat_pesan_api.php?id=${activeID}&tipe=${activeType}&limit=${limit}&offset=${offset}`
  )
    .then((res) => res.json())
    .then((json) => {
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
    .catch((e) => {
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
  newItems.forEach((d) => {
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
  let unique = [...new Set(data.map((d) => d.nama))];
  unique.forEach((u) => {
    select.innerHTML += `<option value="${u}">${u}</option>`;
  });
}

// Infinite scroll: ketika discroll ke atas → ambil lagi
document.addEventListener("DOMContentLoaded", () => {
  let box = document.getElementById("isiPesan");

  box.addEventListener("scroll", function () {
    if (box.scrollTop === 0) {
      loadChatBatch();
    }
  });
});
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
    inputPesanBaru.style.height = inputPesanBaru.scrollHeight + "px";
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
