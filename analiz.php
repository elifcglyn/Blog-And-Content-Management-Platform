<?php 
  $activePage = 'analiz'; // Sidebar'da 'İstatistikler' kısmını koyu (aktif) yapar
  $pageTitle = 'Performans Analizi';  // Topbar'da yazacak başlık
?>
<?php
session_start();
require_once 'auth.php'; // Giriş kontrolü yapan dosyan
require_once 'api/baglanti.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }

        /* Modern Yuvarlak Hatlı Kartlar */
        .modern-card {
            background-color: #f8fafc;
            border-radius: 2rem;
            border: 1px solid #f1f5f9;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(20, 184, 166, 0.05);
        }

        /* İkon Kutuları */
        .icon-box {
            width: 45px; height: 45px;
            border-radius: 1rem;
            background: white;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Trend Grafiği Tasarımı */
        .chart-container {
            background-color: #f8fafc;
            border-radius: 2.5rem;
            border: 1px solid #f1f5f9;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .chart-glow {
            position: absolute; top: -50px; right: -50px;
            width: 250px; height: 250px;
            background-color: rgba(20, 184, 166, 0.1);
            filter: blur(80px); border-radius: 50%;
        }
        .bar-group { position: relative; height: 200px; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; width: 100%; cursor: pointer; }
        .bar-fill {
            width: 100%; background-color: #e2e8f0; border-radius: 1rem 1rem 0 0;
            transition: all 1s ease-in-out;
        }
        .bar-group:hover .bar-fill { background-color: #14b8a6; }
        
        .bar-tooltip {
            opacity: 0; position: absolute; top: -30px;
            background: #0f172a; color: white;
            font-size: 0.7rem; font-weight: bold; padding: 3px 8px; border-radius: 6px;
            transition: opacity 0.3s; pointer-events: none;
            white-space: nowrap;
        }
        .bar-group:hover .bar-tooltip { opacity: 1; }

        /* Koyu Tema Kartı (En Popüler 3) */
        .dark-card {
            background-color: #0f172a;
            border-radius: 2.5rem;
            border: 1px solid #1e293b;
            padding: 2rem;
            color: white;
            height: 100%;
        }
        .progress-custom {
            height: 6px; background-color: #1e293b; border-radius: 10px; overflow: hidden;
            border: 1px solid rgba(51, 65, 85, 0.5); margin-top: 8px;
        }
        .progress-fill {
            height: 100%; background-color: #2dd4bf;
            box-shadow: 0 0 10px rgba(45, 212, 191, 0.5);
            transition: width 1.5s ease-in-out;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <div class="pt-2 pb-5">
                        <div class="text-teal fw-bold text-uppercase mb-3" style="font-size: 0.7rem; letter-spacing: 2px;">
                            <i class="fa-solid fa-chart-simple me-1"></i> Analiz Paneli
                        </div>
                        <h1 class="serif-italic fw-bold text-dark mb-0" style="font-size: 3.8rem; letter-spacing: -1.5px; line-height: 0.9;">Performans Analizi</h1>
                        <p class="text-secondary mt-3 mb-0 fs-5">Yazılarının ve etkileşimlerinin anlık raporu.</p>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border text-teal" role="status"></div>
                    </div>

                    <div id="analiz-icerik" class="d-none">
                        <div class="row g-4 mb-5" id="istatistik-kartlari"></div>

                        <div class="row g-4 mb-5">
                            
                            <div class="col-lg-8">
                                <div class="chart-container">
                                    <div class="chart-glow"></div>
                                    <div class="d-flex justify-content-between align-items-center mb-5 position-relative z-1">
                                        <h4 class="serif-italic mb-0 fw-bold">Haftalık Okunma Trendi</h4>
                                        <i class="fa-solid fa-arrow-trend-up fs-4 text-teal"></i>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between gap-3 position-relative z-1" id="trend-grafigi"></div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="dark-card d-flex flex-column">
                                    <h4 class="serif-italic mb-4 fw-bold" style="color: #2dd4bf;">En Popüler 3</h4>
                                    <div class="d-flex flex-column gap-4 flex-grow-1" id="populer-yazilar"></div>
                                    <button class="btn btn-outline-light w-100 mt-5 rounded-pill text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px; border-color: rgba(255,255,255,0.1);">
                                        Tüm Raporu Gör <i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

   <script>
        document.addEventListener("DOMContentLoaded", function() {
            verileriCanliCek();
            
            if(document.getElementById('sidebarToggleBtn')) {
                document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                    document.getElementById('mainSidebar').classList.toggle('collapsed');
                });
            }
        });

        function verileriCanliCek() {
            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('yukleniyor').classList.add('d-none');
                    document.getElementById('analiz-icerik').classList.remove('d-none');
                    
                    // SADECE GİRİŞ YAPAN KULLANICININ YAZILARINI FİLTRELE (Test hesabı sorunu çözümü)
                    const aktifKullaniciId = <?= json_encode($_SESSION['kullanici_id'] ?? 0) ?>;
                    const yazilar = data.filter(post => post.yazar_id == aktifKullaniciId || post.kullanici_id == aktifKullaniciId);
                    
                    let toplamGoruntulenme = 0;
                    let toplamBegeni = 0;
                    let toplamKelimeSayisi = 0;

                    yazilar.forEach(post => {
                        toplamGoruntulenme += parseInt(post.okunma_sayisi) || 0;
                        toplamBegeni += parseInt(post.begeni_sayisi) || 0;
                        
                        if (post.icerik) {
                            let safMetin = post.icerik.replace(/<[^>]+>/g, '');
                            toplamKelimeSayisi += safMetin.split(/\s+/).length;
                        }
                    });

                    let toplamOkumaSuresi = Math.ceil(toplamKelimeSayisi / 200); 
                    if(toplamOkumaSuresi === 0 && yazilar.length > 0) toplamOkumaSuresi = 1; 

                    const toplamYazi = yazilar.length;

                    kartlariDoldur(toplamGoruntulenme, toplamOkumaSuresi, toplamYazi, toplamBegeni);
                    populerYazilariCiz(yazilar, toplamGoruntulenme);
                    trendGrafigiCiz(); 
                })
                .catch(err => {
                    console.error("Veri çekme hatası:", err);
                    document.getElementById('yukleniyor').innerHTML = '<div class="alert alert-danger">Veriler yüklenemedi.</div>';
                });
        }

        function kartlariDoldur(goruntulenme, okumaSuresi, yaziAdedi, begeni) {
            const alan = document.getElementById('istatistik-kartlari');
            alan.innerHTML = ''; 
            
            const kartVerileri = [
                { label: "Toplam Görüntülenme", value: goruntulenme, icon: "fa-eye", color: "#3b82f6", trend: "Gerçek Veri" },
                { label: "Tahmini Okuma (Dk)", value: okumaSuresi, icon: "fa-clock", color: "#14b8a6", trend: "Analiz" },
                { label: "Yayınlanmış Hikaye", value: yaziAdedi, icon: "fa-feather-pointed", color: "#a855f7", trend: "Sistem" },
                { label: "Toplam Beğeni", value: begeni, icon: "fa-heart", color: "#ef4444", trend: "Gerçek Veri" }
            ];

            kartVerileri.forEach(stat => {
                alan.innerHTML += `
                    <div class="col-md-6 col-xl-3">
                        <div class="modern-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="icon-box" style="color: ${stat.color};">
                                    <i class="fa-solid ${stat.icon} fs-5"></i>
                                </div>
                                <span class="badge rounded-pill text-teal px-2 py-1" style="background-color: #f0fdfa;">${stat.trend}</span>
                            </div>
                            <h2 class="fw-bold text-dark mb-0">${stat.value}</h2>
                            <p class="text-muted fw-bold text-uppercase mt-1 mb-0" style="font-size: 0.65rem; letter-spacing: 1px;">${stat.label}</p>
                        </div>
                    </div>
                `;
            });
        }

        function populerYazilariCiz(yazilar, genelToplamGoruntulenme) {
            const alan = document.getElementById('populer-yazilar');
            alan.innerHTML = '';
            
            if(yazilar.length === 0) {
                alan.innerHTML = "<p class='text-muted small'>Henüz analiz edilecek veri yok.</p>";
                return;
            }

            const siraliYazilar = yazilar.sort((a, b) => {
                let okunmaA = parseInt(a.okunma_sayisi) || 0;
                let okunmaB = parseInt(b.okunma_sayisi) || 0;
                return okunmaB - okunmaA; 
            });

            const enIyi3Yazi = siraliYazilar.slice(0, 3);

            enIyi3Yazi.forEach((post) => {
                let kisaBaslik = post.baslik.length > 22 ? post.baslik.substring(0, 22) + "..." : post.baslik;
                let okunma = parseInt(post.okunma_sayisi) || 0;
                
                let yuzde = genelToplamGoruntulenme > 0 ? Math.round((okunma / genelToplamGoruntulenme) * 100) : 0;
                if(yuzde < 5 && okunma > 0) yuzde = 5; 
                
                alan.innerHTML += `
                    <div>
                        <div class="d-flex justify-content-between text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                            <span class="text-light text-truncate" style="max-width: 180px;">${kisaBaslik}</span>
                            <span style="color: #2dd4bf;">${okunma} Okunma</span>
                        </div>
                        <div class="progress-custom">
                            <div class="progress-fill" style="width: 0%;" data-hedef="${yuzde}%"></div>
                        </div>
                    </div>
                `;
            });

            setTimeout(() => {
                document.querySelectorAll('.progress-fill').forEach(bar => {
                    bar.style.width = bar.getAttribute('data-hedef');
                });
            }, 100);
        }

        function trendGrafigiCiz() {
            const haftalikTrend = [
                { gun: 'Pzt', oran: 30 }, { gun: 'Sal', oran: 45 }, 
                { gun: 'Çar', oran: 35 }, { gun: 'Per', oran: 60 }, 
                { gun: 'Cum', oran: 80 }, { gun: 'Cmt', oran: 90 }, 
                { gun: 'Paz', oran: 50 }
            ];

            const alan = document.getElementById('trend-grafigi');
            alan.innerHTML = '';
            
            haftalikTrend.forEach(veri => {
                alan.innerHTML += `
                    <div class="bar-group w-100">
                        <div class="bar-tooltip">${veri.oran} Okunma</div>
                        <div class="bar-fill" style="height: 0%;" data-hedef="${veri.oran}%"></div>
                        <div class="text-muted fw-bold text-uppercase mt-2" style="font-size: 0.65rem;">${veri.gun}</div>
                    </div>
                `;
            });

            setTimeout(() => {
                document.querySelectorAll('.bar-fill').forEach(bar => {
                    bar.style.height = bar.getAttribute('data-hedef');
                });
            }, 100);
        }
    </script>
</body>
</html>