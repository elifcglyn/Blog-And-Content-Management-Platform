<?php
// API Güvenliği: İşlemleri oturum (Session) üzerinden yönetmek için başlatıyoruz. (Ders 08)
session_start();
require_once 'baglanti.php';

// Güvenlik Duvarı: Oturum açmamış birisi dışarıdan (Fetch veya Postman ile) istek atarsa işlemi reddediyoruz.
if (!isset($_SESSION['kullanici_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açılmamış.']);
    exit;
}

// Manipülasyon Koruması: İşlemi kimin yaptığını dışarıdan (JS'den) almıyoruz, güvenilir sunucu belleğinden ($_SESSION) çekiyoruz.
$kullanici_id = $_SESSION['kullanici_id'];
$kullanici_isim = $_SESSION['ad_soyad'] ?? 'Bir kullanıcı';

// Sadece kaydedilecek yazının ID'sini Frontend'den ($_POST) alıyoruz.
$yazi_id = $_POST['yazi_id'] ?? null;

// Doğrulama (Validation): Geçerli bir yazı ID'si gelmediyse işlemi durdur.
if (!$yazi_id) {
    echo json_encode(['status' => 'error', 'message' => 'Yazı ID eksik.']);
    exit;
}

try {
    // BİLDİRİM HAZIRLIĞI: Kaydedilen yazının asıl yazarını bulmak için veritabanına soruyoruz.
    // SQL Injection'dan korunmak için PDO prepare kullanıyoruz.
    $postSorgu = $db->prepare("SELECT yazar_id, baslik FROM posts WHERE id = ?");
    $postSorgu->execute([$yazi_id]);
    $yazi = $postSorgu->fetch(PDO::FETCH_ASSOC);
    
    // Yazar verisi yoksa 0 ata (Null Coalescing Operator).
    $yazar_id = $yazi['yazar_id'] ?? 0;
    $baslik = $yazi['baslik'] ?? 'Bir hikaye';

    // TOGGLE (AÇ/KAPA) ALGORİTMASI: Bu kullanıcı, bu yazıyı daha önce 'bookmarks' (kaydedilenler) tablosuna eklemiş mi?
    $kontrol = $db->prepare("SELECT id FROM bookmarks WHERE kullanici_id = ? AND yazi_id = ?");
    $kontrol->execute([$kullanici_id, $yazi_id]);
    
    if ($kontrol->rowCount() > 0) {
        // SENARYO 1 (Kaldırma): Kayıt zaten var. O zaman kullanıcının kaydını siliyoruz (DELETE).
        $sil = $db->prepare("DELETE FROM bookmarks WHERE kullanici_id = ? AND yazi_id = ?");
        $sil->execute([$kullanici_id, $yazi_id]);
        
        // Frontend'deki (JS) ikonun içini boşaltması için JSON 'removed' yanıtı dönüyoruz.
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // SENARYO 2 (Ekleme): İlk defa kaydediyor. Tabloya yeni satır ekliyoruz (INSERT).
        $ekle = $db->prepare("INSERT INTO bookmarks (kullanici_id, yazi_id) VALUES (?, ?)");
        $ekle->execute([$kullanici_id, $yazi_id]);

        // İŞ MANTIĞI (Business Logic): Yazar kendi yazısını kaydetmediyse ona bildirim yolluyoruz.
        if ($kullanici_id != $yazar_id && $yazar_id > 0) {
            $mesaj = $kullanici_isim . " yazını kaydetti"; 
            $detay = '"' . $baslik . '"'; 
            
            // DİNAMİK BİLDİRİM: Arayüzde ikonun yeşil (sistem ikonu) çıkması için bildirim tipini 'sistem' olarak veritabanına yazıyoruz.
            $db->prepare("INSERT INTO notifications (user_id, type, message, detail, is_read) VALUES (?, 'sistem', ?, ?, 0)")
               ->execute([$yazar_id, $mesaj, $detay]);
        }

        // Frontend'deki (JS) ikonun içini doldurması için JSON 'added' yanıtı dönüyoruz.
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} catch (PDOException $e) {
    // Olası bir veritabanı kopmasında arayüzün (Frontend) çökmemesi için hatayı JSON olarak bildiriyoruz.
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>