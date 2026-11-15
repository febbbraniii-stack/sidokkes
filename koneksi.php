<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$user = "root";
$password = "";
$database = "sidokkes_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi database gagal. Pastikan MySQL aktif dan database sidokkes_db sudah diimpor.");
}
$conn->set_charset("utf8mb4");

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

function wajib_login() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}
?>
