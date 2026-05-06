<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'baglanti.php';

$json = file_get_contents("php://input");
$veri = json_decode($json, true);

if ($veri) {
    try {
        $db->beginTransaction();

        // 1. Yazıyı ana tabloya ekle
        $sorgu = $db->prepare("INSERT INTO posts (yazar_id, kategori_id, baslik, ozet, icerik, kapak_resmi, okunma_suresi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $sorgu->execute([
            $veri['yazar_id'], $veri['kategori_id'], $veri['baslik'], 
            $veri['ozet'], $veri['icerik'], $veri['kapak_resmi'], $veri['okunma_suresi']
        ]);
        
        $yeni_yazi_id = $db->lastInsertId(); // Eklenen yazının ID'sini al
        $not = $veri['degisiklik_notu'] ?? 'İlk yayın';

        // 2. İlk "Commit"i at (GitHub mantığı başlangıcı)
        $ilkSurum = $db->prepare("INSERT INTO post_versions (yazi_id, surum_numarasi, icerik, degisiklik_notu, guncel_mi) VALUES (?, 'v1.0', ?, ?, 1)");
        $ilkSurum->execute([$yeni_yazi_id, $veri['icerik'], $not]);

        $db->commit();
        echo json_encode(["status" => "success", "mesaj" => "Yazı başarıyla yayımlandı ve v1.0 oluşturuldu!"]);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(["hata" => "Ekleme hatası: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["hata" => "Veri gelmedi!"]);
}
?>