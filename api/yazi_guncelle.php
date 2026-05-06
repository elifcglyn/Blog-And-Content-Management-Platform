<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'baglanti.php';

$json = file_get_contents("php://input");
$veri = json_decode($json, true);

if (isset($veri['id'])) {
    try {
        $db->beginTransaction();

        // 1. Ana yazıyı güncelle
        $sql1 = "UPDATE posts SET baslik = ?, icerik = ? WHERE id = ?";
        $db->prepare($sql1)->execute([$veri['baslik'], $veri['icerik'], $veri['id']]);

        // 2. Kaçıncı sürüm olduğunu bul (v1.1, v1.2 yapmak için)
        $saySorgu = $db->prepare("SELECT COUNT(*) FROM post_versions WHERE yazi_id = ?");
        $saySorgu->execute([$veri['id']]);
        $kacTane = $saySorgu->fetchColumn() + 1;
        $surumNo = "v1." . $kacTane;
        
        // Frontend'den gelen commit mesajını al, yoksa varsayılan mesaj koy
        $not = isset($veri['degisiklik_notu']) && !empty($veri['degisiklik_notu']) ? $veri['degisiklik_notu'] : "İçerik güncellendi";

        // 3. Eski sürümlerin "guncel_mi" durumunu sıfırla (0 yap)
        $db->prepare("UPDATE post_versions SET guncel_mi = 0 WHERE yazi_id = ?")->execute([$veri['id']]);

        // 4. Yeni sürümü "Commit"le
        $sql2 = "INSERT INTO post_versions (yazi_id, surum_numarasi, icerik, degisiklik_notu, guncel_mi) VALUES (?, ?, ?, ?, ?)";
        $db->prepare($sql2)->execute([$veri['id'], $surumNo, $veri['icerik'], $not, 1]);

        $db->commit();
        echo json_encode(["success" => true, "message" => "Güncellendi ve yeni sürüm eklendi."]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(["success" => false, "error" => "Veritabanı hatası: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Veri gelmedi."]);
}
?>