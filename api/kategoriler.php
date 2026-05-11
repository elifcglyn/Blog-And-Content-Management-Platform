<?php
// Veritabanı ile iletişim kurabilmek için ortak bağlantı dosyamızı sayfaya dahil ediyoruz. (Ders 08)
require_once 'baglanti.php';

// Hata Yönetimi: Veritabanında tablo eksikliği gibi kritik bir sorun olursa sitenin çökmesini engellemek için Try-Catch bloğu açıyoruz.
try {
    
    // GÜVENLİK VE PERFORMANS MANTIĞI: Dışarıdan (Kullanıcıdan) hiçbir veri veya ID almadığımız için SQL Injection riski yoktur.
    // Bu sebeple 'prepare' ve 'execute' kullanmak yerine daha hızlı ve doğrudan olan 'query' metodunu tercih ettik.
    // ORDER BY isim ASC: Kategorilerin karmaşık gelmemesi için A'dan Z'ye alfabetik olarak sıralıyoruz.
    $sorgu = $db->query("SELECT * FROM categories ORDER BY isim ASC");
    
    // VERİ DÖNÜŞÜMÜ (Data Parsing): 
    // 1. fetchAll(PDO::FETCH_ASSOC) ile veritabanından dönen tüm satırları sütun isimleriyle (id, isim) eşleşen bir PHP dizisine çeviriyoruz.
    // 2. json_encode() ile bu PHP dizisini JavaScript'in (Fetch API) okuyabileceği evrensel JSON formatına dönüştürüp arayüze (Frontend) fırlatıyoruz. (Ders 04)
    echo json_encode($sorgu->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    // Veritabanında 'categories' tablosu bulunamazsa veya bağlantı koparsa, hatayı yakalayıp JSON formatında arayüze bildiriyoruz.
    echo json_encode(["hata" => "Kategoriler yüklenemedi: " . $e->getMessage()]);
}
?>