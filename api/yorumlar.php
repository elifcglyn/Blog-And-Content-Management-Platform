<?php
require_once 'baglanti.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Belirli bir yazıya ait yorumları getir
    $yazi_id = $_GET['yazi_id'];
    $sorgu = $db->prepare("SELECT comments.*, users.ad_soyad, users.avatar_url FROM comments 
                           LEFT JOIN users ON comments.kullanici_id = users.id 
                           WHERE yazi_id = ? ORDER BY tarih DESC");
    $sorgu->execute([$yazi_id]);
    echo json_encode($sorgu->fetchAll(PDO::FETCH_ASSOC));
} 
else if ($method == 'POST') {
    // Yeni yorum ekle
    $veri = json_decode(file_get_contents("php://input"), true);
    $sorgu = $db->prepare("INSERT INTO comments (yazi_id, kullanici_id, icerik) VALUES (?, ?, ?)");
    $sorgu->execute([$veri['yazi_id'], $veri['kullanici_id'], $veri['icerik']]);
    echo json_encode(["mesaj" => "Yorum eklendi!"]);
}
?>