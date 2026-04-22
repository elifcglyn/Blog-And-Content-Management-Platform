<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../baglanti.php'; // Veritabanı bağlantın

$veri = json_decode(file_get_contents("php://input"), true);
$yazi_id = isset($veri['yazi_id']) ? intval($veri['yazi_id']) : 0;
$islem = isset($veri['islem']) ? $veri['islem'] : '';

if ($yazi_id > 0) {
    // İşleme göre sayıyı 1 artır veya 1 azalt
    if ($islem == 'arttir') {
        $sorgu = $db->prepare("UPDATE yazilar SET begeni_sayisi = begeni_sayisi + 1 WHERE id = ?");
    } else {
        // Eksiye düşmemesi için begeni_sayisi > 0 kontrolü yapıyoruz
        $sorgu = $db->prepare("UPDATE yazilar SET begeni_sayisi = begeni_sayisi - 1 WHERE id = ? AND begeni_sayisi > 0");
    }
    
    $basari = $sorgu->execute([$yazi_id]);
    
    if($basari) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Güncelleme başarısız"]);
    }
} else {
    echo json_encode(["error" => "Geçersiz ID"]);
}
?>