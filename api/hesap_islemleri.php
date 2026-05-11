<?php
// API Güvenliği: Oturumu başlatıyoruz. (Ders 08)
session_start();
require_once 'baglanti.php';

// Güvenlik Duvarı: Giriş yapmamış veya yetkisiz birisi bu API'ye dışarıdan erişmeye çalışırsa engelliyoruz.
if (!isset($_SESSION['kullanici_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açılmamış.']);
    exit; // Kötü niyetli isteği burada tamamen durdur.
}

// Sadece POST metoduyla gelen form/ajax isteklerini kabul et (URL'den adres yazılarak gelinmesini engeller)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // İşlem tipini (silme veya dondurma) POST'tan alıyoruz.
    $action = $_POST['action'] ?? '';
    
    // ÇOK ÖNEMLİ: İşlemi yapılacak kullanıcının ID'sini asla dışarıdan (POST'tan) almıyoruz.
    // Başkasının hesabını silememesi için, güvenli olan sunucu taraflı $_SESSION dizisinden çekiyoruz.
    $userId = $_SESSION['kullanici_id'];

    try {
        // Yönlendirme (Routing) Mantığı: Gelen action değerine göre SQL sorgumuzu hazırlıyoruz.
        if ($action === 'deactivate') {
            // Hesabı Dondurma: Veriyi veritabanından silmiyoruz, sadece 'hesap_durumu' sütununu güncelliyoruz (Update).
            $stmt = $db->prepare("UPDATE users SET hesap_durumu = 'Pasif' WHERE id = ?");
        } else if ($action === 'delete') {
            // Hesabı Kalıcı Silme: Kullanıcının kaydını veritabanından tamamen siliyoruz (Delete).
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        } else {
            // Ne silme ne dondurma geldiyse (farklı bir parametre yollanmışsa) işlemi iptal et.
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz işlem parametresi.']);
            exit;
        }

        // PDO execute ile güvenli bir şekilde (SQL Injection'dan korunarak) sorguyu çalıştırıyoruz.
        if ($stmt->execute([$userId])) {
            // JavaScript'in (Frontend Fetch API) anlayabilmesi için işlemi başarılı olarak JSON formatında dönüyoruz.
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı işlemi başarısız oldu.']);
        }

    } catch (PDOException $e) {
        // Olası bir veritabanı çökmesinde (Örneğin tablo bulunamazsa) hatayı yakalayıp JSON olarak Frontend'e yolluyoruz.
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    // POST dışında (Örn: GET) bir istekle gelinirse reddediyoruz.
    echo json_encode(['status' => 'error', 'message' => 'Sadece POST istekleri kabul edilir.']);
}
?>