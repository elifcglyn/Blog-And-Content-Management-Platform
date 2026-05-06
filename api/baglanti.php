<?php
// Veritabanı Kimlik Bilgileri
$host = "localhost";
$dbname = "blog_veritabani"; 
$username = "root"; 
$password = ""; 

try {
    // PDO ile güvenli bağlantı kuruyoruz
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Hata yakalama modunu aktif ediyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // JSON olarak değil, düz metin olarak hatayı bas ki HTML sayfalarını bozmasın
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>