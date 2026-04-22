<?php 
  $activePage = 'profil'; 
  $pageTitle = 'Profilim'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        .profile-header { 
            background: #f8fafc; 
            border-radius: 2rem; 
            padding: 2.5rem; 
            border: 1px solid #f1f5f9;
        }
        .profile-avatar { 
            width: 130px; height: 130px; 
            border-radius: 50%; object-fit: cover; 
            border: 5px solid white; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
        }

        .post-card { 
            border-radius: 1.5rem; border: 1px solid #e2e8f0; 
            transition: all 0.3s ease; overflow: hidden; 
            display: flex; flex-direction: column; 
            background: #fff; height: 100%; text-decoration: none;
        }
        .post-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 25px -5px rgba(13, 148, 136, 0.1); 
            border-color: #14b8a6; 
        }
        
        @media (min-width: 768px) {
            .post-card { flex-direction: row; align-items: stretch; height: 220px; }
            .post-card-img-wrap { width: 35%; flex-shrink: 0; }
            .post-card-body { width: 65%; display: flex; flex-direction: column; justify-content: center; }
        }
        
        .post-card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }

        .custom-modal-backdrop {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px);
            z-index: 1050; display: none; align-items: center; justify-content: center;
        }
        .custom-modal { background: white; border-radius: 2rem; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; }
        
        /* Sekme Butonu Tasarımları */
        .sekme-btn { cursor: pointer; color: #64748b; font-weight: 600; border: none; background: transparent; padding: 0.5rem 1rem; border-radius: 50rem; transition: 0.3s; }
        .sekme-btn.aktif { background-color: #0d9488; color: white; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5" style="max-width: 1000px; margin: 0 auto;">
                    
                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border text-teal" role="status"></div>
                        <p class="serif-italic mt-3 text-muted fs-5">Profil yükleniyor...</p>
                    </div>

                    <div id="profil-alani" class="d-none mt-4">
                        
                        <div class="profile-header d-flex flex-column flex-md-row align-items-center gap-4 mb-5">
                            <div class="position-relative">
                                <img src="" id="profil-avatar" class="profile-avatar">
                            </div>
                            <div class="text-center text-md-start flex-grow-1">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                                    <h1 class="serif-italic fw-bold text-dark mb-0" id="profil-isim">...</h1>
                                    <button class="btn btn-outline-dark rounded-pill btn-sm fw-bold px-4 mt-3 mt-md-0" onclick="modalAc()">Profili Düzenle</button>
                                </div>
                                <p class="text-teal fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 2px;">
                                    @<span id="profil-username">user</span> • Yazar
                                </p>
                                <p class="text-secondary mb-3 fs-6" id="profil-bio" style="font-style: italic;">"..."</p>
                                
                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-4 text-muted small fw-medium mb-3">
                                    <span><i class="fa-regular fa-calendar text-teal me-2"></i> Üyelik: <span id="profil-tarih"></span></span>
                                    <span><i class="fa-solid fa-feather-pointed text-teal me-2"></i> <span id="yazi-sayisi">0</span> Yayın</span>
                                </div>

                                <div class="d-flex justify-content-center justify-content-md-start gap-3" id="profil-sosyal"></div>
                            </div>
                        </div>

                        <h4 class="serif-italic fw-bold border-bottom pb-3 mb-4 text-dark">Son Yazılar</h4>
                        <div class="row g-4" id="yazilar-listesi"></div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="custom-modal-backdrop" id="duzenle-modal">
        <div class="custom-modal">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h5 class="serif-italic mb-0 fw-bold">Profili Düzenle</h5>
                <button class="btn-close" onclick="modalKapat()"></button>
            </div>
            
            <div class="p-4">
                
                <div class="d-flex gap-2 mb-4">
                    <button class="sekme-btn aktif" id="btn-genel" onclick="sekmeGoster('genel')">Genel Bilgiler</button>
                    <button class="sekme-btn" id="btn-sosyal" onclick="sekmeGoster('sosyal')">Sosyal Medya</button>
                </div>

                <div id="sekme-genel">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small">Görünen Ad</label>
                        <input type="text" id="input-isim" class="form-control bg-light border-0 py-2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small">Kullanıcı Adı</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted">@</span>
                            <input type="text" id="input-username" class="form-control bg-light border-0 py-2">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small">Profil Fotoğrafı URL (Opsiyonel)</label>
                        <input type="url" id="input-avatar" class="form-control bg-light border-0 py-2" placeholder="Boş bırakırsan isminin baş harfi görünür">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small">Biyografi</label>
                        <textarea id="input-bio" class="form-control bg-light border-0" rows="3"></textarea>
                    </div>
                </div>

                <div id="sekme-sosyal" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small"><i class="fa-brands fa-github me-1"></i> Github Linki</label>
                        <input type="url" id="input-github" class="form-control bg-light border-0 py-2" placeholder="https://github.com/SeninProfilin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small"><i class="fa-brands fa-twitter me-1"></i> Twitter / X</label>
                        <input type="url" id="input-twitter" class="form-control bg-light border-0 py-2" placeholder="https://twitter.com/SeninProfilin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold text-uppercase small"><i class="fa-solid fa-link me-1"></i> Web Sitesi</label>
                        <input type="url" id="input-web" class="form-control bg-light border-0 py-2" placeholder="https://seninsiten.com">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button class="btn btn-light rounded-pill fw-bold px-4 me-2" onclick="modalKapat()">İptal</button>
                    <button class="btn text-white rounded-pill fw-bold px-4 py-2" style="background-color: #0d9488;" onclick="profiliKaydet()">Değişiklikleri Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const KULLANICI_ID = 1; 

        document.addEventListener("DOMContentLoaded", () => {
            kullaniciVeYazilariGetir();
        });

        // SEKME DEĞİŞTİRME FONKSİYONU (YENİ)
        function sekmeGoster(sekmeAdi) {
            // Önce tüm butonların aktifliğini al, tüm içerikleri gizle
            document.getElementById('btn-genel').classList.remove('aktif');
            document.getElementById('btn-sosyal').classList.remove('aktif');
            document.getElementById('sekme-genel').style.display = 'none';
            document.getElementById('sekme-sosyal').style.display = 'none';

            // Sadece tıklananı aktif et ve göster
            document.getElementById('btn-' + sekmeAdi).classList.add('aktif');
            document.getElementById('sekme-' + sekmeAdi).style.display = 'block';
        }

        function kullaniciVeYazilariGetir() {
            fetch(`api/kullanici_getir.php?id=${KULLANICI_ID}`)
                .then(res => res.json())
                .then(user => {
                    const adSoyad = user.ad_soyad ? user.ad_soyad.trim() : "İsimsiz Kullanıcı";
                    // Adın temizlenmiş haliyle avatar oluşturuluyor (Böylece EM yerine Elif için sadece E yazar)
                    const avatarUrl = user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(adSoyad)}&background=0d9488&color=fff&size=200`;
                    
                    document.getElementById('profil-isim').innerText = adSoyad;
                    document.getElementById('profil-username').innerText = user.username || "user";
                    document.getElementById('profil-bio').innerText = user.bio ? `"${user.bio}"` : "Henüz bir biyografi yazılmadı.";
                    document.getElementById('profil-tarih').innerText = new Date(user.kayit_tarihi || new Date()).toLocaleDateString('tr-TR');
                    document.getElementById('profil-avatar').src = avatarUrl;
                    
                    let sosyalHtml = '';
                    if(user.github_url) sosyalHtml += `<a href="${user.github_url}" target="_blank" class="text-dark fs-5 hover-teal"><i class="fa-brands fa-github"></i></a>`;
                    if(user.twitter_url) sosyalHtml += `<a href="${user.twitter_url}" target="_blank" class="text-info fs-5 hover-teal"><i class="fa-brands fa-twitter"></i></a>`;
                    if(user.web_url) sosyalHtml += `<a href="${user.web_url}" target="_blank" class="text-secondary fs-5 hover-teal"><i class="fa-solid fa-link"></i></a>`;
                    document.getElementById('profil-sosyal').innerHTML = sosyalHtml;

                    document.getElementById('input-isim').value = adSoyad;
                    document.getElementById('input-username').value = user.username || "";
                    document.getElementById('input-avatar').value = user.avatar_url || "";
                    document.getElementById('input-bio').value = user.bio || "";
                    document.getElementById('input-github').value = user.github_url || "";
                    document.getElementById('input-twitter').value = user.twitter_url || "";
                    document.getElementById('input-web').value = user.web_url || "";

                    return fetch("api/yazilari_getir.php");
                })
                .then(res => res.json())
                .then(yazilar => {
                    document.getElementById('yukleniyor').classList.add('d-none');
                    document.getElementById('profil-alani').classList.remove('d-none');

                    const kullaniciYazilari = yazilar.filter(y => parseInt(y.yazar_id) === KULLANICI_ID);
                    document.getElementById('yazi-sayisi').innerText = kullaniciYazilari.length;
                    
                    const liste = document.getElementById('yazilar-listesi');
                    liste.innerHTML = '';

                    if(kullaniciYazilari.length === 0) {
                        liste.innerHTML = `
                            <div class="col-12 text-center py-5 bg-light rounded-4 border">
                                <p class="text-muted mb-3">Henüz bir yazı paylaşmadın.</p>
                                <a href="yeni-yazi.php" class="btn text-white rounded-pill px-4 btn-sm fw-bold" style="background-color: #0d9488;">İlk Yazını Oluştur</a>
                            </div>`;
                        return;
                    }

                    kullaniciYazilari.forEach(post => {
                        const tarih = new Date(post.olusturulma_tarihi).toLocaleDateString('tr-TR');
                        const resim = post.kapak_resmi || "https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=800";
                        const icerikOzet = post.icerik ? post.icerik.substring(0, 160).replace(/<[^>]+>/g, '') : "";
                        
                        liste.innerHTML += `
                            <div class="col-12">
                                <a href="detay.php?id=${post.id}" class="post-card text-dark">
                                    <div class="post-card-img-wrap">
                                        <img src="${resim}" alt="${post.baslik}">
                                    </div>
                                    <div class="post-card-body p-4">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="text-muted small"><i class="fa-solid fa-calendar-day me-1"></i> ${tarih}</span>
                                        </div>
                                        <h4 class="serif-italic fw-bold mb-2">${post.baslik}</h4>
                                        <p class="text-secondary mb-0 line-clamp-2 fs-6">${icerikOzet}...</p>
                                    </div>
                                </a>
                            </div>`;
                    });
                });
        }

        function modalAc() { 
            // Modal açılırken her zaman ilk sekmeyi gösterelim
            sekmeGoster('genel');
            document.getElementById('duzenle-modal').style.display = 'flex'; 
        }
        function modalKapat() { document.getElementById('duzenle-modal').style.display = 'none'; }

        function profiliKaydet() {
            // Girilen ismi al ve boşluklarını temizle
            const yeniIsim = document.getElementById('input-isim').value.trim();
            
            const veri = {
                id: KULLANICI_ID,
                ad_soyad: yeniIsim,
                username: document.getElementById('input-username').value,
                avatar_url: document.getElementById('input-avatar').value,
                bio: document.getElementById('input-bio').value,
                github_url: document.getElementById('input-github').value,
                twitter_url: document.getElementById('input-twitter').value,
                web_url: document.getElementById('input-web').value
            };

            fetch("api/kullanici_guncelle.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(veri)
            }).then(() => {
                modalKapat();
                document.getElementById('yukleniyor').classList.remove('d-none');
                document.getElementById('profil-alani').classList.add('d-none');
                kullaniciVeYazilariGetir(); 
            });
        }

        if(document.getElementById('sidebarToggleBtn')) {
            document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                document.getElementById('mainSidebar').classList.toggle('collapsed');
            });
        }
    </script>
</body>
</html>