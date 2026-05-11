<?php 
// Geliştirme aşamasındaki olası hataları ekrana basmak için (Ders 08)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum kontrolü ve Veritabanı bağlantısı
require_once 'auth.php'; 
require_once 'api/baglanti.php';

$activePage = 'home'; 
$pageTitle = 'Ana Sayfa'; 

// --- PHP İLE FİLTRELEME VE VERİ ÇEKME MANTIĞI (Ders 08) ---
// Adres çubuğundan (URL) 'kategori' parametresi gelmişse onu al, gelmemişse 'all' (hepsi) say.
$secilen_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';

$yazilar = [];
try {
    // Kategori seçildiyse WHERE şartı ile sadece o kategoriyi getir, 'all' ise hepsini getir.
    // SQL Injection'dan korunmak için PDO prepare() kullanıyoruz.
    if ($secilen_kategori !== 'all') {
        $sorgu = $db->prepare("SELECT * FROM posts WHERE kategori_id = ? ORDER BY yayin_tarihi DESC");
        $sorgu->execute([$secilen_kategori]);
    } else {
        $sorgu = $db->query("SELECT * FROM posts ORDER BY yayin_tarihi DESC");
    }
    // Gelen tüm veriyi $yazilar dizisine (Array) aktarıyoruz.
    $yazilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $hataMesaji = "Veritabanı bağlantı hatası.";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    <style>
        body { background-color: #ffffff; font-family: system-ui, -apple-system, sans-serif; }
        .text-teal { color: #0d9488; }
        .bg-teal-light { background-color: #f0fdfa; }
        .serif-italic { font-family: 'Instrument Serif', Georgia, serif; font-style: italic; }
        
        .wrapped-banner {
            background: linear-gradient(135deg, #312e81, #581c87, #0f172a);
            border-radius: 2.5rem; padding: 3rem; color: white;
            position: relative; overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(168, 85, 247, 0.2);
        }
        
        .featured-img-container {
            border-radius: 2.5rem; overflow: hidden; display: block;
            box-shadow: 0 25px 50px -12px rgba(20, 184, 166, 0.1);
        }
        .featured-img-container img {
            width: 100%; height: 350px; object-fit: cover; transition: transform 0.7s ease;
        }
        .featured-img-container:hover img { transform: scale(1.05); }
        
        .post-card {
            border: none; border-radius: 1.5rem; transition: transform 0.3s ease;
            position: relative; border: 1px solid #f1f5f9; background: #fff;
        }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }
        .post-card img { border-radius: 1.5rem; height: 200px; object-fit: cover; }
        .hover-text-teal:hover { color: #0d9488 !important; }
    </style>
</head>
<body>
    <div class="container-fluid p-0 position-relative z-1">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <!-- Yan menüyü (Sidebar) dahil ediyoruz -->
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <!-- Üst menüyü (Topbar) dahil ediyoruz -->
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5" style="max-width: 1200px; margin: 0 auto;">
                    
                    <section class="wrapped-banner mb-5">
                        <div class="row align-items-center position-relative z-1">
                            <div class="col-md-8 text-center text-md-start mb-4 mb-md-0">
                                <div class="text-info fw-bold text-uppercase mb-2" style="letter-spacing: 2px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Postify Wrapped
                                </div>
                                <h2 class="serif-italic display-5 fw-bold mb-3">Aylık Okuma Özetin Hazır!</h2>
                                <p class="text-white-50 fw-light">
                                    Bu ay platformda ne kadar zaman geçirdin, en çok hangi kategorileri tükettin? Senin için hazırladığımız o büyüleyici hikayeye göz at.
                                </p>
                            </div>
                            <div class="col-md-4 text-center text-md-end">
                                <a href="ozet.php" class="btn btn-light rounded-pill px-4 py-3 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                     Özetimi Keşfet <i class="fa-solid fa-chevron-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- FİLTRELEME ALANI (PHP'ye uygun FORM yapısına çevrildi) -->
                    <section class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-4 mb-5">
                        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                            <div class="p-2 bg-teal-light rounded text-teal">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                            </div>
                            <span class="fw-bold text-uppercase" style="letter-spacing: 1px;">Akışı Filtrele</span>
                        </div>
                        
                        <div class="w-100" style="max-width: 300px;">
                            <!-- Form action index.php'ye gidiyor, method GET. onchange="this.form.submit()" ile butona basmadan otomatik gönderiyoruz. -->
                            <form method="GET" action="index.php" class="m-0">
                                <select name="kategori" id="kategori-secici" class="form-select form-select-lg rounded-pill fw-bold text-secondary shadow-sm" onchange="this.form.submit()">
                                    <!-- PHP Kısa If (Ternary) yapısı ile seçili kategoriyi 'selected' yapıyoruz ki sayfa yenilenince sıfırlanmasın -->
                                    <option value="all" <?= $secilen_kategori == 'all' ? 'selected' : '' ?>>Sana Özel</option>
                                    <option value="1" <?= $secilen_kategori == '1' ? 'selected' : '' ?>>Yazılım</option>
                                    <option value="2" <?= $secilen_kategori == '2' ? 'selected' : '' ?>>Teknoloji</option>
                                    <option value="3" <?= $secilen_kategori == '3' ? 'selected' : '' ?>>Bilim</option>
                                    <option value="4" <?= $secilen_kategori == '4' ? 'selected' : '' ?>>Finans</option>
                                    <option value="5" <?= $secilen_kategori == '5' ? 'selected' : '' ?>>Sağlık</option>
                                </select>
                            </form>
                        </div>
                    </section>

                    <!-- PHP İLE HTML BASMA (SERVER-SIDE RENDERING) -->
                    <?php if (isset($hataMesaji)): ?>
                        <div class="alert alert-danger text-center"><?= $hataMesaji ?></div>
                    <?php elseif (empty($yazilar)): ?>
                        <!-- YAZI YOKSA BOŞ EKRAN UYARISI -->
                        <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                            <h3 class="serif-italic mb-3 text-secondary">Buralar Henüz Sessiz</h3>
                            <p class="text-muted">Bu kategoride henüz bir hikaye paylaşılmamış.</p>
                        </div>
                    <?php else: ?>
                        
                        <!-- 1. MANŞET ALANI ($yazilar dizisinin ilk elemanı [0]) -->
                        <?php 
                            $featuredPost = $yazilar[0]; 
                            
                            // Güvenlik ve Varsayılan Değer Atamaları
                            $resim = !empty($featuredPost['kapak_resmi']) ? $featuredPost['kapak_resmi'] : "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000";
                            $tarih = !empty($featuredPost['yayin_tarihi']) ? date('d.m.Y', strtotime($featuredPost['yayin_tarihi'])) : "Tarih Yok";
                            
                            // PHP'nin yerleşik fonksiyonları strip_tags (HTML temizler) ve mb_substr (Metni kırpar) kullanıyoruz
                            $ozetMetin = !empty($featuredPost['icerik']) ? mb_substr(strip_tags($featuredPost['icerik']), 0, 150) . "..." : "İçerik özeti...";
                        ?>
                        
                        <div id="featured-alani">
                            <div class="text-teal fw-bold text-uppercase mb-4" style="letter-spacing: 2px; font-size: 0.8rem;">
                                <i class="fa-solid fa-star me-1"></i> Haftanın Hikayesi
                            </div>
                            <article class="row align-items-center mb-5">
                                <div class="col-lg-7 mb-4 mb-lg-0">
                                    <a href="detay.php?id=<?= $featuredPost['id'] ?>" class="d-block featured-img-container">
                                        <img src="<?= $resim ?>" alt="<?= htmlspecialchars($featuredPost['baslik']) ?>">
                                    </a>
                                </div>
                                <div class="col-lg-5 px-lg-4">
                                    <a href="detay.php?id=<?= $featuredPost['id'] ?>" class="text-decoration-none">
                                        <h2 class="serif-italic display-4 fw-bold text-dark mb-4 hover-text-teal transition-colors" style="line-height: 1.1;">
                                            <?= htmlspecialchars($featuredPost['baslik']) ?>
                                        </h2>
                                    </a>
                                    <p class="text-secondary fs-5 mb-4" style="line-height: 1.6;"><?= $ozetMetin ?></p>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-4">
                                        <a href="detay.php?id=<?= $featuredPost['id'] ?>" class="text-decoration-none text-teal fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
                                            Devamını Oku <i class="fa-solid fa-arrow-right ms-2"></i>
                                        </a>
                                        <span class="text-muted small fw-medium"><?= $tarih ?></span>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- 2. ALT KARTLAR (GRID ALANI) -->
                        <?php if (count($yazilar) > 1): ?>
                            <div id="grid-baslik" class="mt-5 mb-4 border-bottom pb-3">
                                <h5 class="fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-layer-group text-teal me-2"></i> Popüler Akış</h5>
                            </div>
                            
                            <div class="row g-4" id="grid-alani">
                                <!-- PHP For Döngüsü: Manşeti (0) bastığımız için 1'den başlatıyoruz -->
                                <?php for ($i = 1; $i < count($yazilar); $i++): ?>
                                    <?php 
                                        $post = $yazilar[$i]; 
                                        
                                        $postResim = !empty($post['kapak_resmi']) ? $post['kapak_resmi'] : "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500";
                                        $postTarih = !empty($post['yayin_tarihi']) ? date('d.m.Y', strtotime($post['yayin_tarihi'])) : "Tarih Yok";
                                        $postOzet = !empty($post['icerik']) ? mb_substr(strip_tags($post['icerik']), 0, 80) . "..." : "İçerik...";
                                        $yazar = !empty($post['yazar_adi']) ? $post['yazar_adi'] : "Yazar";
                                    ?>
                                    
                                    <div class="col-md-6 col-xl-4">
                                        <article class="card post-card h-100 p-3">
                                            <img src="<?= $postResim ?>" class="card-img-top" alt="<?= htmlspecialchars($post['baslik']) ?>">
                                            <div class="card-body px-2 py-4">
                                                <a href="detay.php?id=<?= $post['id'] ?>" class="text-decoration-none text-dark stretched-link hover-text-teal">
                                                    <h4 class="card-title serif-italic fw-bold mb-3"><?= htmlspecialchars($post['baslik']) ?></h4>
                                                </a>
                                                <p class="card-text text-secondary" style="font-size: 0.9rem;"><?= $postOzet ?></p>
                                            </div>
                                            <div class="card-footer bg-transparent border-0 px-2 pt-0 pb-2 text-muted d-flex justify-content-between position-relative z-index-2" style="font-size: 0.8rem;">
                                                <span class="fw-bold text-uppercase" style="letter-spacing: 1px;"><?= htmlspecialchars($yazar) ?></span>
                                                <span><?= $postTarih ?></span>
                                            </div>
                                        </article>
                                    </div>

                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?> <!-- PHP İF BLOĞUNUN BİTİŞİ -->

                </div>
            </main>
        </div>
    </div>

    <!-- Ortak Sidebar Scripti -->
    <script>
        var sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        if (sidebarToggleBtn) {
            sidebarToggleBtn.onclick = function() {
                var mainSidebar = document.getElementById('mainSidebar');
                if (mainSidebar.className.indexOf('collapsed') === -1) {
                    mainSidebar.className += ' collapsed';
                } else {
                    mainSidebar.className = mainSidebar.className.replace(' collapsed', '');
                }
            };
        }
    </script>
</body>
</html>