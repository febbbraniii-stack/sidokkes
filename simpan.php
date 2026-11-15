<?php

require_once "koneksi.php";

wajib_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$nomor = trim($_POST["nomor_tahanan"] ?? "");
$nama = trim($_POST["nama_tahanan"] ?? "");

$tanggalLahir = trim($_POST["tanggal_lahir"] ?? "");
$tanggalPeriksa = trim($_POST["tanggal_periksa"] ?? "");

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
    $tanggalPeriksa === "" ||
    $keluhan === "" ||
    $diagnosis === "" ||
    !in_array($jk, ["Laki-laki", "Perempuan", "Lainnya"], true)
) {
    $_SESSION["flash"] = "Data wajib belum lengkap. Silakan periksa kembali.";

    header("Location: index.php#form-pemeriksaan");
    exit;
}

// Jika tanggal lahir kosong, simpan sebagai NULL.
$lahir = $tanggalLahir === "" ? null : $tanggalLahir;

// Jika suhu atau berat badan kosong, gunakan NULL.
$suhuVal = $suhu === "" ? null : (float) $suhu;
$beratVal = $berat === "" ? null : (float) $berat;

// Mengambil ID petugas yang sedang login.
$petugas = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "INSERT INTO pemeriksaan (
        nomor_tahanan,
        nama_tahanan,
        tanggal_lahir,
        tanggal_periksa,
        jenis_kelamin,
        keluhan,
        tekanan_darah,
        suhu,
        berat_badan,
        diagnosis,
        tindak_lanjut,
        petugas_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssssssdsssi",
    $nomor,
    $nama,
    $lahir,
    $tanggalPeriksa,
    $jk,
    $keluhan,
    $td,
    $suhuVal,
    $beratVal,
    $diagnosis,
    $tindak,
    $petugas
);

if ($stmt->execute()) {
    $_SESSION["flash"] = "Catatan pemeriksaan berhasil disimpan.";
} else {
    $_SESSION["flash"] = "Catatan belum berhasil disimpan. Periksa data lalu coba lagi.";
}

$stmt->close();

header("Location: index.php");
exit;

?>
