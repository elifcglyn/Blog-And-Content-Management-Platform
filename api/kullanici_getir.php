<?php
// GİZLİ HATALARI EKRANA YAZDIRMA KODLARI
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'baglanti.php';

// Tarayıcıya bunun bir JSON verisi olduğunu söylüyoruz
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Şifre hariç tüm profil bilgilerini çekiyoruz
    $sorgu = $db->prepare("SELECT id, ad_soyad, username, email, rol, bio, avatar_url, github_url, twitter_url, web_url, hesap_durumu, kayit_tarihi FROM users WHERE id = ?");
    $sorgu->execute([$id]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($kullanici) {
        echo json_encode($kullanici);
    } else {
        echo json_encode(["error" => "Kullanıcı bulunamadı."]);
    }
} else {
    echo json_encode(["error" => "ID parametresi eksik."]);
}
?>