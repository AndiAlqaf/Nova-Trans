<?php
// hapus_berita.php – Hapus Berita NOVA TRANS
require_once __DIR__ . '/../koneksi.php';

// 1. Cegah browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Session & Auth
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../masuk.php');
    exit;
}

// Handle penghapusan
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $koneksi->prepare("DELETE FROM berita WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: kelola_berita.php?berhasil_hapus=1");
        exit;
    } else {
        header("Location: kelola_berita.php?error_hapus=1");
        exit;
    }
} else {
    header("Location: kelola_berita.php?error_hapus=1");
    exit;
}
?>