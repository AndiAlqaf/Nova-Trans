<?php
session_start();
if (!isset($_SESSION['journeyInfo'])) {
    header("Location: pesantiket.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Konfirmasi Pemesanan</title></head>
<body>
    <h2>Pemesanan Berhasil</h2>
    <p>Rute: <?= $_SESSION['journeyInfo']['route'] ?></p>
    <p>Tanggal: <?= $_SESSION['journeyInfo']['date'] ?></p>
    <p>Waktu: <?= $_SESSION['journeyInfo']['time'] ?></p>
    <p>Total: Rp <?= number_format($_SESSION['totalAmount'], 0, ',', '.') ?></p>
    <a href="pesantiket.php">Kembali ke Beranda</a>
</body>
</html>