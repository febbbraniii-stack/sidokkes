<?php
require_once "koneksi.php";

wajib_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $id = filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );

    if (!$id) {
        header("Location: index.php");
        exit;
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

        <title>Konfirmasi Hapus | SIDOKKES</title>

        <link rel="stylesheet" href="style.css">
    </head>

    <body class="auth-page">

        <main class="auth-card">

            <div class="brand-mark">!</div>

            <h1>Hapus catatan?</h1>

            <p class="muted">
                Tindakan ini akan menghapus catatan pemeriksaan
                yang dipilih. Pastikan ini memang diperlukan.
            </p>

            <form method="post" class="form-actions">

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $id ?>"
                >

                <a
                    class="btn secondary"
                    href="index.php"
                >
                    Batal
                </a>

                <button
                    class="btn danger"
                    type="submit"
                >
                    Ya, hapus
                </button>
            </form>
        </main>
    </body>
    </html>

    <?php

    exit;
}

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if ($id) {

    $stmt = $conn->prepare(
        "DELETE FROM pemeriksaan WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();

    $_SESSION["flash"] =
        "Catatan pemeriksaan telah dihapus jika data tersebut tersedia.";
}

header("Location: index.php");
exit;

?>