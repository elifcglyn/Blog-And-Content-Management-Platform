<?php 
  $activePage = 'yazilarim'; 
  $pageTitle = 'Yazılarım'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    <style>
        .post-card { border-radius: 1.5rem; border: 1px solid #f1f5f9; transition: 0.3s; background: white; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(0,0,0,0.05); }
        .post-img { width: 100%; height: 120px; object-fit: cover; border-radius: 1.2rem; }
        @media (min-width: 768px) { .post-img { width: 150px; } }
        
        /* Düzenle Butonu Özel Stil */
        .btn-edit { color: #0d9488; background: #f0fdfa; border: 1px solid #ccfbf1; }
        .btn-edit:hover { background: #0d9488; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            <?php include 'sidebar.php'; ?>
            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                <?php include 'topbar.php'; ?>
                <div class="px-4 px-md-5 pb-5" style="max-width: 1000px; margin: 0 auto;">
                    
                    <div class="mb-5 border-bottom pb-4 mt-4">
                        <div class="text-teal fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 2px;">
                            <i class="fa-solid fa-layer-group me-1"></i> İçerik Yönetimi
                        </div>
                        <h1 class="serif-italic display-5 fw-bold text-dark">Yazılarım</h1>
                        <p class="text-secondary fs-6">Yayınladığın hikayeleri yönetebilir, düzenleyebilir veya silebilirsin.</p>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border text-teal" role="status"></div>
                        <p class="serif-italic mt-3 text-muted">İçerikler hazırlanıyor...</p>
                    </div>

                    <div id="yazilar-listesi" class="d-flex flex-column gap-4 d-none"></div>

                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            yazilariGetir();
            
            if(document.getElementById('sidebarToggleBtn')) {
                document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                    document.getElementById('mainSidebar').classList.toggle('collapsed');
                });
            }
        });

        function yazilariGetir() {
            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(yazilar => {
                    document.getElementById('yukleniyor').classList.add('d-none');
                    const liste = document.getElementById('yazilar-listesi');
                    liste.classList.remove('d-none');

                    if(yazilar.length === 0) {
                        liste.innerHTML = `
                            <div class="text-center py-5 bg-light rounded-4 border">
                                <h4 class="serif-italic text-muted">Henüz hiç yazı paylaşmamışsın.</h4>
                                <a href="yeni-yazi.php" class="btn bg-teal text-white rounded-pill px-4 mt-3">İlk Hikayeni Yaz</a>
                            </div>`;
                        return;
                    }

                    liste.innerHTML = ''; // Temizle
                    yazilar.forEach(post => {
                        const rawDate = post.olusturulma_tarihi || post.yayin_tarihi || new Date();
                        const tarih = new Date(rawDate).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' });
                        const resim = post.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=500";
                        const icerik = post.icerik ? post.icerik.substring(0, 110).replace(/<[^>]+>/g, '') : "";

                        liste.innerHTML += `
                            <div class="post-card p-3 d-flex flex-column flex-md-row align-items-center gap-4">
                                <img src="${resim}" class="post-img shadow-sm">
                                <div class="flex-grow-1 w-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-light text-teal border border-teal-subtle fw-medium" style="font-size: 0.65rem;">YAYINLANDI</span>
                                        <span class="text-muted" style="font-size: 0.75rem;"><i class="fa-regular fa-calendar me-1"></i> ${tarih}</span>
                                    </div>
                                    <h4 class="serif-italic fw-bold text-dark mb-1">${post.baslik}</h4>
                                    <p class="text-secondary small mb-0">${icerik}...</p>
                                </div>
                                <div class="d-flex gap-2 mt-3 mt-md-0 w-100 w-md-auto justify-content-end">
                                    <a href="detay.php?id=${post.id}" class="btn btn-light rounded-pill btn-sm fw-bold px-3 shadow-sm" title="Görüntüle">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="duzenle.php?id=${post.id}" class="btn btn-edit rounded-pill btn-sm fw-bold px-3 shadow-sm" title="Düzenle">
                                        <i class="fa-regular fa-pen-to-square"></i> <span class="d-md-none d-lg-inline ms-1">Düzenle</span>
                                    </a>
                                    <button class="btn btn-outline-danger rounded-circle btn-sm p-2 shadow-sm" onclick="yaziSil(${post.id})" title="Sil">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                });
        }

        function yaziSil(id) {
            if(confirm('Bu hikayeyi silmek istediğine emin misin? Bu işlem geri alınamaz.')) {
                // Burada api/yazi_sil.php dosyasına istek atabilirsin
                alert('Silme işlemi için api/yazi_sil.php dosyası hazırlanmalı. Şimdilik ID: ' + id);
            }
        }
    </script>
</body>
</html>