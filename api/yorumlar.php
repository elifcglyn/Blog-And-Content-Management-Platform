<?php
session_start();
require_once 'baglanti.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $yazi_id = $_GET['yazi_id'] ?? 0;
    $sorgu = $db->prepare("SELECT comments.*, users.ad_soyad, users.avatar_url FROM comments 
                           LEFT JOIN users ON comments.kullanici_id = users.id 
                           WHERE yazi_id = ? ORDER BY tarih DESC");
    $sorgu->execute([$yazi_id]);
    echo json_encode($sorgu->fetchAll(PDO::FETCH_ASSOC));
} 
else if ($method == 'POST') {
    if (!isset($_SESSION['kullanici_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Oturum açılmamış.']);
        exit;
    }

    $yorum_yapan_id = $_SESSION['kullanici_id'];
    $yorum_yapan_isim = $_SESSION['ad_soyad'] ?? 'Bir kullanıcı';

    $veri = json_decode(file_get_contents("php://input"), true);
    $yazi_id = $veri['yazi_id'] ?? $_POST['yazi_id'] ?? null;
    $icerik = $veri['icerik'] ?? $_POST['yorum'] ?? null;

    if (!$yazi_id || !$icerik) {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri.']);
        exit;
    }

    try {
        // Yorumu tabloya ekle
        $ekle = $db->prepare("INSERT INTO comments (yazi_id, kullanici_id, icerik) VALUES (?, ?, ?)");
        $ekle->execute([$yazi_id, $yorum_yapan_id, $icerik]);
        // (Çöken UPDATE satırı buradan silindi)

        $sorgu = $db->prepare("SELECT * FROM posts WHERE id = ?");
        $sorgu->execute([$yazi_id]);
        $yazi = $sorgu->fetch(PDO::FETCH_ASSOC);

        $yazar_id = $yazi['yazar_id'] ?? $yazi['kullanici_id'] ?? 0;
        $baslik = $yazi['baslik'] ?? 'Bir hikaye';

        if ($yorum_yapan_id != $yazar_id && $yazar_id > 0) {
            $mesaj = $yorum_yapan_isim . " hikayene yorum yaptı";
            $detay = '"' . $baslik . '"';
            $db->prepare("INSERT INTO notifications (user_id, type, message, detail, is_read) VALUES (?, 'yorum', ?, ?, 0)")
               ->execute([$yazar_id, $mesaj, $detay]);
        }

        echo json_encode(['status' => 'success', 'message' => 'Yorum eklendi!']);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'SQL Hatası: ' . $e->getMessage()]);
    }
}
?>