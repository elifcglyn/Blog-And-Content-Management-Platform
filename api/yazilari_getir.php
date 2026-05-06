<?php
// 1. JSON formatında yanıt vereceğimizi ve Türkçe karakter sorununu (UTF-8) çözeceğimizi belirtiyoruz.
header('Content-Type: application/json; charset=utf-8');
require_once 'baglanti.php';

try {
    // 2. Güvenli SQL Sorgusu (LEFT JOIN kullanarak boş olanları da çöktürmeden getiririz)
    $sorgu = $db->prepare("
        SELECT 
            posts.*, 
            users.ad_soyad AS yazar_adi, 
            users.avatar_url AS yazar_avatar
        FROM posts 
        LEFT JOIN users ON posts.yazar_id = users.id 
        ORDER BY posts.id DESC
    ");
    $sorgu->execute();
    
    // 3. Verileri dizi (Array) olarak çek
    $yazilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    // 4. NULL (boş) gelen verileri temizle ve varsayılan değerler ata (Sistemin çökmesini engeller)
    foreach ($yazilar as $key => $yazi) {
        // Eğer yazar silinmişse veya adı yoksa
        if (empty($yazi['yazar_adi'])) {
            $yazilar[$key]['yazar_adi'] = "Bilinmeyen Yazar";
        }
        
        // Eğer yazarın resmi yoksa, UI-Avatars API'sinden isminin baş harfiyle resim oluştur
        if (empty($yazi['yazar_avatar'])) {
            $isim_url = urlencode($yazilar[$key]['yazar_adi']);
            $yazilar[$key]['yazar_avatar'] = "https://ui-avatars.com/api/?name={$isim_url}&background=0d9488&color=fff";
        }

        // Eğer yazının tarihi yoksa şu anki tarihi ata
        if (empty($yazi['yayin_tarihi']) && empty($yazi['olusturulma_tarihi'])) {
            $yazilar[$key]['yayin_tarihi'] = date('Y-m-d H:i:s');
        }
    }

    // 5. Tertemiz JSON verisini Frontend'e (JavaScript'e) gönder
    echo json_encode($yazilar);

} catch (PDOException $e) {
    // 6. Eğer SQL'de bir hata olursa, sayfayı beyaz ekranda bırakmak yerine JSON olarak hata gönder
    echo json_encode([
        "hata" => "Veri çekilirken bir sorun oluştu.",
        "detay" => $e->getMessage() // (Geliştirme aşamasında hatayı görmek için)
    ]);
}
?>