<?php 
  // Sidebar'da Keşfet linkinin aktif (renkli) olmasını sağlayan değişken ataması
  $activePage = 'kesfet'; 
  $pageTitle = 'Keşfet'; 
?>
<?php
// Sayfa güvenliği ve veritabanı iletişimi için oturum kontrolü ve bağlantı dosyalarını dahil ediyoruz.
session_start();
require_once 'auth.php'; 
require_once 'api/baglanti.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Tüm sayfalarda ortak olan head (CSS, Bootstrap) dosyalarını çağırıyoruz -->
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        /* DİNAMİK CSS DEĞİŞKENLERİ: Javascript ile bu renkleri değiştirerek sayfa temasını anlık güncelleyeceğiz. */
        :root {
            --theme-color: #0d9488; /* Varsayılan renk: Yazılım (Teal) */
            --theme-bg: #f0fdfa;
        }

        body { 
            /* Tema rengi değiştiğinde arka planın yumuşakça geçiş yapması için transition ekliyoruz. */
            transition: background-color 0.5s ease;
        }
        
        /* Dinamik Arkaplan Parıltısı: Seçilen kategori rengine göre sayfanın köşesine devasa bir blur efekti veriyoruz. */
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
            pointer-events: none; /* Tıklamaları engellemek için */
        }

        /* Yatay Kaydırılabilir Kategori Menüsü: overflow-x ile yana kaydırma özelliği katıyoruz. */
        .category-scroll {
            display: flex;
            overflow-x: auto;
            gap: 0.75rem;
            padding-bottom: 1rem;
            scrollbar-width: none; /* Firefox için scrollbar gizleme */
        }
        .category-scroll::-webkit-scrollbar { display: none; } /* Chrome/Safari için scrollbar gizleme */

        /* Kategori Butonları ve Hover (Üzerine gelme) / Active (Seçili) durumları */
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
            /* Aktif butonda yukarıdaki CSS değişkenlerini (var) kullanarak rengi dinamik alıyoruz. */
            background-color: var(--theme-bg);
            color: var(--theme-color);
            border-color: var(--theme-color);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            transform: scale(1.05);
        }

        /* Dinamik Kart Tasarımı ve CSS Animasyonları */
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

    <!-- CSS ile oluşturduğumuz bulanık dinamik arka planı DOM'a ekliyoruz -->
    <div class="glow-background"></div>

    <div class="container-fluid p-0 position-relative z-1">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <!-- Yan menüyü (Sidebar) dahil ediyoruz -->
            <?php include 'sidebar.php'; ?>

            <!-- Ana içerik alanı. Arka planı transparent yaptık ki alttaki glow efekti görünsün. -->
            <main class="flex-grow-1" style="min-width: 0; background-color: transparent;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <header class="mb-5">
                        <h1 class="serif-italic display-4 fw-bold text-dark mb-3">Keşfet</h1>
                        <p class="text-secondary italic fw-light fs-5">İlgini çeken dünyalara dal ve en çok okunanları yakala.</p>
                    </header>

                    <!-- JavaScript ile dinamik olarak doldurulacak Kategori Menüsü alanı -->
                    <div class="category-scroll mb-5" id="kategori-alani"></div>

                    <!-- Kategori seçimine göre ismi ve rengi değişecek dinamik başlık -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <i class="fa-solid fa-arrow-trend-up fs-4" style="color: var(--theme-color); transition: 0.5s;"></i>
                        <h2 class="serif-italic mb-0" id="dinamik-baslik">Yazılım Gündemi</h2>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border" style="color: var(--theme-color);" role="status"></div>
                    </div>

                    <!-- Yazı kartlarının basılacağı Grid alanı -->
                    <div class="row g-4 mb-5" id="yazilar-alani"></div>

                </div>
            </main>
        </div>
    </div>

    <!-- Veri Çekme, Filtreleme ve Dinamik Tema İşlemleri (Slaytlardaki JS Mantığına Uygun) -->
    <script>
        // Kategori verilerini, ikonlarını ve tema renklerini bir JS Nesnesi (Object) içinde topluyoruz.
        var kategoriler = {
            "yazilim": { id: 1, icon: "fa-code", label: "Yazılım", color: "#0d9488", bg: "#f0fdfa" },
            "teknoloji": { id: 2, icon: "fa-laptop", label: "Teknoloji", color: "#2563eb", bg: "#eff6ff" },
            "bilim": { id: 3, icon: "fa-flask", label: "Bilim", color: "#0891b2", bg: "#ecfeff" },
            "finans": { id: 4, icon: "fa-wallet", label: "Finans", color: "#059669", bg: "#ecfdf5" },
            "saglik": { id: 5, icon: "fa-heart-pulse", label: "Sağlık", color: "#dc2626", bg: "#fef2f2" },
            "spor": { id: 6, icon: "fa-dumbbell", label: "Spor", color: "#ea580c", bg: "#fff7ed" },
            "sanat": { id: 8, icon: "fa-palette", label: "Sanat", color: "#c026d3", bg: "#fdf4ff" }
        };

        // Slaytlardaki standartlara uygun olarak var değişkenleri ile başlangıç durumlarını atıyoruz.
        var aktifKategoriKey = "yazilim";
        var tumYazilar = [];

        // HTML DOM yapısı hazır olduğunda fonksiyonları sırasıyla ateşliyoruz.
        window.onload = function() {
            kategoriButonlariniCiz();
            temaRenginiGuncelle();
            yazilariGetir();
        };

        // DOM Manipülasyonu: JS nesnesindeki verilerle kategori butonlarını HTML'e çiziyoruz.
        function kategoriButonlariniCiz() {
            var alan = document.getElementById('kategori-alani');
            alan.innerHTML = '';

            for (var key in kategoriler) {
                var kat = kategoriler[key];
                
                // Eğer döngüdeki anahtar (key) aktif kategoriye eşitse 'active' CSS sınıfını atıyoruz.
                var isActive = "";
                if (key === aktifKategoriKey) {
                    isActive = "active";
                }
                
                // create Element metodu ile sıfırdan bir HTML butonu oluşturuyoruz.
                var btn = document.createElement('button');
                btn.className = "cat-btn " + isActive;
                btn.innerHTML = "<i class='fa-solid " + kat.icon + " me-2'></i> " + kat.label;
                
                // Butona tıklandığında (onclick) temanın ve içeriğin değişmesini sağlayan Event'i ekliyoruz.
                // Closure (kapsam) problemi olmaması için dataset üzerinden key değerini taşıyoruz.
                btn.setAttribute("data-key", key);
                btn.onclick = function() {
                    aktifKategoriKey = this.getAttribute("data-key");
                    kategoriButonlariniCiz(); // Butonların aktiflik durumunu yenile
                    temaRenginiGuncelle();    // Sayfanın CSS renklerini değiştir
                    icerigiFiltrele();        // Yeni kategoriye göre veritabanından gelen veriyi süz
                };
                
                alan.appendChild(btn);
            }
        }

        // MİMARİNİN KALBİ: CSS Değişkenlerine JS ile müdahale ederek temanın anlık değişmesini sağlayan metot.
        function temaRenginiGuncelle() {
            var secili = kategoriler[aktifKategoriKey];
            document.documentElement.style.setProperty('--theme-color', secili.color);
            document.documentElement.style.setProperty('--theme-bg', secili.bg);
            
            document.getElementById('dinamik-baslik').innerText = secili.label + " Gündemi";
        }

        // AJAX (Fetch API) ile tüm yazıları JSON formatında çekiyoruz.
        function yazilariGetir() {
            fetch('api/yazilari_getir.php')
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    tumYazilar = data;
                    document.getElementById('yukleniyor').style.display = "none";
                    icerigiFiltrele(); // Veriler geldiği gibi filtrelemeyi tetikliyoruz.
                })
                .catch(function(err) {
                    console.error("API Hatası:", err);
                });
        }

        // Algoritmik Filtreleme: Tıklanan kategorinin ID'si ile eşleşen yazıları bulup ekrana basıyoruz.
        function icerigiFiltrele() {
            var alan = document.getElementById('yazilar-alani');
            alan.innerHTML = '';
            
            var seciliId = kategoriler[aktifKategoriKey].id;
            var seciliKat = kategoriler[aktifKategoriKey];
            
            // Slaytlara uygun klasik For döngüsü ile filtreleme ve 6 adetle sınırlama yapıyoruz.
            var filtrelenmis = [];
            var i;
            for (i = 0; i < tumYazilar.length; i++) {
                if (parseInt(tumYazilar[i].kategori_id) === seciliId) {
                    filtrelenmis.push(tumYazilar[i]);
                    if (filtrelenmis.length === 6) break; // Maksimum 6 yazı gösterilecek.
                }
            }

            // Seçilen kategoride veri yoksa boş ekran kalmaması için kullanıcıya özel uyarı tasarımı basıyoruz.
            if (filtrelenmis.length === 0) {
                alan.innerHTML = "<div class='col-12 text-center py-5'>" +
                    "<i class='fa-solid " + seciliKat.icon + " fa-3x mb-3 text-muted' style='opacity: 0.3;'></i>" +
                    "<h4 class='serif-italic text-muted'>Bu kategoride henüz popüler bir yazı yok.</h4>" +
                    "</div>";
                return;
            }

            // Filtrelenmiş verileri For döngüsü ve String birleştirme (+) yöntemi ile HTML kartları olarak DOM'a aktarıyoruz.
            var j;
            for (j = 0; j < filtrelenmis.length; j++) {
                var yazi = filtrelenmis[j];
                
                var tarih = "Tarih Yok";
                if (yazi.yayin_tarihi) {
                    tarih = yazi.yayin_tarihi; 
                }

                var ozet = "";
                if (yazi.icerik) {
                    // İçeriğin ilk 80 karakterini al ve basit bir şekilde özetle.
                    ozet = yazi.icerik.substring(0, 80) + "...";
                }

                var htmlCard = "<div class='col-md-6 col-lg-4'>" +
                    "<div class='explore-card p-4 d-flex flex-column' onclick=\"window.location.href='detay.php?id=" + yazi.id + "'\">" +
                        "<div class='mb-auto'>" +
                            "<span class='badge-dynamic d-inline-block mb-4'>" +
                                "<i class='fa-solid " + seciliKat.icon + " me-1'></i> " + seciliKat.label +
                            "</span>" +
                            "<h4 class='fw-bold text-dark mb-3' style='font-size: 1.25rem;'>" + yazi.baslik + "</h4>" +
                            "<p class='text-secondary small'>" + ozet + "</p>" +
                        "</div>" +
                        "<div class='d-flex justify-content-between align-items-center mt-4 pt-4 border-top'>" +
                            "<span class='text-muted' style='font-size: 0.8rem;'>" +
                                "<i class='fa-regular fa-clock me-1'></i> " + tarih +
                            "</span>" +
                            "<div class='icon-btn'><i class='fa-solid fa-arrow-right'></i></div>" +
                        "</div>" +
                    "</div>" +
                "</div>";

                alan.innerHTML += htmlCard;
            }
        }

        // Ortak Sidebar Butonu Scripti: Eğer sidebarToggleBtn varsa ona Event Listener atıyoruz.
        var sidebarBtn = document.getElementById('sidebarToggleBtn');
        if(sidebarBtn) {
            sidebarBtn.onclick = function() {
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