<?php
// Oturum kontrolü ile API güvenliğini sağlıyoruz. (Ders 08)
session_start();
require_once 'baglanti.php';

// Güvenlik Duvarı: Giriş yapmamış birisi dışarıdan (örneğin Postman ile) POST isteği atarsa işlemi reddedip JSON hata mesajı dönüyoruz.
if (!isset($_SESSION['kullanici_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açılmamış.']);
    exit;
}

// İşlemi yapan kullanıcının verilerini, dışarıdan manipüle edilemeyen güvenli $_SESSION dizisi üzerinden alıyoruz.
$begenen_id = $_SESSION['kullanici_id'];
$begenen_isim = $_SESSION['ad_soyad'] ?? 'Bir kullanıcı';

// JavaScript (Fetch API) üzerinden gönderilen Yazı ID'sini yakalıyoruz.
$yazi_id = $_POST['yazi_id'] ?? null;

// Doğrulama (Validation): Yazı ID'si boş gelirse işlemi durdur.
if (!$yazi_id) {
    echo json_encode(['status' => 'error', 'message' => 'Yazı ID eksik.']);
    exit;
}

try {
    // BİLDİRİM ALGORİTMASI İÇİN HAZIRLIK: Önce beğenilen yazıyı veritabanından çekip asıl yazarını buluyoruz.
    // SQL Injection'dan korunmak için PDO prepare ve execute kullanıyoruz.
    $sorgu = $db->prepare("SELECT * FROM posts WHERE id = ?"); 
    $sorgu->execute([$yazi_id]);
    $yazi = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Yazının yazarını ve başlığını olası boşluk (null) ihtimallerine karşı (??) Null Coalescing operatörüyle güvene alıyoruz.
    $yazar_id = $yazi['yazar_id'] ?? $yazi['kullanici_id'] ?? 0;
    $baslik = $yazi['baslik'] ?? 'Bir hikaye';

    // TOGGLE (AÇ/KAPA) ALGORİTMASI: Kullanıcı bu yazıyı daha önce beğenmiş mi diye likes tablosunu kontrol ediyoruz.
    $kontrol = $db->prepare("SELECT id FROM likes WHERE kullanici_id = ? AND yazi_id = ?");
    $kontrol->execute([$begenen_id, $yazi_id]);

    if ($kontrol->rowCount() > 0) {
        // SENARYO 1: Zaten beğenmiş. O zaman kullanıcının beğenisini geri alıyoruz (DELETE işlemi).
        $db->prepare("DELETE FROM likes WHERE kullanici_id = ? AND yazi_id = ?")->execute([$begenen_id, $yazi_id]);
        
        // JavaScript tarafındaki DOM manipülasyonuna bilgi vermek için 'action' olarak 'removed' (kaldırıldı) yolluyoruz.
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // SENARYO 2: İlk defa beğeniyor. likes tablosuna yeni kayıt atıyoruz (INSERT işlemi).
        $db->prepare("INSERT INTO likes (kullanici_id, yazi_id) VALUES (?, ?)")->execute([$begenen_id, $yazi_id]);

        // BİLDİRİM GÖNDERME İŞLEMİ (Business Logic): 
        // Kullanıcı kendi yazısını beğendiyse kendi kendine bildirim gitmesini engellemek için (!=) kontrolü yapıyoruz.
        if ($begenen_id != $yazar_id && $yazar_id > 0) {
            $mesaj = $begenen_isim . " yazını beğendi"; 
            $detay = '"' . $baslik . '"'; 
            
            // Yazarın bildirim paneline düşmesi için veritabanına kaydediyoruz.
            $db->prepare("INSERT INTO notifications (user_id, type, message, detail, is_read) VALUES (?, 'begeni', ?, ?, 0)")
               ->execute([$yazar_id, $mesaj, $detay]);
        }
        
        // JavaScript tarafındaki butonun içini kırmızı yapması için 'added' (eklendi) mesajı yolluyoruz.
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} catch (PDOException $e) {
    // Hata Yönetimi: Sistemin çökmesini engelleyip hatayı frontend tarafına JSON formatında (Ders 04) iletiyoruz.
    echo json_encode(['status' => 'error', 'message' => 'SQL Hatası: ' . $e->getMessage()]);
}
?>