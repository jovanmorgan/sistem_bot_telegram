-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Des 2025 pada 03.25
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bot_cnet`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_grup`
--

CREATE TABLE `daftar_grup` (
  `id` int(11) NOT NULL,
  `grup_id` bigint(20) NOT NULL,
  `nama` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `daftar_grup`
--

INSERT INTO `daftar_grup` (`id`, `grup_id`, `nama`) VALUES
(11, -4990657414, 'coba bott');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_user`
--

CREATE TABLE `daftar_user` (
  `id` int(12) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `daftar_user`
--

INSERT INTO `daftar_user` (`id`, `user_id`, `nama`) VALUES
(8, 8273878903, 'VANZ CODE');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id_pengaturan` int(11) NOT NULL,
  `id_pesan_broadcast` int(12) NOT NULL,
  `auto_kirim` enum('on','off') DEFAULT 'off'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_auto`
--

CREATE TABLE `pengaturan_auto` (
  `id` int(11) NOT NULL,
  `group_id` text DEFAULT NULL,
  `pesan_list` text DEFAULT NULL,
  `auto_kirim` enum('on','off') DEFAULT 'off',
  `last_run` timestamp NULL DEFAULT NULL,
  `loop_interval_ms` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_auto`
--

INSERT INTO `pengaturan_auto` (`id`, `group_id`, `pesan_list`, `auto_kirim`, `last_run`, `loop_interval_ms`) VALUES
(221, '[\"-1001491410777\",\"-1001429757173\",\"-1001816190758\",\"-1001835364141\",\"-1001919161355\",\"-1001180605382\",\"-1001169208160\",\"-1001165175195\",\"-1002366572295\",\"-1002421628065\",\"-1002437480364\",\"-1002286256546\",\"-1002364234383\",\"-1002476438344\",\"-1002290082603\",\"-1001468983855\",\"-1001914622045\",\"-1002055329551\",\"-1001165286090\",\"-4990657414\",\"-1002335718812\",\"-1001107652234\"]', '[\"45\",\"44\",\"43\",\"42\",\"41\",\"40\",\"38\"]', 'on', NULL, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan_broadcast`
--

CREATE TABLE `pesan_broadcast` (
  `id` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `waktu_kirim` datetime NOT NULL,
  `status` varchar(20) DEFAULT 'sent',
  `message_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesan_broadcast`
--

INSERT INTO `pesan_broadcast` (`id`, `pesan`, `waktu_kirim`, `status`, `message_id`) VALUES
(38, '‼ SIMPATI DATA TERBAIK PROMO ‼\n==================================\n\n♦ STP3GB   = 27.085  🔥🔥🔥 #Turun\nData Reguler 3 GB 30 Hari\n\n♦ STP8GB   = 63.165   🔥🔥🔥#Turun\nData Reguler 8 GB 30 Hari\n\n♦ STP13GB   = 90.225  🔥🔥🔥#Turun\nData Reguler 13 GB 30 Hari\n\n\n\n‼ SIMPATI DATA TERBAIK ‼\n==========================\n\n♦ STU3GB   = 29.700  \nData Reguler 3 GB 30 Hari\n\n♦ STU8GB   = 69.100   \nData Reguler 8 GB 30 Hari\n\n♦ STU13GB   = 98.650  \nData Reguler 13 GB 30 Hari\n\n♦ STU1H   = 9.985 🆕\nData Reguler 1.5 GB 1 Hari\n\n♦ STU3H   = 24.775 🆕\nData Reguler 3.5 GB 3 Hari\n\n♦ STU30GB   = 118.460 🆕\nData Reguler 30 GB 30 Hari\n\n♦ STU50GB   = 177.635 🆕\nData Reguler 50 GB 30 Hari\n\n♦ STU75GB   = 197.575 🆕\nData Reguler 75 GB 30 Hari\n\n♦ STU100GB   = 246.695 🆕\nData Reguler 100 GB 30 Hari\n\n\n‼ SIMPATI DATA LONG VALIDITY‼\n===============================\n\n♦ DT90G3M   = 221.825 🆕\nKuota 90GB (30GB per bulan selama 3 bulan) + Voice All Operator 15rbu menit + SMS 15rbu 90 Hari\n\n♦ DT180G6M   = 418.825 🆕\nKuota 180GB (30GB per bulan selama 6 bulan) + Voice All Operator 30rbu menit + SMS 30rbu 180 Hari\n\n♦ DT360G12M   = 812.825 🆕\nKuota 360GB (30GB per bulan selama 12 bulan) + Voice All Operator 45rbu menit + SMS 45rbu 360 Hari\n\n\n\n*Stock Aman Gassspoolll\n*Jalur Modchan\n*Speed Detikan Wussss\n*Bukan Barang GIFT\n*Full Kuota Utama dan Berlaku Nasional \n*Tanpa Syarat Semua Nomor Telkomsel Bisa\n*Rekon suspect 2 Jam\n*Cs Stanby 24X7\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:14:49', 'pending', NULL),
(39, '‼ TELKOMSEL DATA REGULER ‼\n==============================\n\n♦ TDR10   = 46.000  ⬆⬆\nData Reguler 10GB 30 Hari\n\n\n*Stock Aman Gassspoolll\n*Speed Detikan Wussss\n*Cs Stanby 24X7\n\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:15:07', 'pending', NULL),
(40, '‼ TELKOMSEL SUPER CUANN ‼\n==============================\n🔴 SP5        =   5.230     🔥\n🔴 SP10      =  10.160   🔥\n🔴 SP20      =  19.847   🔥\n🔴 SP25      =  24.767   🔥\n🔴 SP100    =  97.310   🔥\n\n\n*Stock Aman Gassspoolll\n*Speed Detikan Wussss\n*Stock Modchan\n*Rekon H+0 (Max 2Jam)\n*Cs Stanby 24X7\n\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:15:43', 'pending', NULL),
(41, '‼ TELKOMSEL DATA MINI ‼\n==========================\n\n♦ TVF23   = 10.443  Hot Sale 🆕\nData Reguler 2GB 3 Hari\n\n♦ TVF253   = 11.431  Hot Sale 🆕\nData Reguler 2,5GB 3 Hari\n\n♦ TVF115   = 10.983  Hot Sale 🆕\nData Reguler 1GB 15 Hari\n\n♦ TVF215   = 18.937  Hot Sale 🆕\nData Reguler 2GB 15 Hari\n\n♦ TVF315   = 20.429  Hot Sale 🆕\nData Reguler 3GB 15 Hari\n\n\n===================================\n\n\n♦ TVFF13   = 8.975  Hot Sale \nData Reguler 1 GB 3 Hari\n\n♦ TVF17   = 9.921  Hot Sale🔥\nData Reguler 1 GB 7 Hari\n\n♦ TVF27   = 15.433 Hot Sale🔥\nData Reguler 2 GB 7 Hari\n\n♦ TVFF37   = 15.466 Hot Sale (Update Kode)\nData Reguler 3 GB 7 Hari\n\n♦ TVF57   = 18.750 Hot Sale🔥\nData Reguler 5 GB 7 Hari\n\n♦ TVF25   = 11.250  Hot Sale ⬇\nData Reguler 2,5 GB 5 Hari\n\n♦ TVF35   = 12.250  Hot Sale🔥\nData Reguler 3 GB 5 Hari\n\n♦ TVF77   = 25.400  Hot Sale🔥\nData Reguler 7 GB 7 Hari\n\n\n\n\n*Stock Aman Gassspoolll\n*Jalur Modchan\n*Speed Detikan Wussss\n*Bukan Barang GIFT\n*Full Kuota Utama dan Berlaku Nasional \n*Tanpa Syarat Semua Nomor Telkomsel Bisa\n*Rekon suspect 2 Jam\n*Cs Stanby 24X7\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM RESMI•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:15:53', 'pending', NULL),
(42, '‼ TELKOMSEL DATA FLASH NASIONAL ‼\n===================================\n\n♦ BULK500MB = 6.500     Hot Sale🔥\nTelkomsel Data Flash 500MB 30 Hari\n\n♦ BULK1GB  =  11.350    Hot Sale🔥\nTelkomsel Data Flash 1GB 30 Hari\n\n♦ BULK2GB  =  22.000    Hot Sale🔥\nTelkomsel Data Flash 30 Hari\n  \n♦ DTFN3      =  32.975  Hot Sale🔥\nTelkomsel Data Flash 3GB 30 Hari\n\n\n‼ TELKOMSEL DATA FLASH NON-PUMA ‼\n===================================\n\n♦ DTFN4      =  39.500  Hot Sale🔥\nTelkomsel Data Flash 4GB 30 Hari\n\n♦ DTFN5      =  40 450  Hot Sale🔥\nTelkomsel Data Flash 5GB 30 Hari\n\n♦ DTFN8      =  50.550  Hot Sale🔥\nTelkomsel Data Flash 8GB 30 Hari\n\n♦ DTFN14      =  95.450  Hot Sale🔥\nTelkomsel Data Flash 14GB 30 Hari\n\n♦ DTFN17     =  50.875  Hot Sale🔥\nTelkomsel Data Flash 17GB 30 Hari \n\n♦ DTFN15      =   50.725 Hot Sale🆕\nTelkomsel Data Flash 15GB 30 Hari \n\n♦ DTFN20      =   76.537 Hot Sale🆕\nTelkomsel Data Flash 20GB 30 Hari \n\n♦ DTFN25      =   86.530 Hot Sale🆕\nTelkomsel Data Flash 25GB 30 Hari \n\n♦ DTFN30      =   91.515 Hot Sale🆕\nTelkomsel Data Flash 30GB 30 Hari \n\n♦ DTFN35      =   96.400 Hot Sale🆕\nTelkomsel Data Flash 35GB 30 Hari \n\n♦ DTFN40      =   101.417 Hot Sale🆕\nTelkomsel Data Flash 40GB 30 Hari \n\n♦ DTFN45      =   106.410 Hot Sale🆕\nTelkomsel Data Flash 45GB 30 Hari\n\n♦ DTFN50      =   111.400 Hot Sale🆕\nTelkomsel Data Flash 50GB 30 Hari\n\n♦ DTFN60      =   121.395 Hot Sale🆕\nTelkomsel Data Flash 60GB 30 Hari\n\n♦ DTFN70      =   141.389 Hot Sale🆕\nTelkomsel Data Flash 70GB 30 Hari\n\n♦ DTFN80      =   161.383 Hot Sale🆕\nTelkomsel Data Flash 80GB 30 Hari\n\n♦ DTFN90      =   176.377 Hot Sale🆕\nTelkomsel Data Flash 90GB 30 Hari\n\n♦ DTFN100    =   191.353 Hot Sale🆕\nTelkomsel Data Flash 100GB 30 Hari\n\n\n*Stock Sendiri Aman Gassspoolll\n*Speed Detikan Wussss\n*Full Kuota Utama \n*Rekon suspect 2 Jam Khusus Kode DTFN\n*Cs Stanby 24X7\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM RESMI•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:19:35', 'pending', NULL),
(43, '‼ TELKOMSEL NELPON FIX NASIONAL ‼\n=====================================\n\n♦ NF5   = 4.830  Hot Sale🔥\nPaket telpon Fix 10Mnt All Opr + 55Mnt Sesama 1 Hari\n\n♦ NF10 = 10.350 Hot Sale🔥\nPaket telpon Fix 20Mnt All Opr + 120Mnt Sesama 3 Hari\n\n♦ NF20 = 20.400 Hot Sale🔥\nPaket telpon Fix 50Mnt All Opr + 225Mnt Sesama 7 Hari\n\n♦ NF25 = 23.500 Hot Sale🔥\nPaket telpon Fix 50Mnt All Opr + 270Mnt Sesama 7 Hari\n\n♦ NF50 = 54.150 Hot Sale🔥\nPaket telpon Fix 100Mnt All Opr + 640Mnt Sesama 30 Hari\n\n♦ NF100 = 95.000 Hot Sale🔥\nPaket telpon Fix 200Mnt All Opr + 1100Mnt Sesama 30 Hari\n\n\n‼ TELKOMSEL NELPON BULK NASIONAL ‼\n=======================================\n\n♦ TPB10 = 9.975    \nPaket Telpon Semua Operator 5Mnt All Opr + 325Mnt Sesama 1 Hari\n\n♦ TPB20 = 19.850   \nPaket Telpon Semua Operator 25Mnt All Opr + 635Mnt Sesama 3 Hari\n\n♦ TPB25 = 24.775   \nPaket Telpon Semua Operator 50Mnt All Opr + 830Mnt Sesama 7 Hari\n\n♦ TPB41 = 39.600   \nPaket Telpon Sesama Telkomsel 3600Mnt Sesama 7 Hari\n\n♦ TPB85 = 83.875   \nPaket Telpon Semua Operator 100Mnt All Opr + 3500Mnt Sesama 30 Hari\n\n♦ TPB105 = 103.575  \nPaket Telpon Sesama Telkomsel 10.000Mnt Sesama 30 Hari\n\n♦ TPB135 = 133.600  \nPaket Telpon Semua Operator 60Mnt All Opr + 9.700Mnt Sesama 30 Hari \n\n♦TPB60 = 10.025 🆕\nPaket Telpon Semua Operator 600Mnt All Opr 1 Hari \n\n♦TPB100 = 19.875 🆕\nPaket Telpon Semua Operator 100Mnt All Opr 3 Hari \n\n♦TPB200 = 24.800 🆕\nPaket Telpon Semua Operator 200Mnt All Opr 7 Hari \n\n♦TPB1500 = 83.900 🆕\nPaket Telpon Semua Operator 1500Mnt All Opr 30 Hari \n\n\n\n\n⚠Warning⚠\nRincian kuota yg tercantumkan sesuai perolehan dari telkomsel adapun beberapa zona bagian timur indonesia jumlah menit  lebih sedikit. Silahkan call ke 188 karena kami tidak bisa menambah atau mengurangi jumlah menit.\n\n\n*Jalur NGRS & Modchan\n*Stock Aman Gassspoolll\n*Speed Detikan Wussss\n*Kuota Berlaku Nasional\n*Cs Stanby 24X7\n\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM RESMI•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:19:45', 'pending', NULL),
(44, '🎉 OPEN NEW PRODUK 🎉\n\n‼ ESIM GLOBAL ‼\n==================\n\n- Negara Coverage -\n\nINDONESIA\nSINGAPORE\nCHINA\nJAPAN\nMALAYSIA\nTHAILAND\nVIETNAM\nHONG KONG\nMACAU\nPHILIPPINES\nTURKEY\n\nPRICELIST & KODE PRODUK KLIK DISINI (https://docs.google.com/spreadsheets/d/1nsWBBL6zyUjClBNYXP1N2pC6OSrZlE-l/edit?usp=sharing&ouid=107336322665983779872&rtpof=true&sd=true) ‼\n\nNote :\n- Stock Sendiri\n- Valid Aman Lancar\n- Speed Detikan Wussss\n- SN= Customer Check ID/Customer Phone\n- Nomer Tujuan Di Isi Nomer HP\n\nCara check QR :\n1. Masuk ke https://centrum.esim.co.id/check\n2. Masukan Customer Check ID dan Customer Phone yang di dapat dari SN .\n3. Silahkan Scan QR atau input Manual Code.\n4. Silahkan ikuti langkah selanjutnya untuk aktivasi esim anda.\n\nFitur :\n- SIM Card Langsung Aktif & Langsung Connect.\n- SIM Card Khusus Internet (Tanpa nomor telepon).\n- Bisa untuk Tethering/Hotspot.\n- Tidak Perlu Registrasi KTP atau Paspor.\n- Bisa digunakan untuk aplikasi seperti WhatsApp, Instagram, Facebook, dan lainnya.\n- Bisa digunakan untuk Browsing.\n- Bisa diisi ulang kapan saja.\n\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:19:56', 'pending', NULL),
(45, '‼ TELKOMSEL MASA AKTIF ‼\n============================\n\n♦ TMA5  =  2.875\nTELKOMSEL MASA AKTIF 5 HARI\n\n♦ TMA10 =  4.925\nTELKOMSEL MASA AKTIF 10 HARI\n\n♦ TMA15 =  6.275\nTELKOMSEL MASA AKTIF 15 HARI\n\n♦ TMA30 =  13.985\nTELKOMSEL MASA AKTIF 30 HARI\n\n♦ TMA90 =  32.755\nTELKOMSEL MASA AKTIF 90 HARI\n\n♦ TMA180 =  61.955\nTELKOMSEL MASA AKTIF 180 HARI\n\n♦ TMA360 =  113.875\nTELKOMSEL MASA AKTIF 360 HARI\n\n\n⚠Warning⚠\nPengisian Masa Aktif Harus Lebih Panjang Dari Sisa Masa Aktif Kartu, Jika Pengisian Masa Aktif Lebih Pendek di Banding Sisa Masa Aktif Kartu, Maka Tidak Akan Bertambah Setelah di Lakukan Penambahan Masa Aktif.\n\n\n*Jalur NGRS & Modchan\n*Stock Aman Gassspoolll\n*Speed Detikan Wussss\n*Nomor Hangus / Kartu Tidak Aktif = GAGAL\n*Cs Stanby 24X7\n\n\n==== CUSTOMER SERVICE ====\n\n•TELEGRAM RESMI•\n@Cs_Centrumnet\n\n•WEBREPORT•\nhttp://119.13.100.58:8088/\n\n•IRS MARKET MEMBER•\nhttps://member.irsmarket.com/supplier/1024\n\n•DIGIFLAZZ MEMBER•\nhttps://digiflazz.com/seller/oJzzBo', '2025-12-09 02:20:07', 'pending', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan_masuk`
--

CREATE TABLE `pesan_masuk` (
  `id` int(11) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `chat_type` enum('group','private') NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `pesan` text NOT NULL,
  `waktu` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `daftar_grup`
--
ALTER TABLE `daftar_grup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_grup` (`grup_id`);

--
-- Indeks untuk tabel `daftar_user`
--
ALTER TABLE `daftar_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`);

--
-- Indeks untuk tabel `pengaturan_auto`
--
ALTER TABLE `pengaturan_auto`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesan_broadcast`
--
ALTER TABLE `pesan_broadcast`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesan_masuk`
--
ALTER TABLE `pesan_masuk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `daftar_grup`
--
ALTER TABLE `daftar_grup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `daftar_user`
--
ALTER TABLE `daftar_user`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_auto`
--
ALTER TABLE `pengaturan_auto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT untuk tabel `pesan_broadcast`
--
ALTER TABLE `pesan_broadcast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `pesan_masuk`
--
ALTER TABLE `pesan_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
