<?php 
// Sayfa güvenliği ve veritabanı bağlantılarını içeri aktarıyoruz. Oturum açılmadıysa auth.php müdahale edecek. (Ders 08)
$activePage = 'notif'; 
$pageTitle = 'Bildirimler'; 
require_once 'auth.php'; 
require_once 'api/baglanti.php';

// Tanımsız değişken hatalarını önlemek için başlangıç değişkenlerimizi tanımlıyoruz.
$kullanici_id = $_SESSION['kullanici_id'];
$bildirimler = [];
$yeniBildirimSayisi = 0;

// ALGORİTMA: Veritabanından gelen standart tarihi "5 DK ÖNCE", "2 SA ÖNCE" gibi Instagram/X tarzı okunabilir bir formata çeviren özel PHP fonksiyonum. (Ders 07: Fonksiyonlar)
function zamanFarki($tarih) {
    $zaman = strtotime($tarih);
    $fark = time() - $zaman; // Şu anki zaman ile bildirim zamanı arasındaki saniye farkını bul.
    if ($fark < 60) return "AZ ÖNCE";
    if ($fark < 3600) return floor($fark / 60) . " DK ÖNCE";
    if ($fark < 86400) return floor($fark / 3600) . " SA ÖNCE";
    return floor($fark / 86400) . " GÜN ÖNCE";
}

// VERİTABANI İŞLEMLERİ (Ders 08: PDO)
try {
    // Sadece aktif kullanıcının bildirimlerini en yeniden en eskiye (DESC) doğru sıralayarak çekiyoruz.
    $sorgu = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $sorgu->execute([$kullanici_id]);
    $bildirimler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    // PHP'nin yerleşik array_filter fonksiyonu ile, dizideki 'is_read' değeri 0 (okunmamış) olanları süzüp count ile toplamını buluyoruz.
    $yeniBildirimSayisi = count(array_filter($bildirimler, function($b) { return $b['is_read'] == 0; }));
} catch (PDOException $e) {
    // HATA YÖNETİMİ: Eğer notifications tablosu henüz veritabanında oluşturulmamışsa, sistemin 500 hatası verip çökmesini engelliyoruz.
    // Try-Catch bloğunun Catch kısmında frontend'in test edilebilmesi için yedek (mock) veriler basıyoruz.
    $bildirimler = [
        ['id' => 1, 'type' => 'begeni', 'message' => 'Sarah Chen yazını beğendi', 'detail' => '"React Compiler: Geleceğe Bakış"', 'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes')), 'is_read' => 0],
        ['id' => 2, 'type' => 'yorum', 'message' => 'Alex Johnson yorum yaptı', 'detail' => '"Tailwind v4"', 'created_at' => date('Y-m-d H:i:s', strtotime('-23 minutes')), 'is_read' => 0],
        ['id' => 3, 'type' => 'sistem', 'message' => 'Sistem bir güncelleme yayınladı', 'detail' => '"Postify v2.5 Yayında!"', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')), 'is_read' => 1]
    ];
    $yeniBildirimSayisi = 2;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Ortak head etiketlerini barındıran yapıyı dahil ediyoruz -->
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }

        /* Bildirim Kartı Temel Tasarımı ve Hover (üzerine gelme) efekti */
        .notif-card {
            background-color: #f8fafc; border-radius: 1.5rem; border: 1px solid #f1f5f9;
            padding: 1.25rem; margin-bottom: 1rem; transition: all 0.3s ease;
            display: flex; align-items: center; gap: 1.2rem;
        }
        .notif-card:hover { transform: translateY(-3px); border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        
        /* Eğer bildirim okunmamışsa (unread) arkaplanı beyaz yapıp çerçevesini vurguluyoruz. */
        .notif-card.unread { background-color: #fff; border-color: #0d9488; box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.1); }
        
        .icon-circle {
            width: 48px; height: 48px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 1.1rem;
        }
        
        /* PHP'den gelecek bildirim türüne göre dinamik olarak basılacak renk paletleri */
        .bg-like { background-color: #fff1f2; color: #ef4444; }
        .bg-comment { background-color: #f0f9ff; color: #0ea5e9; }
        .bg-system { background-color: #f0fdf4; color: #22c55e; }
        .bg-default { background-color: #f1f5f9; color: #64748b; }
        
        .new-badge {
            background-color: #f0fdfa; color: #0d9488;
            font-weight: bold; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem;
            border: 1px solid #ccfbf1;
        }
        
        .unread-dot { width: 10px; height: 10px; border-radius: 50%; background-color: #0d9488; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <div class="pt-2 pb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4">
                        <div>
                            <h1 class="serif-italic fw-bold text-dark mb-0" style="font-size: 3.8rem; letter-spacing: -1.5px; line-height: 0.9;">Bildirimler</h1>
                            <p class="text-secondary mt-3 mb-0 fs-5">Etkileşimleri ve sistem güncellemelerini yönet.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            
                            <!-- PHP IF Kontrolü: Okunmamış bildirim varsa dinamik YENİ rozetini ekrana basıyoruz. -->
                            <?php if($yeniBildirimSayisi > 0): ?>
                                <span class="new-badge"><i class="fa-solid fa-bell-concierge me-1"></i> <?= $yeniBildirimSayisi ?> YENİ</span>
                            <?php endif; ?>
                            
                            <!-- Basit form submission ile "Tümünü Okundu İşaretle" işlemi (POST Metodu) -->
                            <form action="api/bildirimleri_okundu_yap.php" method="POST" class="m-0">
                                <button type="submit" class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm">Tümünü Okundu İşaretle</button>
                            </form>
                        </div>
                    </div>

                    <div class="notif-list" style="max-width: 850px;">
                        
                        <!-- Veritabanından dönen dizi boşsa, kullanıcıya boş state (durum) tasarımı gösteriyoruz. -->
                        <?php if(empty($bildirimler)): ?>
                            <div class="text-center py-5 bg-light rounded-4 border">
                                <i class="fa-regular fa-bell-slash fs-1 text-muted mb-3"></i>
                                <h5 class="serif-italic fw-bold">Henüz bildirim yok</h5>
                                <p class="text-secondary mb-0">Yeni bir etkileşim aldığında burada görünecek.</p>
                            </div>
                        <?php else: ?>
                        
                            <!-- Döngü Mimarisi (Ders 07): Her bir bildirim için dinamik HTML kartı çiziyoruz -->
                            <?php foreach($bildirimler as $b): ?>
                                <?php 
                                    // Gelen bildirimin tipine göre IF - ELSEIF yapısıyla doğru icon ve arkaplan sınıfını belirliyoruz.
                                    $icon = 'fa-bell'; $bgClass = 'bg-default';
                                    if($b['type'] == 'begeni') { $icon = 'fa-heart'; $bgClass = 'bg-like'; }
                                    elseif($b['type'] == 'yorum') { $icon = 'fa-comment'; $bgClass = 'bg-comment'; }
                                    elseif($b['type'] == 'sistem') { $icon = 'fa-code-branch'; $bgClass = 'bg-system'; }
                                    
                                    // Okundu bilgisini kontrol edip CSS sınıfını ayarlıyoruz.
                                    $okunduClass = $b['is_read'] == 0 ? 'unread' : 'opacity-75';
                                ?>
                                
                                <div class="notif-card <?= $okunduClass ?>">
                                    <div class="icon-circle <?= $bgClass ?>"><i class="fa-solid <?= $icon ?>"></i></div>
                                    <div class="flex-grow-1">
                                        
                                        <!-- XSS (Cross-Site Scripting) güvenlik önlemi: Veritabanından gelen metni htmlspecialchars ile temizleyip basıyoruz -->
                                        <p class="mb-0 text-dark fs-6"><?= htmlspecialchars($b['message']) ?></p>
                                        
                                        <?php if(!empty($b['detail'])): ?>
                                            <p class="mb-0 text-teal fw-bold small mt-1"><?= htmlspecialchars($b['detail']) ?></p>
                                        <?php endif; ?>
                                        
                                        <small class="text-muted fw-bold mt-2 d-block" style="font-size: 0.65rem; letter-spacing: 1px;">
                                            <!-- Yukarıda yazdığımız özel zamanFarki fonksiyonunu çağırarak tarihi 'Az Önce' gibi formatlıyoruz -->
                                            <i class="fa-regular fa-clock me-1"></i> <?= zamanFarki($b['created_at']) ?>
                                        </small>
                                    </div>
                                    
                                    <!-- Okunmamışsa yeşil minik bildirim noktasını ekle -->
                                    <?php if($b['is_read'] == 0): ?>
                                        <div class="unread-dot shadow-sm"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                        <?php endif; ?>

                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>