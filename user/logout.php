<?php
// logout.php
session_start();
// Hapus semua data session
$_SESSION = [];
// Hancurkan session di server
session_destroy();
// Redirect ke halaman masuk
header('Location: masuk.php');
exit;
?>