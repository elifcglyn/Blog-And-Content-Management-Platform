<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'baglanti.php';

$json = file_get_contents("php://input");
$veri = json_decode($json, true);

if(isset($veri['id'])) {
    try {
        // Tablondaki sütun isimleriyle eşleşen UPDATE sorgusu
        $sql = "UPDATE users SET 
                ad_soyad = ?, 
                username = ?, 
                avatar_url = ?, 
                bio = ?, 
                github_url = ?, 
                twitter_url = ?, 
                web_url = ? 
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $veri['ad_soyad'],
            $veri['username'],
            $veri['avatar_url'],
            $veri['bio'],
            $veri['github_url'],
            $veri['twitter_url'],
            $veri['web_url'],
            $veri['id']
        ]);

        echo json_encode(["success" => true, "message" => "Profil güncellendi"]);
    } catch(PDOException $e) {
        echo json_encode(["success" => false, "error" => "Veritabanı hatası: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Kullanıcı ID bulunamadı."]);
}
?>