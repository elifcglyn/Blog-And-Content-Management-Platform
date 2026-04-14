<?php
require_once 'baglanti.php';

// Tarayıcıya JSON döneceğimizi ve dışarıdan POST kabul ettiğimizi söylüyoruz
header('Content-Type: application/json');

// React'tan gelen JSON verisini al ve PHP'nin anlayacağı hale (Array) çevir
$veri = json_decode(file_get_contents("php://input"), true);

// Eğer ID gönderilmişse işlemi başlat
if (isset($veri['id'])) {
    $id = $veri['id'];
    $ad_soyad = isset($veri['ad_soyad']) ? $veri['ad_soyad'] : '';
    $bio = isset($veri['bio']) ? $veri['bio'] : '';

    // MySQL'e "Bu ID'li kullanıcının adını ve biyografisini değiştir" emrini ver
    $sorgu = $db->prepare("UPDATE users SET ad_soyad = ?, bio = ? WHERE id = ?");
    $basari = $sorgu->execute([$ad_soyad, $bio, $id]);

    if ($basari) {
        echo json_encode(["success" => true, "mesaj" => "Profil başarıyla güncellendi!"]);
    } else {
        echo json_encode(["success" => false, "error" => "Veritabanı güncellenemedi."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "ID bilgisi eksik."]);
}
?>