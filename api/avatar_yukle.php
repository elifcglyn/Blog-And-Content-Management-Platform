<?php
// Çıktı Tamponlaması (Output Buffering): PHP'nin ekrana aniden hata veya boşluk basmasını engeller. (JSON yapısını korumak için hayati önem taşır)
ob_start(); 

// Kullanıcının ID'sine ulaşmak için oturumu (Session) başlatıyoruz. (Ders 08)
session_start();
require_once 'baglanti.php';

// Güvenlik ve API Standartı: Olası hataları arka planda yakala ama tarayıcıya (ekrana) yansıtma ki JSON formatı bozulmasın.
error_reporting(E_ALL);
ini_set('display_errors', 0);

// İşlemler başlamadan önce varsayılan olarak API'den dönecek JSON yanıt dizisi (Array) oluşturuyoruz.
$response = ['status' => 'error', 'message' => 'Bilinmeyen bir hata oluştu.'];

// Doğrulama: Sadece POST metoduyla istek atıldıysa ve 'avatar' adında bir dosya ($_FILES) geldiyse işlemleri başlatıyoruz.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    
    $userId = $_SESSION['kullanici_id'];
    $uploadDir = '../uploads/avatars/'; 
    
    // Dinamik Klasör Yönetimi: Eğer sunucuda uploads/avatars klasörü yoksa, 'mkdir' ile bu klasörü otomatik oluşturuyoruz. (0777 okuma/yazma iznidir)
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $response['message'] = 'Uploads klasörü oluşturulamadı. İzinleri kontrol edin.';
            echo json_encode($response);
            exit;
        }
    }

    // Dosya Bilgilerini Alma: $_FILES süper globali ile geçici belleğe alınan dosyayı yakalıyoruz.
    $file = $_FILES['avatar'];
    
    // Güvenlik: pathinfo ile dosyanın sadece uzantısını alıyoruz (.jpg, .png vb.)
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION); 
    
    // İsim Çakışmasını Önleme (Unique Name): "avatar_15_1678901234.jpg" gibi içine 'time()' (zaman damgası) katarak benzersiz bir isim üretiyoruz.
    $fileName = "avatar_" . $userId . "_" . time() . "." . $ext;
    $targetPath = $uploadDir . $fileName;

    // move_uploaded_file: Dosyayı sunucunun gizli geçici klasöründen (tmp_name) alıp kalıcı klasörümüze (targetPath) taşıyoruz.
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        
        // Veritabanına yazılacak olan dosya yolunu belirliyoruz.
        $dbPath = 'uploads/avatars/' . $fileName;
        
        // PDO Update: Eski avatarın yolunu veritabanında yeni yüklenen resmin yolu ile güncelliyoruz.
        $stmt = $db->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        $stmt->execute([$dbPath, $userId]);
        
        // Gelişmiş UX: Kullanıcı sayfayı yenilemeden menüdeki küçük resmin de değişmesi için $_SESSION verisini de anında güncelliyoruz.
        $_SESSION['avatar_url'] = $dbPath;
        
        // Her şey başarılıysa JSON yanıt dizimizi güncelliyoruz.
        $response = ['status' => 'success', 'new_url' => $dbPath];
    } else {
        $response['message'] = 'Dosya taşınamadı. Klasör yazma izinlerini kontrol edin.';
    }
}

// ob_start ile tutulan tüm gereksiz çıktıları (boşluklar, gizli hatalar) temizliyoruz.
ob_end_clean();

// Tarayıcıya saf JSON döndürdüğümüzü belirtip, $response dizisini JSON'a çevirerek (Ders 04) JavaScript'e yolluyoruz.
header('Content-Type: application/json');
echo json_encode($response);
?>