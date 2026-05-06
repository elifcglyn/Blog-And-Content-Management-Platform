<?php
// Oturumu başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eğer oturumda bir kullanıcı ID'si yoksa (yani giriş yapmamışsa)
if (!isset($_SESSION['kullanici_id'])) {
    // Onu login sayfasına şutla!
    header("Location: login.php");
    exit();
}
?>