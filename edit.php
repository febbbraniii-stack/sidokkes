<?php

require_once "koneksi.php";

wajib_login();

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT * FROM pemeriksaan WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$row) {
    $_SESSION["flash"] = "Catatan tidak ditemukan.";

    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Mengambil data dari form.
    $nomor = trim($_POST["nomor_tahanan"] ?? "");
    $nama = trim($_POST["nama_tahanan"] ?? "");

    $lahir = trim($_POST["tanggal_lahir"] ?? "");
    $tanggal = trim($_POST["tanggal_periksa"] ?? "");

    $jk = $_POST["jenis_kelamin"] ?? "Laki-laki";

    $keluhan = trim($_POST["keluhan"] ?? "");
    $td = trim($_POST["tekanan_darah"] ?? "");
    $suhu = trim($_POST["suhu"] ?? "");
    $berat = trim($_POST["berat_badan"] ?? "");

    $diagnosis = trim($_POST["diagnosis"] ?? "");
    $tindak = trim($_POST["tindak_lanjut"] ?? "");

    if (
        $nomor === "" ||
        $nama === "" ||
        $tanggal === "" ||
        $keluhan === "" ||
        $diagnosis === "" ||
        !in_array(
            $jk,
            ["Laki-laki", "Perempuan", "Lainnya"],
            true
        )
    ) {
        $error = "Lengkapi semua kolom wajib.";
    } else {

        // Kolom kosong akan disimpan sebagai NULL.
        $lahirVal = $lahir === "" ? null : $lahir;

        $suhuVal = $suhu === "" ? null : (float) $suhu;
        $beratVal = $berat === "" ? null : (float) $berat;

        $up = $conn->prepare(
            "UPDATE pemeriksaan SET
                nomor_tahanan = ?,
                nama_tahanan = ?,
                tanggal_lahir = ?,
                tanggal_periksa = ?,
                jenis_kelamin = ?,
                keluhan = ?,
                tekanan_darah = ?,
                suhu = ?,
                berat_badan = ?,
                diagnosis = ?,
                tindak_lanjut = ?
            WHERE id = ?"
        );

        $up->bind_param(
            "sssssssddssi",
            $nomor,
            $nama,
            $lahirVal,
            $tanggal,
            $jk,
            $keluhan,
            $td,
            $suhuVal,
            $beratVal,
            $diagnosis,
            $tindak,
            $id
        );

        if ($up->execute()) {

            $_SESSION["flash"] =
                "Catatan pemeriksaan berhasil diperbarui.";

            $up->close();

            header("Location: index.php");
            exit;

        } else {

            $error = "Perubahan belum berhasil disimpan.";

            $up->close();
        }
    }

    $row = array_merge(
        $row,
        [
            "nomor_tahanan" => $nomor,
            "nama_tahanan" => $nama,
            "tanggal_lahir" => $lahir,
            "tanggal_periksa" => $tanggal,
            "jenis_kelamin" => $jk,
            "keluhan" => $keluhan,
            "tekanan_darah" => $td,
            "suhu" => $suhu,
            "berat_badan" => $berat,
            "diagnosis" => $diagnosis,
            "tindak_lanjut" => $tindak
        ]
    );
}

function val($r, $k)
{
    return e($r[$k] ?? "");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Edit Pemeriksaan | SIDOKKES</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="main-content standalone">

        <header class="topbar">

            <div>
                <p class="eyebrow">
                    DATABASE PEMERIKSAAN
                </p>

                <h1>Edit catatan</h1>
            </div>

            <a class="btn secondary" href="index.php">
                ← Kembali
            </a>

        </header>

        <section class="content-card">

            <div class="section-heading">

                <div>
                    <h2>Perbarui data pemeriksaan</h2>

                    <p class="muted">
                        Pastikan perubahan sesuai catatan pemeriksaan.
                    </p>
                </div>

            </div>

            <?php if ($error): ?>

                <div class="alert error">
                    <?= e($error) ?>
                </div>

            <?php endif; ?>

            <form method="post" class="data-form">

                <div class="form-grid">

                    <label>
                        Nomor tahanan 

                        <input
                            name="nomor_tahanan"
                            required
                            value="<?= val($row, "nomor_tahanan") ?>"
                        >
                    </label>

                    <label>
                        Nama tahanan 

                        <input
                            name="nama_tahanan"
                            required
                            value="<?= val($row, "nama_tahanan") ?>"
                        >
                    </label>

                    <label>
                        Tanggal lahir

                        <input
                            type="date"
                            name="tanggal_lahir"
                            value="<?= val($row, "tanggal_lahir") ?>"
                        >
                    </label>

                    <label>
                        Tanggal pemeriksaan 

                        <input
                            type="date"
                            name="tanggal_periksa"
                            required
                            value="<?= val($row, "tanggal_periksa") ?>"
                        >
                    </label>

                    <label>
                        Jenis kelamin

                        <select name="jenis_kelamin">

                            <?php foreach (
                                ["Laki-laki", "Perempuan", "Lainnya"]
                                as $g
                            ): ?>

                                <option
                                    value="<?= e($g) ?>"
                                    <?= $row["jenis_kelamin"] === $g
                                        ? "selected"
                                        : "" ?>
                                >
                                    <?= e($g) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </label>

                    <label>
                        Tekanan darah

                        <input
                            name="tekanan_darah"
                            value="<?= val($row, "tekanan_darah") ?>"
                        >
                    </label>

                    <label>
                        Suhu (°C)

                        <input
                            type="number"
                            step="0.1"
                            name="suhu"
                            value="<?= val($row, "suhu") ?>"
                        >
                    </label>

                    <label>
                        Berat badan (kg)

                        <input
                            type="number"
                            step="0.1"
                            name="berat_badan"
                            value="<?= val($row, "berat_badan") ?>"
                        >
                    </label>

                    <label class="span-2">
                        Keluhan 

                        <textarea
                            name="keluhan"
                            required
                            rows="3"
                        ><?= val($row, "keluhan") ?></textarea>
                    </label>

                    <label class="span-2">
                        Diagnosis 

                        <textarea
                            name="diagnosis"
                            required
                            rows="3"
                        ><?= val($row, "diagnosis") ?></textarea>
                    </label>

                    <label class="span-2">
                        Tindak lanjut

                        <textarea
                            name="tindak_lanjut"
                            rows="2"
                        ><?= val($row, "tindak_lanjut") ?></textarea>
                    </label>

                </div>

                <div class="form-actions">

                    <a
                        class="btn secondary"
                        href="index.php"
                    >
                        Batal
                    </a>

                    <button
                        class="btn primary"
                        type="submit"
                    >
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>