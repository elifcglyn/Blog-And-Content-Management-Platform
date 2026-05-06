<?php 
  require_once 'auth.php'; // Giriş kontrolünü en üste alalım
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
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(0,0,0,0.05); border-color: #0d9488; }
        .post-img { width: 100%; height: 140px; object-fit: cover; border-radius: 1.2rem; }
        @media (min-width: 768px) { .post-img { width: 180px; } }
        .btn-edit { color: #0d9488; background: #f0fdfa; border: 1px solid #ccfbf1; }
        .btn-edit:hover { background: #0d9488; color: white; }
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }
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
                        <div class="text-teal fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 2px; color: #0d9488;">
                            <i class="fa-solid fa-layer-group me-1"></i> İçerik Yönetimi
                        </div>
                        <h1 class="serif-italic display-5 fw-bold text-dark">Yazılarım</h1>
                        <p class="text-secondary fs-6">Sadece senin tarafından yayınlanan hikayeler burada listelenir.</p>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border" role="status" style="color: #0d9488;"></div>
                        <p class="serif-italic mt-3 text-muted">Senin hikayelerin yükleniyor...</p>
                    </div>

                    <div id="yazilar-listesi" class="d-flex flex-column gap-4 d-none"></div>

                </div>
            </main>
        </div>
    </div>

    <script>
        const KULLANICI_ID = <?= $_SESSION['kullanici_id'] ?>;

        document.addEventListener("DOMContentLoaded", () => {
            yazilariGetir();
        });

        function yazilariGetir() {
            // SİHİRLİ DOKUNUŞ: API'ye "sadece benim yazılarımı getir" diyoruz
            fetch(`api/yazilari_getir.php?yazar_id=${KULLANICI_ID}`)
                .then(res => res.json())
                .then(yazilar => {
                    document.getElementById('yukleniyor').classList.add('d-none');
                    const liste = document.getElementById('yazilar-listesi');
                    liste.classList.remove('d-none');
                    liste.innerHTML = ''; 

                    if(!yazilar || yazilar.length === 0) {
                        liste.innerHTML = `
                            <div class="text-center py-5 bg-light rounded-4 border">
                                <h4 class="serif-italic text-muted">Henüz hiç yazı paylaşmamışsın.</h4>
                                <a href="yeni-yazi.php" class="btn text-white rounded-pill px-4 mt-3 fw-bold" style="background-color: #0d9488;">İlk Hikayeni Yaz</a>
                            </div>`;
                        return;
                    }

                    yazilar.forEach(post => {
                        // TARAYICI UYUMLU, ZIRHLI TARİH FORMATI (Boşlukları T ile değiştiriyoruz)
                        const tarih = post.yayin_tarihi 
                            ? new Date(post.yayin_tarihi.replace(' ', 'T')).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' }) 
                            : 'Tarih Yok';

                        const resim = post.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=500";
                        // Varsa 'ozet' sütununu kullan, yoksa icerikten kes
                        const ozet = post.ozet ? post.ozet : (post.icerik ? post.icerik.substring(0, 120).replace(/<[^>]+>/g, '') + '...' : "");

                        liste.innerHTML += `
                            <div class="post-card p-3 d-flex flex-column flex-md-row align-items-center gap-4">
                                <img src="${resim}" class="post-img shadow-sm">
                                <div class="flex-grow-1 w-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge rounded-pill bg-light text-dark border fw-medium" style="font-size: 0.65rem;">ID: #${post.id}</span>
                                        <span class="text-muted" style="font-size: 0.75rem;"><i class="fa-regular fa-calendar me-1"></i> ${tarih}</span>
                                    </div>
                                    <h4 class="serif-italic fw-bold text-dark mb-1">${post.baslik}</h4>
                                    <p class="text-secondary small mb-0">${ozet}</p>
                                </div>
                                <div class="d-flex gap-2 mt-3 mt-md-0 w-100 w-md-auto justify-content-end">
                                    <a href="detay.php?id=${post.id}" class="btn btn-light rounded-pill btn-sm fw-bold px-3 border shadow-sm">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="duzenle.php?id=${post.id}" class="btn btn-edit rounded-pill btn-sm fw-bold px-3 shadow-sm">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <button class="btn btn-outline-danger rounded-circle btn-sm p-2 shadow-sm" onclick="yaziSil(${post.id})">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    console.error("Hata:", err);
                    document.getElementById('yukleniyor').innerHTML = "<p class='text-danger text-center'>Veriler yüklenirken bir sorun oluştu.</p>";
                });
        }

        function yaziSil(id) {
            if(confirm('Bu hikayeyi silmek istediğine emin misin?')) {
                fetch(`api/yazi_sil.php?id=${id}`, { method: 'GET' })
                .then(res => res.json())
                .then(data => {
                    if(data.basarili) {
                        yazilariGetir(); // Listeyi yenile
                    } else {
                        alert("Silme hatası: " + data.hata);
                    }
                });
            }
        }
    </script>
</body>
</html>