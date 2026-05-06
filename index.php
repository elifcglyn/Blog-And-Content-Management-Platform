<?php 
  $activePage = 'home'; 
  $pageTitle = 'Akış'; 
  require_once 'auth.php'; // Giriş kontrolü
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }

        /* Banner Tasarımı (Wrapped Banner) */
        .wrapped-banner {
            background: linear-gradient(135deg, #312e81, #581c87, #0f172a);
            border-radius: 2.5rem; padding: 3rem; color: white;
            position: relative; overflow: hidden;
            box-shadow: 0 25px 30px -10px rgba(168, 85, 247, 0.2);
        }

        /* Haftanın Hikayesi Görseli */
        .featured-img-container { 
            border-radius: 2.5rem; overflow: hidden; 
            box-shadow: 0 30px 60px -12px rgba(0,0,0,0.15); 
        }
        .featured-img-container img { 
            width: 100%; height: 350px; object-fit: cover; transition: 0.8s ease; 
        }
        .featured-img-container:hover img { transform: scale(1.05); }

        /* Grid Kart Tasarımları */
        .post-card { 
            border: none; border-radius: 2rem; transition: 0.4s ease; 
            border: 1px solid #f1f5f9; background: #fff; 
        }
        .post-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 30px 40px -10px rgba(0,0,0,0.08); border-color: #0d9488; 
        }
        .post-card img { border-radius: 1.8rem; height: 200px; object-fit: cover; }
        
        .hover-text-teal:hover { color: #0d9488 !important; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <!-- KEŞFET SAYFASIYLA BİREBİR AYNI KALIN BAŞLIK VE ALT METİN -->
                    <div class="pt-2 pb-4">
                        <h1 class="serif-italic fw-bold text-dark mb-2" style="font-size: 4rem; letter-spacing: -1.5px; line-height: 1;">Akış</h1>
                        <p class="text-secondary fs-5 mb-0">Senin için derlenen en güncel hikayeler ve içerikler.</p>
                    </div>

                    <section class="wrapped-banner mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-8 text-center text-md-start mb-4 mb-md-0">
                                <div class="text-info fw-bold text-uppercase mb-2 small" style="letter-spacing: 3px;">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Postify Wrapped
                                </div>
                                <h2 class="serif-italic display-4 fw-bold mb-3">Aylık Özetin Hazır!</h2>
                                <p class="text-white-50 fw-light fs-5 mb-0">
                                    Bu ay en çok hangi hikayeler seni etkiledi? Senin için hazırladığımız o büyüleyici hikayeye göz at.
                                </p>
                            </div>
                            <div class="col-md-4 text-center text-md-end">
                                <a href="ozet.php" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                    Özetimi Keşfet <i class="fa-solid fa-chevron-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </section>

                    <section class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-4 mb-5">
                        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                            <div class="p-3 bg-teal-light rounded-circle text-teal">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                            </div>
                            <span class="fw-bold text-uppercase" style="letter-spacing: 2px;">Akışı Filtrele</span>
                        </div>
                        
                        <div class="w-100" style="max-width: 250px;">
                            <select id="kategori-secici" class="form-select rounded-pill fw-bold text-secondary shadow-sm" onchange="yazilariGetir(this.value)">
                                <option value="all">Sana Özel</option>
                                <option value="1">Yazılım</option>
                                <option value="2">Teknoloji</option>
                            </select>
                        </div>
                    </section>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border text-teal" role="status"></div>
                    </div>

                    <div id="featured-alani"></div>
                    <div class="row g-4 mt-2" id="grid-alani"></div>

                </div>
            </main>
        </div>
    </div>

    <script>
        let tumYazilar = [];

        document.addEventListener("DOMContentLoaded", () => yazilariGetir("all"));

        function yazilariGetir(kategori) {
            fetch('api/yazilari_getir.php').then(res => res.json()).then(data => {
                tumYazilar = data;
                document.getElementById('yukleniyor').classList.add('d-none');
                ekranaBas(data, kategori);
            });
        }

        function ekranaBas(yazilar, kategori) {
            const featuredAlani = document.getElementById('featured-alani');
            const gridAlani = document.getElementById('grid-alani');
            featuredAlani.innerHTML = ''; gridAlani.innerHTML = '';

            const filtreli = kategori === "all" ? yazilar : yazilar.filter(y => y.kategori_id == kategori);

            if (filtreli.length > 0) {
                const f = filtreli[0];
                const fTarih = f.yayin_tarihi ? new Date(f.yayin_tarihi.replace(' ', 'T')).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' }) : '...';
                
                featuredAlani.innerHTML = `
                    <h5 class="serif-italic fw-bold text-teal mb-4 fs-3">Haftanın Hikayesi</h5>
                    <div class="row align-items-center mb-5">
                        <div class="col-lg-7"><a href="detay.php?id=${f.id}" class="featured-img-container d-block"><img src="${f.kapak_resmi || 'https://images.unsplash.com/photo-1555066931-4365d14bab8c'}"></a></div>
                        <div class="col-lg-5 ps-lg-5 mt-4 mt-lg-0">
                            <h2 class="serif-italic display-5 fw-bold mb-3 hover-text-teal transition-colors">${f.baslik}</h2>
                            <p class="text-secondary fs-5 mb-4">${f.ozet}...</p>
                            <div class="pt-4 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-uppercase" style="letter-spacing:1px">${f.yazar_adi}</span>
                                <span class="text-muted small">${fTarih}</span>
                            </div>
                        </div>
                    </div>`;

                filtreli.slice(1).forEach(p => {
                    const pTarih = p.yayin_tarihi ? new Date(p.yayin_tarihi.replace(' ', 'T')).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long' }) : '...';
                    gridAlani.innerHTML += `
                        <div class="col-md-6 col-xl-4">
                            <article class="post-card p-3 h-100">
                                <img src="${p.kapak_resmi || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085'}" class="w-100 mb-3" alt="${p.baslik}">
                                <div class="p-2">
                                    <h4 class="serif-italic fw-bold fs-4 mb-2 hover-text-teal transition-colors">${p.baslik}</h4>
                                    <p class="text-secondary small line-clamp-2">${p.ozet}...</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                        <span class="small fw-bold text-teal">${p.yazar_adi}</span>
                                        <span class="small text-muted">${pTarih}</span>
                                    </div>
                                    <a href="detay.php?id=${p.id}" class="stretched-link"></a>
                                </div>
                            </article>
                        </div>`;
                });
            }
        }
    </script>
</body>
</html>