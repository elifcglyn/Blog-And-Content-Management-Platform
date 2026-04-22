<?php 
  $activePage = 'bookmarks'; 
  $pageTitle = 'Kütüphanen'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    <style>
        .post-card { border-radius: 1.5rem; border: 1px solid #f1f5f9; transition: 0.3s; background: white; text-decoration: none; display: block; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(20, 184, 166, 0.1); border-color: #14b8a6; }
        .post-img { width: 100%; height: 140px; object-fit: cover; border-radius: 1rem; }
        @media (min-width: 768px) { .post-img { width: 220px; } }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            <?php include 'sidebar.php'; ?>
            <main class="flex-grow-1" style="min-width: 0;">
                <?php include 'topbar.php'; ?>
                
                <div class="px-4 px-md-5 pb-5" style="max-width: 900px; margin: 0 auto;">
                    
                    <div class="mb-5 border-bottom pb-4">
                        <div class="text-teal fw-bold text-uppercase mb-2" style="letter-spacing: 2px; font-size: 0.75rem;">
                            <i class="fa-solid fa-bookmark me-1"></i> Kütüphanen
                        </div>
                        <h1 class="serif-italic display-5 fw-bold text-dark">Kaydedilenler</h1>
                        <p class="text-secondary fs-6" id="kayit-sayisi">Daha sonra okumak için ayırdığın hikayeler aranıyor...</p>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border text-teal" role="status"></div>
                    </div>

                    <div id="kaydedilenler-listesi" class="d-flex flex-column gap-4 d-none"></div>

                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // LocalStorage'dan kaydedilen ID'leri al (Ders 6: LocalStorage)
            let kaydedilenIdler = JSON.parse(localStorage.getItem('kaydedilenYazilar')) || [];

            document.getElementById('kayit-sayisi').innerText = `Daha sonra okumak için ayırdığın ${kaydedilenIdler.length} hikaye.`;

            if(kaydedilenIdler.length === 0) {
                document.getElementById('yukleniyor').classList.add('d-none');
                document.getElementById('kaydedilenler-listesi').classList.remove('d-none');
                document.getElementById('kaydedilenler-listesi').innerHTML = `
                    <div class="text-center py-5 bg-light rounded-4 border">
                        <i class="fa-regular fa-bookmark fs-1 text-muted mb-3"></i>
                        <h5 class="serif-italic fw-bold text-dark">Kütüphanen Boş</h5>
                        <p class="text-muted">Keşfet sayfasından beğendiğin yazıları kaydedebilirsin.</p>
                        <a href="kesfet.php" class="btn btn-dark rounded-pill px-4 mt-2" style="background-color: #0d9488;">Keşfetmeye Başla</a>
                    </div>`;
                return;
            }

            // Bütün yazıları çek ve sadece kaydedilenleri filtrele
            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(yazilar => {
                    document.getElementById('yukleniyor').classList.add('d-none');
                    const liste = document.getElementById('kaydedilenler-listesi');
                    liste.classList.remove('d-none');

                    const filtrelenmisYazilar = yazilar.filter(y => kaydedilenIdler.includes(parseInt(y.id)));

                    filtrelenmisYazilar.forEach(post => {
                        const rawDate = post.olusturulma_tarihi || post.yayin_tarihi || new Date();
                        const tarih = new Date(rawDate).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' });
                        const resim = post.kapak_resmi || "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500";
                        const icerik = post.icerik ? post.icerik.substring(0, 120).replace(/<[^>]+>/g, '') : "";

                        liste.innerHTML += `
                            <a href="detay.php?id=${post.id}" class="post-card p-3 d-flex flex-column flex-md-row align-items-center gap-4 text-dark">
                                <img src="${resim}" class="post-img">
                                <div class="flex-grow-1 w-100">
                                    <h3 class="serif-italic fw-bold mb-2">${post.baslik}</h3>
                                    <p class="text-secondary small mb-3">${icerik}...</p>
                                    <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                        <span class="fw-bold text-uppercase" style="letter-spacing: 1px;">Admin • ${tarih}</span>
                                        <i class="fa-solid fa-chevron-right text-teal"></i>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                });
            
            if(document.getElementById('sidebarToggleBtn')) {
                document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                    document.getElementById('mainSidebar').classList.toggle('collapsed');
                });
            }
        });
    </script>
</body>
</html>