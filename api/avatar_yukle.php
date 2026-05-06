<?php
// Tüm çıktıları durdur (Hata mesajlarının JSON'u bozmasını engeller)
ob_start(); 
session_start();
require_once 'baglanti.php';

// Hataları sadece arka planda tut, ekrana basma
error_reporting(E_ALL);
ini_set('display_errors', 0);

$response = ['status' => 'error', 'message' => 'Bilinmeyen bir hata oluştu.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $userId = $_SESSION['kullanici_id'];
    $uploadDir = '../uploads/avatars/'; 
    
    // Klasör kontrolü
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $response['message'] = 'Uploads klasörü oluşturulamadı. İzinleri kontrol edin.';
            echo json_encode($response);
            exit;
        }
    }

    $file = $_FILES['avatar'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = "avatar_" . $userId . "_" . time() . "." . $ext;
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $dbPath = 'uploads/avatars/' . $fileName;
        $stmt = $db->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        $stmt->execute([$dbPath, $userId]);
        $_SESSION['avatar_url'] = $dbPath;
        
        $response = ['status' => 'success', 'new_url' => $dbPath];
    } else {
        $response['message'] = 'Dosya taşınamadı. Klasör yazma izinlerini kontrol edin.';
    }
}

// Tamponu temizle ve sadece JSON bas
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);