<?php
require_once "koneksi.php";
if (!empty($_SESSION["user_id"])) { header("Location: index.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    if ($nama === "" || $username === "" || strlen($password) < 8) {
        $error = "Lengkapi semua kolom. Kata sandi minimal 8 karakter.";
    } else {
        $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $ada = $cek->get_result()->num_rows > 0;
        $cek->close();
        if ($ada) {
            $error = "Username sudah digunakan. Pilih username lain.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nama, $username, $hash);
            if ($stmt->execute()) {
                header("Location: login.php?daftar=berhasil"); exit;
            }
            $error = "Pendaftaran belum berhasil. Coba lagi.";
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Daftar Petugas | SIDOKKES</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-page"><main class="auth-card"><div class="brand-mark">✚</div><p class="eyebrow">SIDOKKES • ADMINISTRASI KESEHATAN</p><h1>Buat akun petugas</h1><p class="muted">Daftarkan akun untuk masuk ke sistem.</p>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="stack-form">
<label>Nama lengkap<input name="nama" required maxlength="100" value="<?= e($_POST["nama"] ?? "") ?>"></label>
<label>Username<input name="username" required maxlength="50" autocomplete="username" value="<?= e($_POST["username"] ?? "") ?>"></label>
<label>Kata sandi<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>Minimal 8 karakter.</small></label>
<button class="btn primary full" type="submit">Buat akun</button>
</form><p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></p></main></body></html>
