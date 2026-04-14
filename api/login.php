<?php
require_once 'baglanti.php';

$veri = json_decode(file_get_contents("php://input"), true);

if ($veri) {
    $email = $veri['email'];
    $sifre = $veri['sifre'];

    try {
        $sorgu = $db->prepare("SELECT * FROM users WHERE email = ? AND sifre = ?");
        $sorgu->execute([$email, $sifre]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($kullanici) {
            // Şifre doğruysa kullanıcı bilgilerini gönder (Şifreyi güvenlik için siliyoruz)
            unset($kullanici['sifre']);
            echo json_encode(["basarili" => true, "kullanici" => $kullanici]);
        } else {
            echo json_encode(["basarili" => false, "mesaj" => "E-posta veya şifre hatalı!"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["basarili" => false, "mesaj" => "Hata: " . $e->getMessage()]);
    }
}
?>