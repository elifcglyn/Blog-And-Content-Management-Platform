<?php
session_start();
require_once 'baglanti.php';
header("Content-Type: application/json; charset=utf-8");

$json = file_get_contents("php://input");
$veri = json_decode($json, true);

if ($veri && isset($_SESSION['kullanici_id'])) {
    try {
        // BURASI ÇOK ÖNEMLİ: JavaScript'ten gelen yazar_id'yi sildik.
        // Doğrudan o an giriş yapmış olan Elif'in ID'sini kullanıyoruz.
        $aktif_yazar_id = $_SESSION['kullanici_id'];
        
        $sorgu = $db->prepare("INSERT INTO posts (yazar_id, kategori_id, baslik, ozet, icerik, kapak_resmi) VALUES (?, ?, ?, ?, ?, ?)");
        
        $sorgu->execute([
            $aktif_yazar_id, 
            $veri['kategori_id'] ?? 1,
            $veri['baslik'],
            $veri['ozet'] ?? '',
            $veri['icerik'],
            $veri['kapak_resmi'] ?? null
        ]);

        echo json_encode(["mesaj" => "Yazı başarıyla senin hesabına (ID: $aktif_yazar_id) eklendi!"]);
    } catch (PDOException $e) {
        echo json_encode(["hata" => "Veritabanı Hatası: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["hata" => "Oturum bulunamadı veya veri gelmedi!"]);
}