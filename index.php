<?php
require_once "koneksi.php";
wajib_login();

$q = trim($_GET["q"] ?? "");
$filter = "%" . $q . "%";

$stmt = $conn->prepare(
    "SELECT id, nomor_tahanan, nama_tahanan, tanggal_periksa,
            keluhan, diagnosis, tindak_lanjut
     FROM pemeriksaan
     WHERE nomor_tahanan LIKE ? OR nama_tahanan LIKE ?
     ORDER BY tanggal_periksa DESC, id DESC"
);

$stmt->bind_param("ss", $filter, $filter);
$stmt->execute();

$data = $stmt->get_result();

$total = (int) $conn
    ->query("SELECT COUNT(*) AS total FROM pemeriksaan")
    ->fetch_assoc()["total"];

$hariIni = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM pemeriksaan
     WHERE tanggal_periksa = CURDATE()"
);

$hariIni->execute();

$totalHariIni = (int) $hariIni
    ->get_result()
    ->fetch_assoc()["total"];

$bulanIni = (int) $conn
    ->query(
        "SELECT COUNT(*) AS total
         FROM pemeriksaan
         WHERE YEAR(tanggal_periksa) = YEAR(CURDATE())
         AND MONTH(tanggal_periksa) = MONTH(CURDATE())"
    )
    ->fetch_assoc()["total"];

$flash = $_SESSION["flash"] ?? "";
unset($_SESSION["flash"]);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard | SIDOKKES</title>

    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>

<body>
    <div class="app-shell">

        <aside class="sidebar">

            <a class="sidebar-brand" href="index.php">
                <span class="brand-mark small">✚</span>

                <span>
                    <strong>SIDOKKES</strong>
                    <small>POLI KESEHATAN</small>
                </span>
            </a>

            <div class="nav-label">MENU UTAMA</div>

            <a class="nav-item active" href="index.php">
                ▦ <span>Dashboard</span>
            </a>

            <a class="nav-item" href="index.php#form-pemeriksaan">
                ＋ <span>Input Pemeriksaan</span>
            </a>

            <a class="nav-item" href="laporan.php">
                ▤ <span>Laporan</span>
            </a>

            <div class="sidebar-bottom">

                <div class="user-mini">
                    <div class="avatar">
                        <?= e(strtoupper(substr($_SESSION["nama"] ?? "P", 0, 1))) ?>
                    </div>

                    <div>
                        <strong>
                            <?= e($_SESSION["nama"] ?? "Petugas") ?>
                        </strong>

                        <small>Petugas terdaftar</small>
                    </div>
                </div>

                <a class="nav-item logout" href="logout.php">
                    ↪ <span>Keluar</span>
                </a>

            </div>
        </aside>
        <main class="main-content">

            <header class="topbar">

                <div>
                    <p class="eyebrow">SISTEM INFORMASI DOKKES</p>
                    <h1>Dashboard</h1>
                </div>

                <div class="topbar-right">
                    <span class="status-dot"></span>
                    <span>Mode administrasi</span>

                    <div class="avatar">
                        <?= e(strtoupper(substr($_SESSION["nama"] ?? "P", 0, 1))) ?>
                    </div>
                </div>

            </header>

            <!-- NOTIFIKASI -->
            <?php if ($flash): ?>
                <div class="alert success">
                    <?= e($flash) ?>
                </div>
            <?php endif; ?>

            <!-- SAMBUTAN -->
            <section class="welcome-panel">

                <div>
                    <p class="eyebrow light">PANEL PETUGAS</p>

                    <h2>
                        Selamat bertugas,
                        <?= e($_SESSION["nama"] ?? "Petugas") ?>.
                    </h2>

                    <p>
                        Kelola catatan pemeriksaan kesehatan secara terstruktur.
                    </p>
                </div>

                <div class="welcome-symbol">✚</div>

            </section>

            <section class="stats-grid">

                <article class="stat-card">
                    <div class="stat-icon blue">▤</div>

                    <p>Total pemeriksaan</p>
                    <strong><?= $total ?></strong>

                    <small>Seluruh catatan tersimpan</small>
                </article>

                <article class="stat-card">
                    <div class="stat-icon mint">◷</div>

                    <p>Pemeriksaan hari ini</p>
                    <strong><?= $totalHariIni ?></strong>

                    <small>Dicatat untuk hari ini</small>
                </article>

                <article class="stat-card">
                    <div class="stat-icon violet">▦</div>

                    <p>Pemeriksaan bulan ini</p>
                    <strong><?= $bulanIni ?></strong>

                    <small>Bulan berjalan</small>
                </article>

            </section>

            <section class="content-card" id="form-pemeriksaan">

                <div class="section-heading">

                    <div>
                        <p class="eyebrow">FORMULIR DIGITAL</p>

                        <h2>Tambah pemeriksaan kesehatan</h2>

                        <p class="muted">
                            Isi berdasarkan pemeriksaan oleh petugas yang berwenang.
                        </p>
                    </div>

                    <span class="section-number">01</span>

                </div>

                <form
                    action="simpan.php"
                    method="post"
                    class="data-form"
                >

                    <div class="form-grid">

                        <label>
                            Nomor tahanan <span class="required">*</span>

                            <input
                                type="text"
                                name="nomor_tahanan"
                                maxlength="50"
                                required
                                placeholder="Contoh: THN-0001"
                            >
                        </label>

                        <label>
                            Nama tahanan <span class="required">*</span>

                            <input
                                type="text"
                                name="nama_tahanan"
                                maxlength="100"
                                required
                                placeholder="Nama sesuai administrasi"
                            >
                        </label>

                        <label>
                            Tanggal lahir

                            <input
                                type="date"
                                name="tanggal_lahir"
                            >
                        </label>

                        <label>
                            Tanggal pemeriksaan
                            <span class="required">*</span>

                            <input
                                type="date"
                                name="tanggal_periksa"
                                required
                                value="<?= date('Y-m-d') ?>"
                            >
                        </label>

                        <label>
                            Jenis kelamin

                            <select name="jenis_kelamin" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </label>

                        <label>
                            Tekanan darah

                            <input
                                type="text"
                                name="tekanan_darah"
                                maxlength="30"
                                placeholder="Contoh: 120/80 mmHg"
                            >
                        </label>

                        <label>
                            Suhu tubuh (°C)

                            <input
                                type="number"
                                name="suhu"
                                min="25"
                                max="45"
                                step="0.1"
                                placeholder="Contoh: 36.7"
                            >
                        </label>

                        <label>
                            Berat badan (kg)

                            <input
                                type="number"
                                name="berat_badan"
                                min="1"
                                max="500"
                                step="0.1"
                                placeholder="Opsional"
                            >
                        </label>

                        <label class="span-2">
                            Keluhan <span class="required">*</span>

                            <textarea
                                name="keluhan"
                                rows="3"
                                required
                                placeholder="Catat keluhan yang disampaikan..."
                            ></textarea>
                        </label>

                        <label class="span-2">
                            Diagnosis / hasil pemeriksaan
                            <span class="required">*</span>

                            <textarea
                                name="diagnosis"
                                rows="3"
                                required
                                placeholder="Diisi petugas medis sesuai hasil pemeriksaan..."
                            ></textarea>
                        </label>

                        <label class="span-2">
                            Tindak lanjut

                            <textarea
                                name="tindak_lanjut"
                                rows="2"
                                placeholder="Saran, tindakan, atau rujukan bila diperlukan..."
                            ></textarea>
                        </label>

                    </div>

                    <div class="form-actions">

                        <span class="form-hint">
                            <span class="required">*</span> Wajib diisi
                        </span>

                        <button
                            type="reset"
                            class="btn secondary"
                        >
                            Kosongkan
                        </button>

                        <button
                            type="submit"
                            class="btn primary"
                        >
                            Simpan pemeriksaan <span>→</span>
                        </button>

                    </div>

                </form>
            </section>
            <section class="content-card">

                <div class="section-heading">

                    <div>
                        <p class="eyebrow">DATABASE PEMERIKSAAN</p>

                        <h2>Riwayat pemeriksaan</h2>

                        <p class="muted">
                            Cari berdasarkan nama atau nomor tahanan.
                        </p>
                    </div>

                    <a class="btn secondary" href="laporan.php">
                        Lihat laporan ↗
                    </a>

                </div>
                <form class="search-row" method="get">

                    <div class="search-input">
                        ⌕
                        <input
                            type="text"
                            name="q"
                            value="<?= e($q) ?>"
                            placeholder="Cari nama atau nomor tahanan..."
                        >
                    </div>

                    <button type="submit" class="btn primary">
                        Cari
                    </button>

                    <?php if ($q !== ""): ?>
                        <a class="btn secondary" href="index.php">
                            Reset
                        </a>
                    <?php endif; ?>

                </form>

                <div class="table-wrap">

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor / Nama</th>
                                <th>Tanggal</th>
                                <th>Keluhan</th>
                                <th>Diagnosis</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($data->num_rows === 0): ?>

                                <tr>
                                    <td colspan="5" class="empty-state">
                                        Belum ada catatan yang cocok.
                                        Tambahkan pemeriksaan melalui formulir di atas.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php while ($row = $data->fetch_assoc()): ?>

                                    <tr>
                                        <td>
                                            <strong>
                                                <?= e($row["nama_tahanan"]) ?>
                                            </strong>

                                            <small class="table-sub">
                                                <?= e($row["nomor_tahanan"]) ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?= e(date(
                                                "d M Y",
                                                strtotime($row["tanggal_periksa"])
                                            )) ?>
                                        </td>

                                        <td class="cell-wrap">
                                            <?= e($row["keluhan"]) ?>
                                        </td>

                                        <td class="cell-wrap">
                                            <?= e($row["diagnosis"]) ?>
                                        </td>

                                        <td>
                                            <div class="action-group">

                                                <a
                                                    class="action-link"
                                                    href="edit.php?id=<?= (int) $row["id"] ?>"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    class="action-link danger-link"
                                                    href="hapus.php?id=<?= (int) $row["id"] ?>"
                                                    data-confirm="Yakin ingin menghapus catatan ini?"
                                                >
                                                    Hapus
                                                </a>

                                            </div>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php endif; ?>

                        </tbody>
                    </table>

                </div>

                <div class="table-footer">
                    Menampilkan catatan pemeriksaan · jaga kerahasiaan data
                </div>

            </section>
            <footer class="footer">
                SIDOKKES • Prototipe administrasi kesehatan
                · Gunakan sesuai kebijakan dan kewenangan instansi.
            </footer>
        </main>
    </div>
</body>
</html>