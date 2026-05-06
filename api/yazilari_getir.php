<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

require_once 'baglanti.php';

try {
    // Eğer URL'den bir yazar_id gelmişse (Örn: yazilari_getir.php?yazar_id=2)
    $yazar_id = isset($_GET['yazar_id']) ? $_GET['yazar_id'] : null;

    if ($yazar_id) {
        // Sadece o yazara ait olanları getir HEM DE yazarın isim ve resmini al (JOIN)
        $sql = "SELECT posts.*, users.ad_soyad as yazar_adi, users.avatar_url as yazar_avatar 
                FROM posts 
                LEFT JOIN users ON posts.yazar_id = users.id 
                WHERE posts.yazar_id = ? 
                ORDER BY posts.yayin_tarihi DESC";
        $sorgu = $db->prepare($sql);
        $sorgu->execute([$yazar_id]);
    } else {
        // Her şeyi getir (Keşfet sayfası gibi yerler için) HEM DE yazarın isim ve resmini al (JOIN)
        $sql = "SELECT posts.*, users.ad_soyad as yazar_adi, users.avatar_url as yazar_avatar 
                FROM posts 
                LEFT JOIN users ON posts.yazar_id = users.id 
                ORDER BY posts.yayin_tarihi DESC";
        $sorgu = $db->prepare($sql);
        $sorgu->execute();
    }
    
    $yazilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($yazilar);

} catch (PDOException $e) {
    echo json_encode(["hata" => "Yazılar getirilirken bir sorun oluştu: " . $e->getMessage()]);
}
?>