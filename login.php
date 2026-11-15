<?php
require_once "koneksi.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, nama, username, password
         FROM users
         WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = (int) $user["id"];
        $_SESSION["nama"] = $user["nama"];
        $_SESSION["username"] = $user["username"];

        header("Location: index.php");
        exit;

    } else {

        $error = "Username atau kata sandi tidak sesuai.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Masuk | SIDOKKES</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page">

    <main class="auth-card">

        <div class="brand-mark">✚</div>

        <p class="eyebrow">SISTEM INFORMASI DOKKES</p>

        <h1>Selamat datang</h1>

        <p class="muted">
            Masuk menggunakan akun petugas.
        </p>

        <?php if (isset($_GET["daftar"])): ?>
            <div class="alert success">
                Akun berhasil dibuat. Silakan masuk.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="stack-form">

            <label>
                Username

                <input
                    type="text"
                    name="username"
                    required
                    autocomplete="username"
                    value="<?= e($_POST["username"] ?? "") ?>"
                >
            </label>

            <label>
                Kata sandi

                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </label>

            <button
                class="btn primary full"
                type="submit"
            >
                Masuk ke dashboard
            </button>

        </form>

        <p class="auth-switch">
            Belum punya akun?
            <a href="register.php">Daftar petugas</a>
        </p>

        <p class="tiny-note">
            Gunakan akun petugas yang terdaftar.
            Jangan bagikan kata sandi.
        </p>

    </main>

</body>
</html>
