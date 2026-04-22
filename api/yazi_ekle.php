<?php
require_once 'baglanti.php';

// React'tan gelen JSON verisini alıyoruz
$veri = json_decode(file_get_contents("php://input"), true);

if ($veri) {
    try {
        $sorgu = $db->prepare("INSERT INTO posts (yazar_id, kategori_id, baslik, ozet, icerik, kapak_resmi) VALUES (?, ?, ?, ?, ?, ?)");
        
        $sorgu->execute([
            $veri['yazar_id'],
            $veri['kategori_id'],
            $veri['baslik'],
            $veri['ozet'],
            $veri['icerik'],
            $veri['kapak_resmi']
        ]);

        echo json_encode(["mesaj" => "Yazı başarıyla eklendi!"]);
    } catch (PDOException $e) {
        echo json_encode(["hata" => "Ekleme hatası: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["hata" => "Veri gelmedi!"]);
}
?>