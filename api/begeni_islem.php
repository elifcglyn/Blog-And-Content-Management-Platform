<?php
session_start();
require_once 'baglanti.php';

if (!isset($_SESSION['kullanici_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açılmamış.']);
    exit;
}

$begenen_id = $_SESSION['kullanici_id'];
$begenen_isim = $_SESSION['ad_soyad'] ?? 'Bir kullanıcı';
$yazi_id = $_POST['yazi_id'] ?? null;

if (!$yazi_id) {
    echo json_encode(['status' => 'error', 'message' => 'Yazı ID eksik.']);
    exit;
}

try {
    $sorgu = $db->prepare("SELECT * FROM posts WHERE id = ?"); 
    $sorgu->execute([$yazi_id]);
    $yazi = $sorgu->fetch(PDO::FETCH_ASSOC);

    $yazar_id = $yazi['yazar_id'] ?? $yazi['kullanici_id'] ?? 0;
    $baslik = $yazi['baslik'] ?? 'Bir hikaye';

    $kontrol = $db->prepare("SELECT id FROM likes WHERE kullanici_id = ? AND yazi_id = ?");
    $kontrol->execute([$begenen_id, $yazi_id]);

    if ($kontrol->rowCount() > 0) {
        // Zaten beğenmiş, sadece likes tablosundan siliyoruz (Çöken UPDATE satırı silindi)
        $db->prepare("DELETE FROM likes WHERE kullanici_id = ? AND yazi_id = ?")->execute([$begenen_id, $yazi_id]);
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // İlk defa beğeniyor, likes tablosuna ekliyoruz (Çöken UPDATE satırı silindi)
        $db->prepare("INSERT INTO likes (kullanici_id, yazi_id) VALUES (?, ?)")->execute([$begenen_id, $yazi_id]);

        // Bildirim atma
        if ($begenen_id != $yazar_id && $yazar_id > 0) {
            $mesaj = $begenen_isim . " yazını beğendi"; 
            $detay = '"' . $baslik . '"'; 
            $db->prepare("INSERT INTO notifications (user_id, type, message, detail, is_read) VALUES (?, 'begeni', ?, ?, 0)")
               ->execute([$yazar_id, $mesaj, $detay]);
        }
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Hatası: ' . $e->getMessage()]);
}
?>