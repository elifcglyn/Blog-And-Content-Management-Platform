<?php
session_start();
require_once 'baglanti.php'; // Veritabanı bağlantımız

// GÜVENLİK: Kullanıcı giriş yapmış mı?
if (!isset($_SESSION['kullanici_id'])) {
    // Giriş yapmamışsa ana sayfaya (veya login'e) geri gönder
    header("Location: ../index.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];

try {
    // 1. GİRİŞ YAPAN KULLANICININ TÜM BİLDİRİMLERİNİ BUL VE is_read DEĞERİNİ 1 (OKUNDU) YAP
    $guncelle = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $guncelle->execute([$kullanici_id]);

    // 2. İŞLEM BİTİNCE KULLANICIYI GELDİĞİ SAYFAYA (BİLDİRİMLER) GERİ GÖNDER
    header("Location: ../bildirimler.php");
    exit;

} catch (PDOException $e) {
    // Herhangi bir SQL hatası olursa ekrana bas (Çökme koruması)
    die("Veritabanı hatası: " . $e->getMessage());
}
?>