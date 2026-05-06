<?php 
  require_once 'auth.php'; 
  $activePage = 'home'; 
  $pageTitle = 'Ana Sayfa'; 
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
        
        /* Banner Tasarımı */
        .wrapped-banner {
            background: linear-gradient(135deg, #312e81, #581c87, #0f172a);
            border-radius: 2.5rem; padding: 3rem; color: white;
            position: relative; overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(168, 85, 247, 0.2);
        }
        
        /* Haftanın Hikayesi Görseli */
        .featured-img-container {
            border-radius: 2.5rem; overflow: hidden; display: block;
            box-shadow: 0 25px 50px -12px rgba(20, 184, 166, 0.1);
        }
        .featured-img-container img {
            width: 100%; height: 350px; object-fit: cover; transition: transform 0.7s ease;
        }
        .featured-img-container:hover img { transform: scale(1.05); }
        
        /* Grid Kart Tasarımları */
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
            
            <!-- MOBİL UYUMLU SİDEBAR ENTEGRASYONU -->
            <?php include 'sidebar.php'; ?>

            <!-- flex-grow-1 ve min-width: 0 ile sayfa taşmaları engellendi -->
            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5" style="max-width: 1200px; margin: 0 auto;">
                    
                    <!-- LİNKİ DÜZELTİLMİŞ WRAPPED BANNER -->
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
                                <!-- BURASI DETAY.PHP YERİNE DOĞRUDAN OZET.PHP'YE YÖNLENDİRİLDİ -->
                                <a href="ozet.php" class="btn btn-light rounded-pill px-4 py-3 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                     Özetimi Keşfet <i class="fa-solid fa-chevron-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- FİLTRELEME ALANI -->
                    <section class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-4 mb-5">
                        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                            <div class="p-2 bg-teal-light rounded text-teal">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                            </div>
                            <span class="fw-bold text-uppercase" style="letter-spacing: 1px;">Akışı Filtrele</span>
                        </div>
                        
                        <div class="w-100" style="max-width: 300px;">
                            <select id="kategori-secici" class="form-select form-select-lg rounded-pill fw-bold text-secondary shadow-sm" onchange="yazilariGetir(this.value)">
                                <option value="all">Sana Özel</option>
                                <option value="1">Yazılım</option>
                                <option value="2">Teknoloji</option>
                                <option value="3">Bilim</option>
                                <option value="4">Finans</option>
                                <option value="5">Sağlık</option>
                            </select>
                        </div>
                    </section>

                    <!-- YÜKLENİYOR SPINNER -->
                    <div id="yukleniyor" class="text-center my-5">
                        <div class="spinner-border text-teal" role="status"></div>
                    </div>

                    <!-- YAZILARIN BASILACAĞI ALANLAR -->
                    <div id="featured-alani"></div>

                    <div id="grid-baslik" class="d-none mt-5 mb-4 border-bottom pb-3">
                        <h5 class="fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-layer-group text-teal me-2"></i> Popüler Akış</h5>
                    </div>
                    
                    <div class="row g-4" id="grid-alani"></div>

                </div>
            </main>
        </div>
    </div>

    <!-- AJAX VE DOM MANİPÜLASYONU -->
    <script>
        let tumYazilar = [];

        document.addEventListener("DOMContentLoaded", function() {
            yazilariGetir("all");
        });

        function yazilariGetir(kategoriFiltresi) {
            const yukleniyor = document.getElementById('yukleniyor');
            
            // Eğer veriler zaten çekildiyse tekrar API'ye gitme (Performans)
            if (tumYazilar.length > 0) {
                ekranaBas(tumYazilar, kategoriFiltresi);
                return;
            }

            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(data => {
                    tumYazilar = data; 
                    yukleniyor.classList.add('d-none');
                    ekranaBas(tumYazilar, kategoriFiltresi);
                })
                .catch(err => {
                    console.error("Veri çekme hatası:", err);
                    yukleniyor.innerHTML = '<div class="alert alert-danger">Veriler yüklenirken hata oluştu.</div>';
                });
        }

        function ekranaBas(yazilar, kategori) {
            const featuredAlani = document.getElementById('featured-alani');
            const gridAlani = document.getElementById('grid-alani');
            const gridBaslik = document.getElementById('grid-baslik');

            featuredAlani.innerHTML = '';
            gridAlani.innerHTML = '';

            // Filtereleme Mantığı
            const filtrelenmisYazilar = kategori === "all" ? yazilar : yazilar.filter(y => parseInt(y.kategori_id) === parseInt(kategori));

            if (filtrelenmisYazilar.length === 0) {
                featuredAlani.innerHTML = `
                    <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                        <h3 class="serif-italic mb-3 text-secondary">Buralar Henüz Sessiz</h3>
                        <p class="text-muted">Bu kategoride henüz bir hikaye paylaşılmamış.</p>
                    </div>`;
                gridBaslik.classList.add('d-none');
                return;
            }

            const featuredPost = filtrelenmisYazilar[0];
            const remainingPosts = filtrelenmisYazilar.slice(1);

            // 1. ÖNE ÇIKAN (FEATURED) POST
            let tarih = "...";
            try { if(featuredPost.yayin_tarihi) tarih = new Date(featuredPost.yayin_tarihi.replace(' ', 'T')).toLocaleDateString('tr-TR'); } catch(e) {}
            const resim = featuredPost.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=1000";
            
            const ozetMetin = featuredPost.icerik ? featuredPost.icerik.substring(0, 150).replace(/<[^>]+>/g, '') : "İçerik özeti...";

            // LİNKLER DETAY.PHP OLARAK GÜNCELLENDİ
            featuredAlani.innerHTML = `
                <div class="text-teal fw-bold text-uppercase mb-4" style="letter-spacing: 2px; font-size: 0.8rem;">
                    <i class="fa-solid fa-star me-1"></i> Haftanın Hikayesi
                </div>
                <article class="row align-items-center mb-5">
                    <div class="col-lg-7 mb-4 mb-lg-0">
                        <a href="detay.php?id=${featuredPost.id}" class="d-block featured-img-container">
                            <img src="${resim}" alt="${featuredPost.baslik}">
                        </a>
                    </div>
                    <div class="col-lg-5 px-lg-4">
                        <a href="detay.php?id=${featuredPost.id}" class="text-decoration-none">
                            <h2 class="serif-italic display-4 fw-bold text-dark mb-4 hover-text-teal transition-colors" style="line-height: 1.1;">${featuredPost.baslik}</h2>
                        </a>
                        <p class="text-secondary fs-5 mb-4" style="line-height: 1.6;">${ozetMetin}...</p>
                        <div class="d-flex justify-content-between align-items-center border-top pt-4">
                            <a href="detay.php?id=${featuredPost.id}" class="text-decoration-none text-teal fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
                                Devamını Oku <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                            <span class="text-muted small fw-medium">${tarih}</span>
                        </div>
                    </div>
                </article>
            `;

            // 2. GRID POSTS (KARTLAR)
            if (remainingPosts.length > 0) {
                gridBaslik.classList.remove('d-none');
                
                remainingPosts.forEach(post => {
                    let postTarih = "...";
                    try { if(post.yayin_tarihi) postTarih = new Date(post.yayin_tarihi.replace(' ', 'T')).toLocaleDateString('tr-TR'); } catch(e) {}
                    const postResim = post.kapak_resmi || "https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=500";
                    const postOzet = post.icerik ? post.icerik.substring(0, 80).replace(/<[^>]+>/g, '') : "İçerik...";
                    
                    gridAlani.innerHTML += `
                        <div class="col-md-6 col-xl-4">
                            <article class="card post-card h-100 p-3">
                                <img src="${postResim}" class="card-img-top" alt="${post.baslik}">
                                <div class="card-body px-2 py-4">
                                    <a href="detay.php?id=${post.id}" class="text-decoration-none text-dark stretched-link hover-text-teal">
                                        <h4 class="card-title serif-italic fw-bold mb-3">${post.baslik}</h4>
                                    </a>
                                    <p class="card-text text-secondary" style="font-size: 0.9rem;">${postOzet}...</p>
                                </div>
                                <div class="card-footer bg-transparent border-0 px-2 pt-0 pb-2 text-muted d-flex justify-content-between position-relative z-index-2" style="font-size: 0.8rem;">
                                    <span class="fw-bold text-uppercase" style="letter-spacing: 1px;">${post.yazar_adi || 'Yazar'}</span>
                                    <span>${postTarih}</span>
                                </div>
                            </article>
                        </div>
                    `;
                });
            } else {
                gridBaslik.classList.add('d-none');
            }
        }
    </script>
</body>
</html>