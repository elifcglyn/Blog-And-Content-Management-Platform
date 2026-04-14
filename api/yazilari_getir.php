<?php
// 1. Önce mutfağın anahtarını alıyoruz (Bir önceki dosyayı buraya dahil ediyoruz)
require_once 'baglanti.php';

try {
    // 2. Veritabanına sorumuzu (SQL Query) soruyoruz: 
    // "posts tablosundaki her şeyi (*) yayın tarihine göre en yeniden eskiye doğru (DESC) getir"
    $sorgu = $db->prepare("SELECT * FROM posts ORDER BY yayin_tarihi DESC");
    $sorgu->execute();
    
    // 3. Gelen cevabı PHP'nin anlayacağı bir listeye çeviriyoruz
    $yazilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Son olarak bu listeyi React'ın anladığı evrensel dil olan JSON formatına çevirip ekrana basıyoruz
    echo json_encode($yazilar);

} catch (PDOException $e) {
    // Bir hata olursa hatayı JSON olarak göster
    echo json_encode(["hata" => "Yazılar getirilirken bir sorun oluştu: " . $e->getMessage()]);
}
?>