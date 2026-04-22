<?php
// Dışarıdan (React'tan) gelecek isteklere izin vermek için gerekli güvenlik ayarları (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Veritabanı Kimlik Bilgileri
$host = "localhost";
$dbname = "blog_veritabani"; // Demin phpMyAdmin'de açtığımız isim
$username = "root"; // XAMPP'ın varsayılan patron kullanıcısı
$password = ""; // XAMPP'ta şifre varsayılan olarak boştur

try {
    // PDO (PHP Data Objects) ile güvenli bağlantı kuruyoruz
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Hata yakalama modunu aktif ediyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // TEST İÇİN: Bağlantı başarılıysa ekrana yazdır (Sonra sileceğiz)
    //echo json_encode(["mesaj" => "Harika! Veritabanina basariyla baglandin Emirhan."]);

} catch (PDOException $e) {
    // Bağlantıda sorun varsa hatayı ekrana bas
    echo json_encode(["hata" => "Baglanti basarisiz: " . $e->getMessage()]);
    exit;
}
?>