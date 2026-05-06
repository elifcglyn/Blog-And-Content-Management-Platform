<?php
session_start();
require_once 'baglanti.php';

if (!isset($_SESSION['kullanici_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açılmamış.']);
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$kullanici_isim = $_SESSION['ad_soyad'] ?? 'Bir kullanıcı';
$yazi_id = $_POST['yazi_id'] ?? null;

if (!$yazi_id) {
    echo json_encode(['status' => 'error', 'message' => 'Yazı ID eksik.']);
    exit;
}

try {
    // Yazı yazarını bul (Bildirim atabilmek için)
    $postSorgu = $db->prepare("SELECT yazar_id, baslik FROM posts WHERE id = ?");
    $postSorgu->execute([$yazi_id]);
    $yazi = $postSorgu->fetch(PDO::FETCH_ASSOC);
    
    $yazar_id = $yazi['yazar_id'] ?? 0;
    $baslik = $yazi['baslik'] ?? 'Bir hikaye';

    $kontrol = $db->prepare("SELECT id FROM bookmarks WHERE kullanici_id = ? AND yazi_id = ?");
    $kontrol->execute([$kullanici_id, $yazi_id]);
    
    if ($kontrol->rowCount() > 0) {
        $sil = $db->prepare("DELETE FROM bookmarks WHERE kullanici_id = ? AND yazi_id = ?");
        $sil->execute([$kullanici_id, $yazi_id]);
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        $ekle = $db->prepare("INSERT INTO bookmarks (kullanici_id, yazi_id) VALUES (?, ?)");
        $ekle->execute([$kullanici_id, $yazi_id]);

        // YENİ EKLENEN ÖZELLİK: KAYDETME BİLDİRİMİ
        if ($kullanici_id != $yazar_id && $yazar_id > 0) {
            $mesaj = $kullanici_isim . " yazını kaydetti"; 
            $detay = '"' . $baslik . '"'; 
            // Sistem ikonuyla (yeşil) görünmesi için type olarak 'sistem' gönderiyoruz
            $db->prepare("INSERT INTO notifications (user_id, type, message, detail, is_read) VALUES (?, 'sistem', ?, ?, 0)")
               ->execute([$yazar_id, $mesaj, $detay]);
        }

        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>