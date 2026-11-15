<?php

require_once "koneksi.php";

wajib_login();

// Tanggal awal default: tanggal 1 bulan ini.
$awal = $_GET["awal"] ?? date("Y-m-01");

// Tanggal akhir default: hari ini.
$akhir = $_GET["akhir"] ?? date("Y-m-d");

$stmt = $conn->prepare(
    "SELECT
        p.*,
        u.nama AS nama_petugas
    FROM pemeriksaan p
    LEFT JOIN users u ON p.petugas_id = u.id
    WHERE p.tanggal_periksa BETWEEN ? AND ?
    ORDER BY
        p.tanggal_periksa DESC,
        p.id DESC"
);

$stmt->bind_param("ss", $awal, $akhir);

$stmt->execute();

$rows = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Laporan | SIDOKKES</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="main-content standalone">

        <header class="topbar">

            <div>
                <p class="eyebrow">
                    REKAP DATA
                </p>

                <h1>Laporan pemeriksaan</h1>
            </div>

            <div class="topbar-right no-print">

                <a
                    class="btn secondary"
                    href="index.php"
                >
                    ← Dashboard
                </a>

                <button
                    class="btn primary"
                    type="button"
                    onclick="window.print()"
                >
                    Cetak laporan
                </button>

            </div>

        </header>

        <section class="content-card">

            <div class="section-heading">

                <div>
                    <p class="eyebrow">
                        SIDOKKES
                    </p>

                    <h2>Rekap pemeriksaan kesehatan</h2>

                    <p class="muted">
                        Periode
                        <?= e(date("d M Y", strtotime($awal))) ?>
                        sampai
                        <?= e(date("d M Y", strtotime($akhir))) ?>
                    </p>
                </div>

            </div>

            <form
                method="get"
                class="search-row no-print"
            >

                <label>
                    Dari tanggal

                    <input
                        type="date"
                        name="awal"
                        value="<?= e($awal) ?>"
                        required
                    >
                </label>

                <label>
                    Sampai tanggal

                    <input
                        type="date"
                        name="akhir"
                        value="<?= e($akhir) ?>"
                        required
                    >
                </label>

                <button
                    class="btn primary"
                    type="submit"
                >
                    Tampilkan
                </button>

            </form>

            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nomor tahanan</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Keluhan</th>
                            <th>Diagnosis</th>
                            <th>Tindak lanjut</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php

                        $n = 1;

                        ?>

                        <?php if ($rows->num_rows === 0): ?>

                            <tr>
                                <td
                                    colspan="8"
                                    class="empty-state"
                                >
                                    Tidak ada catatan pada periode ini.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php while ($r = $rows->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?= $n++ ?>
                                    </td>

                                    <td>
                                        <?= e($r["nomor_tahanan"]) ?>
                                    </td>

                                    <td>
                                        <?= e($r["nama_tahanan"]) ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            date(
                                                "d-m-Y",
                                                strtotime($r["tanggal_periksa"])
                                            )
                                        ) ?>
                                    </td>

                                    <td class="cell-wrap">
                                        <?= e($r["keluhan"]) ?>
                                    </td>

                                    <td class="cell-wrap">
                                        <?= e($r["diagnosis"]) ?>
                                    </td>

                                    <td class="cell-wrap">
                                        <?= e($r["tindak_lanjut"]) ?>
                                    </td>

                                    <td>
                                        <?= e($r["nama_petugas"] ?? "—") ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <div class="table-footer">
                Total catatan dalam periode:
                <?= $n - 1 ?>
            </div>

            <p class="tiny-note">
                Laporan ini adalah prototipe.
                Penggunaan operasional harus mengikuti
                kebijakan instansi dan perlindungan data pribadi.
            </p>
        </section>
    </main>
</body>
</html>

<?php

$stmt->close();

?>
