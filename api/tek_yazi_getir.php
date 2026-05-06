<?php
// 1. JSON formatında yanıt vereceğimizi belirtiyoruz
header('Content-Type: application/json; charset=utf-8');
require_once 'baglanti.php';

// 2. URL'den gelen ID'yi güvenli bir şekilde alıyoruz (GET parametresi)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        // 3. Sadece istenen ID'ye ait yazıyı ve o yazının yazar bilgilerini çekiyoruz
        // LEFT JOIN kullanarak yazar silinmiş olsa bile yazının çökmeden gelmesini sağlıyoruz
        $sorgu = $db->prepare("SELECT posts.*, users.ad_soyad AS yazar_ismi, users.avatar_url AS yazar_avatar 
                               FROM posts 
                               LEFT JOIN users ON posts.yazar_id = users.id 
                               WHERE posts.id = ?");
        $sorgu->execute([$id]);
        
        // Sadece tek bir satır çekeceğimiz için fetchAll yerine fetch kullanıyoruz
        $yazi = $sorgu->fetch(PDO::FETCH_ASSOC);
        
        if ($yazi) {
            echo json_encode($yazi);
        } else {
            // Yazı veritabanında yoksa zarif bir hata mesajı döndür
            echo json_encode(["hata" => "Yazı veritabanında bulunamadı veya silinmiş."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["hata" => "Veritabanı bağlantı hatası: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["hata" => "Geçersiz bir yazı ID'si gönderildi."]);
}
?>