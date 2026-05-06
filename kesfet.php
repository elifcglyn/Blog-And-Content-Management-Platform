<?php 
  $activePage = 'kesfet'; // Sidebar'da Keşfet linki aktif olur
  $pageTitle = 'Keşfet'; 
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
        /* CSS DEĞİŞKENLERİ: Javascript ile bu renkleri dinamik değiştireceğiz */
        :root {
            --theme-color: #0d9488; /* Varsayılan: Yazılım (Teal) */
            --theme-bg: #f0fdfa;
        }

        body { 
            transition: background-color 0.5s ease;
        }
        
        /* Dinamik Arkaplan Parıltısı */
        .glow-background {
            position: fixed;
            top: -10%; right: -10%;
            width: 50vw; height: 50vw;
            background-color: var(--theme-color);
            opacity: 0.05;
            filter: blur(100px);
            border-radius: 50%;
            z-index: 0;
            transition: background-color 1s ease;
            pointer-events: none;
        }

        /* Yatay Kaydırılabilir Kategori Menüsü */
        .category-scroll {
            display: flex;
            overflow-x: auto;
            gap: 0.75rem;
            padding-bottom: 1rem;
            scrollbar-width: none;
        }
        .category-scroll::-webkit-scrollbar { display: none; }

        /* Kategori Butonları */
        .cat-btn {
            white-space: nowrap;
            border-radius: 50rem;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            transition: all 0.3s ease;
        }
        .cat-btn:hover { transform: scale(1.05); }
        .cat-btn.active {
            background-color: var(--theme-bg);
            color: var(--theme-color);
            border-color: var(--theme-color);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            transform: scale(1.05);
        }

        /* Dinamik Kart Tasarımı */
        .explore-card {
            border-radius: 2rem;
            border: 1px solid #f1f5f9;
            background: white;
            transition: all 0.4s ease;
            cursor: pointer;
            height: 100%;
            position: relative;
            z-index: 1;
        }
        .explore-card:hover {
            transform: translateY(-8px);
            border-color: var(--theme-color);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .explore-card .badge-dynamic {
            background-color: var(--theme-bg);
            color: var(--theme-color);
            padding: 0.4rem 0.8rem;
            border-radius: 50rem;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .explore-card .icon-btn {
            width: 35px; height: 35px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #f8fafc; color: var(--theme-color);
            transition: 0.3s;
        }
        .explore-card:hover .icon-btn { transform: scale(1.2); }
    </style>
</head>
<body>

    <div class="glow-background"></div>

    <div class="container-fluid p-0 position-relative z-1">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: transparent;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <header class="mb-5">
                        <h1 class="serif-italic display-4 fw-bold text-dark mb-3">Keşfet</h1>
                        <p class="text-secondary italic fw-light fs-5">İlgini çeken dünyalara dal ve en çok okunanları yakala.</p>
                    </header>

                    <div class="category-scroll mb-5" id="kategori-alani"></div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <i class="fa-solid fa-arrow-trend-up fs-4" style="color: var(--theme-color); transition: 0.5s;"></i>
                        <h2 class="serif-italic mb-0" id="dinamik-baslik">Yazılım Gündemi</h2>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border" style="color: var(--theme-color);" role="status"></div>
                    </div>

                    <div class="row g-4 mb-5" id="yazilar-alani"></div>

                </div>
            </main>
        </div>
    </div>

    <script>
        const kategoriler = {
            "yazilim": { id: 1, icon: "fa-code", label: "Yazılım", color: "#0d9488", bg: "#f0fdfa" },
            "teknoloji": { id: 2, icon: "fa-laptop", label: "Teknoloji", color: "#2563eb", bg: "#eff6ff" },
            "bilim": { id: 3, icon: "fa-flask", label: "Bilim", color: "#0891b2", bg: "#ecfeff" },
            "finans": { id: 4, icon: "fa-wallet", label: "Finans", color: "#059669", bg: "#ecfdf5" },
            "saglik": { id: 5, icon: "fa-heart-pulse", label: "Sağlık", color: "#dc2626", bg: "#fef2f2" },
            "spor": { id: 6, icon: "fa-dumbbell", label: "Spor", color: "#ea580c", bg: "#fff7ed" },
            "sanat": { id: 8, icon: "fa-palette", label: "Sanat", color: "#c026d3", bg: "#fdf4ff" }
        };

        let aktifKategoriKey = "yazilim";
        let tumYazilar = [];

        document.addEventListener("DOMContentLoaded", function() {
            kategoriButonlariniCiz();
            temaRenginiGuncelle();
            yazilariGetir();
        });

        function kategoriButonlariniCiz() {
            const alan = document.getElementById('kategori-alani');
            alan.innerHTML = '';

            for (const key in kategoriler) {
                const kat = kategoriler[key];
                const isActive = key === aktifKategoriKey ? 'active' : '';
                
                const btn = document.createElement('button');
                btn.className = `cat-btn ${isActive}`;
                btn.innerHTML = `<i class="fa-solid ${kat.icon} me-2"></i> ${kat.label}`;
                
                btn.onclick = function() {
                    aktifKategoriKey = key;
                    kategoriButonlariniCiz();
                    temaRenginiGuncelle();
                    icerigiFiltrele();
                };
                
                alan.appendChild(btn);
            }
        }

        function temaRenginiGuncelle() {
            const secili = kategoriler[aktifKategoriKey];
            document.documentElement.style.setProperty('--theme-color', secili.color);
            document.documentElement.style.setProperty('--theme-bg', secili.bg);
            document.getElementById('dinamik-baslik').innerText = secili.label + " Gündemi";
        }

        function yazilariGetir() {
            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(data => {
                    tumYazilar = data;
                    document.getElementById('yukleniyor').classList.add('d-none');
                    icerigiFiltrele();
                })
                .catch(err => console.error("API Hatası:", err));
        }

        function icerigiFiltrele() {
            const alan = document.getElementById('yazilar-alani');
            alan.innerHTML = '';
            const seciliId = kategoriler[aktifKategoriKey].id;
            const seciliKat = kategoriler[aktifKategoriKey];
            
            const filtrelenmis = tumYazilar.filter(y => parseInt(y.kategori_id) === seciliId).slice(0, 6);

            if (filtrelenmis.length === 0) {
                alan.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid ${seciliKat.icon} fa-3x mb-3 text-muted" style="opacity: 0.3;"></i>
                        <h4 class="serif-italic text-muted">Bu kategoride henüz popüler bir yazı yok.</h4>
                    </div>`;
                return;
            }

            filtrelenmis.forEach(yazi => {
                const tarih = new Date(yazi.yayin_tarihi).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' });
                alan.innerHTML += `
                    <div class="col-md-6 col-lg-4">
                        <div class="explore-card p-4 d-flex flex-column" onclick="window.location.href='detay.php?id=${yazi.id}'">
                            <div class="mb-auto">
                                <span class="badge-dynamic d-inline-block mb-4">
                                    <i class="fa-solid ${seciliKat.icon} me-1"></i> ${seciliKat.label}
                                </span>
                                <h4 class="fw-bold text-dark mb-3" style="font-size: 1.25rem;">${yazi.baslik}</h4>
                                <p class="text-secondary small">${yazi.icerik ? yazi.icerik.substring(0, 80).replace(/<[^>]+>/g, '') : ''}...</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    <i class="fa-regular fa-clock me-1"></i> ${tarih}
                                </span>
                                <div class="icon-btn"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </div>`;
            });
        }

        // Ortak Sidebar Scripti
        if(document.getElementById('sidebarToggleBtn')) {
            document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                document.getElementById('mainSidebar').classList.toggle('collapsed');
            });
        }
    </script>
</body>
</html>