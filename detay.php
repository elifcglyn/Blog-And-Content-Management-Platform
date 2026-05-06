<?php 
  require_once 'auth.php'; 
  require_once 'api/baglanti.php';
  
  $activePage = ''; 
  $pageTitle = 'Hikaye Detayı'; 
  
  $kullanici_id = $_SESSION['kullanici_id'] ?? null; 
  $secilen_yazi_id = $_GET['id'] ?? 0;

  $begenmis_mi = false;
  $kaydetmis_mi = false;
  $gercek_begeni_sayisi = 0;
  $gercek_yorum_sayisi = 0;

  if ($secilen_yazi_id > 0) {
      try {
          // GÜVENİLİR SAYIM: Doğrudan tablodaki satırları sayar (Asla şaşmaz)
          $begeniSorgu = $db->prepare("SELECT COUNT(*) FROM likes WHERE yazi_id = ?");
          $begeniSorgu->execute([$secilen_yazi_id]);
          $gercek_begeni_sayisi = $begeniSorgu->fetchColumn();

          $yorumSorgu = $db->prepare("SELECT COUNT(*) FROM comments WHERE yazi_id = ?");
          $yorumSorgu->execute([$secilen_yazi_id]);
          $gercek_yorum_sayisi = $yorumSorgu->fetchColumn();

          if ($kullanici_id) {
              $bSorgu = $db->prepare("SELECT id FROM likes WHERE kullanici_id = ? AND yazi_id = ?");
              $bSorgu->execute([$kullanici_id, $secilen_yazi_id]);
              if ($bSorgu->rowCount() > 0) $begenmis_mi = true;

              $kSorgu = $db->prepare("SELECT id FROM bookmarks WHERE kullanici_id = ? AND yazi_id = ?");
              $kSorgu->execute([$kullanici_id, $secilen_yazi_id]);
              if ($kSorgu->rowCount() > 0) $kaydetmis_mi = true;
          }
      } catch (PDOException $e) { }
  }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsdiff/5.1.0/diff.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }

        .cover-image-container { aspect-ratio: 16/9; border-radius: 2.5rem; overflow: hidden; border: 1px solid #f1f5f9; }
        .cover-image-container img { width: 100%; height: 100%; object-fit: cover; }
        .post-content { font-size: 1.15rem; line-height: 1.8; color: #334155; white-space: pre-wrap; }
        .modern-card { border-radius: 2rem; border: 1px solid #f1f5f9; background: #f8fafc; }
        
        .surum-kart:hover { border-color: #0d9488 !important; transform: translateY(-2px); }
        .text-teal { color: #0d9488 !important; }
        .avatar-img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #f1f5f9; }
        
        .interaction-bar { background: #fff; border: 1px solid #f1f5f9; border-radius: 2rem; padding: 0.5rem 1rem; display: inline-flex; gap: 1rem; }
        .btn-interact { background: none; border: none; color: #64748b; display: flex; align-items: center; gap: 0.5rem; transition: 0.2s; padding: 0.5rem 1rem; border-radius: 1.5rem; }
        .btn-interact:hover { background: #f8fafc; color: #0f172a; }
        .btn-interact.active-like { color: #ef4444; background: #fef2f2; }
        .btn-interact.active-save { color: #0d9488; background: #f0fdfa; } 
        
        .comment-box { border-radius: 1.5rem; border: 1px solid #e2e8f0; resize: none; font-size: 1rem; padding: 1rem; }
        .comment-box:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1); outline: none; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <div class="mb-4">
                        <button onclick="window.history.back()" class="btn btn-link text-decoration-none text-secondary fw-bold p-0">
                            <i class="fa-solid fa-arrow-left me-2"></i> Geri Dön
                        </button>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border" role="status" style="color: #0d9488;"></div>
                        <p class="serif-italic mt-3 text-muted fs-5">Hikaye yükleniyor...</p>
                    </div>

                    <div class="row g-5 d-none" id="icerik-alani" style="max-width: 1200px; margin: 0 auto;">
                        
                        <div class="col-lg-8">
                            <header class="mb-5">
                                <h1 class="serif-italic fw-bold text-dark mb-4" id="yazi-baslik" style="font-size: 3.8rem; letter-spacing: -1.5px; line-height: 0.9;">...</h1>
                                
                                <div class="py-4 border-top border-bottom mt-4 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="" id="yazar-avatar" class="avatar-img">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" id="yazar-ismi">Yazar Yükleniyor...</span>
                                            <span class="text-muted small" id="yazi-tarih">...</span>
                                        </div>
                                    </div>
                                    
                                    <div class="interaction-bar">
                                        <?php
                                            $btnLikeClass = $begenmis_mi ? 'active-like' : '';
                                            $iconLikeClass = $begenmis_mi ? 'fa-solid' : 'fa-regular';

                                            $btnSaveClass = $kaydetmis_mi ? 'active-save' : '';
                                            $iconSaveClass = $kaydetmis_mi ? 'fa-solid' : 'fa-regular';
                                        ?>
                                        <button class="btn-interact <?= $btnLikeClass ?>" id="btn-like" onclick="likeToggle()">
                                            <i class="<?= $iconLikeClass ?> fa-heart fs-5" id="like-icon"></i> 
                                            <!-- ARTIK JS SIFIRLAMAYACAK, DOĞRUDAN PHP'NİN VERİSİ GÖRÜNECEK -->
                                            <span class="fw-bold" id="like-count"><?= $gercek_begeni_sayisi ?></span>
                                        </button>
                                        <button class="btn-interact" onclick="document.getElementById('yorum-alani').scrollIntoView({behavior: 'smooth'})">
                                            <i class="fa-regular fa-comment fs-5"></i>
                                            <span class="fw-bold" id="comment-count"><?= $gercek_yorum_sayisi ?></span>
                                        </button>
                                        <button class="btn-interact <?= $btnSaveClass ?>" id="btn-save" onclick="saveToggle()">
                                            <i class="<?= $iconSaveClass ?> fa-bookmark fs-5" id="save-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </header>

                            <article>
                                <div class="cover-image-container mb-5">
                                    <img src="" id="yazi-resim" alt="Kapak">
                                </div>
                                <div class="post-content" id="yazi-metni"></div>
                            </article>
                            
                            <section id="yorum-alani" class="mt-5 pt-5 border-top">
                                <h3 class="serif-italic fw-bold mb-4">Hikayeye Yorum Yap</h3>
                                <div class="d-flex gap-3 mb-5">
                                    <img src="<?= $_SESSION['avatar_url'] ?? 'https://ui-avatars.com/api/?name=User&background=f1f5f9' ?>" class="avatar-img" style="width: 40px; height: 40px;">
                                    <div class="flex-grow-1">
                                        <textarea id="yorum-input" class="form-control comment-box mb-3" rows="3" placeholder="Bu hikaye hakkında ne düşünüyorsun?"></textarea>
                                        <div class="text-end">
                                            <button onclick="yorumGonder()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">Yanıtla</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="yorumlar-listesi"></div>
                            </section>

                        </div>

                        <aside class="col-lg-4">
                            <div class="modern-card p-4 sticky-top" style="top: 100px;" id="versiyon-listesi">
                                <div class="text-center text-muted small py-4">Sürüm geçmişi yükleniyor...</div>
                            </div>
                        </aside>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const yaziId = urlParams.get('id');
        let orijinalMetin = ""; 

        document.addEventListener("DOMContentLoaded", () => {
            if (yaziId) yaziyiGetir();
        });

        function yaziyiGetir() {
            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(data => {
                    const yazi = data.find(p => p.id == yaziId);
                    if (yazi) {
                        document.getElementById('yukleniyor').classList.add('d-none');
                        document.getElementById('icerik-alani').classList.remove('d-none');
                        
                        document.getElementById('yazi-baslik').innerText = yazi.baslik;
                        orijinalMetin = yazi.icerik;
                        document.getElementById('yazi-metni').innerHTML = orijinalMetin;
                        document.getElementById('yazi-resim').src = yazi.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000";
                        
                        const tarih = yazi.yayin_tarihi || yazi.olusturulma_tarihi;
                        let tarihMetni = "...";
                        try { if(tarih) tarihMetni = new Date(tarih.replace(' ', 'T')).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' }); } catch(e) {}
                        document.getElementById('yazi-tarih').innerText = tarihMetni;

                        const isim = yazi.yazar_adi || yazi.yazar_ismi || "Bilinmeyen Yazar";
                        document.getElementById('yazar-ismi').innerText = isim;
                        
                        const avatarPath = yazi.yazar_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(isim)}&background=0d9488&color=fff`;
                        document.getElementById('yazar-avatar').src = avatarPath;
                        
                        // JAVASCRIPT'IN SAYILARI SIFIRLADIĞI KODLAR BURADAN TAMAMEN SİLİNDİ!

                        surumleriYukle(); 
                        yorumlariGetir();
                    }
                });
        }

        function likeToggle() {
            const btn = document.getElementById('btn-like');
            const icon = document.getElementById('like-icon');
            const count = document.getElementById('like-count');
            
            let isLiked = btn.classList.contains('active-like');
            
            if(!isLiked) {
                btn.classList.add('active-like');
                icon.classList.replace('fa-regular', 'fa-solid');
                count.innerText = parseInt(count.innerText) + 1;
            } else {
                btn.classList.remove('active-like');
                icon.classList.replace('fa-solid', 'fa-regular');
                count.innerText = parseInt(count.innerText) - 1;
            }

            fetch('api/begeni_islem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `yazi_id=${yaziId}`
            })
            .then(res => res.json())
            .then(data => { if(data.status === 'error') console.error("Hata:", data.message); })
            .catch(err => console.error("Bağlantı hatası:", err));
        }

        function saveToggle() {
            const btn = document.getElementById('btn-save');
            const icon = document.getElementById('save-icon');
            let isSaved = btn.classList.contains('active-save');

            if(!isSaved) {
                btn.classList.add('active-save');
                icon.classList.replace('fa-regular', 'fa-solid');
            } else {
                btn.classList.remove('active-save');
                icon.classList.replace('fa-solid', 'fa-regular');
            }

            fetch('api/kaydet_islem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `yazi_id=${yaziId}`
            })
            .then(res => res.json())
            .then(data => { if(data.status === 'error') console.error("Hata:", data.message); })
            .catch(err => console.error("Bağlantı hatası:", err));
        }

        function yorumGonder() {
            const yorumMetni = document.getElementById('yorum-input').value;
            if(yorumMetni.trim() === '') return alert('Lütfen bir yorum yazın.');

            fetch('api/yorumlar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ yazi_id: yaziId, icerik: yorumMetni })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'error') {
                    console.error("Yorum Kayıt Hatası:", data.message);
                    alert("Yorum eklenirken hata oluştu.");
                    return;
                }
                const count = document.getElementById('comment-count');
                count.innerText = parseInt(count.innerText) + 1;
                document.getElementById('yorum-input').value = '';
                
                yorumlariGetir();
            })
            .catch(err => console.error("Bağlantı hatası:", err));
        }

        function yorumlariGetir() {
            fetch(`api/yorumlar.php?yazi_id=${yaziId}`)
                .then(res => res.json())
                .then(yorumlar => {
                    const liste = document.getElementById('yorumlar-listesi');
                    liste.innerHTML = '';
                    
                    if(yorumlar.status === 'error') {
                        console.error(yorumlar.message);
                        return;
                    }

                    if(!Array.isArray(yorumlar) || yorumlar.length === 0) {
                        liste.innerHTML = '<p class="text-muted small">Henüz yorum yapılmamış. İlk yorumu sen yap!</p>';
                        return;
                    }

                    // Yorum sayısını gerçek array uzunluğu ile de garantiye alıyoruz
                    document.getElementById('comment-count').innerText = yorumlar.length;

                    yorumlar.forEach(y => {
                        const avatar = y.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(y.ad_soyad)}&background=0d9488&color=fff`;
                        let tarih = "...";
                        try { if(y.tarih) tarih = new Date(y.tarih.replace(' ', 'T')).toLocaleString('tr-TR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }); } catch(e) {}
                        
                        liste.innerHTML += `
                            <div class="d-flex gap-3 mb-4">
                                <img src="${avatar}" class="avatar-img" style="width: 40px; height: 40px;">
                                <div class="flex-grow-1">
                                    <div class="bg-light p-3 rounded-4 border">
                                        <h6 class="fw-bold mb-1">${y.ad_soyad}</h6>
                                        <p class="mb-0 text-secondary" style="font-size: 0.95rem;">${y.icerik}</p>
                                    </div>
                                    <small class="text-muted ms-2 mt-1 d-block fw-bold" style="font-size: 0.7rem;">${tarih}</small>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => console.error("Yorum çekme hatası:", err));
        }

        function surumleriYukle() {
            fetch(`api/surumleri_getir.php?yazi_id=${yaziId}`)
                .then(res => res.json())
                .then(surumler => {
                    const liste = document.getElementById('versiyon-listesi'); 
                    liste.innerHTML = '<h5 class="serif-italic mb-4 fw-bold"><i class="fa-solid fa-clock-rotate-left text-teal me-2"></i> Yazı Geçmişi</h5>';

                    if(!surumler || surumler.length === 0) {
                        liste.innerHTML += '<p class="text-muted small">Henüz düzenleme geçmişi yok.</p>';
                        return;
                    }

                    surumler.forEach((s) => {
                        let tarih = "...";
                        try { tarih = new Date(s.tarih.replace(' ', 'T')).toLocaleString('tr-TR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }); } catch(e) {}
                        liste.innerHTML += `
                            <div class="p-3 bg-white rounded-4 border shadow-sm mb-2 surum-kart" 
                                 style="cursor:pointer; transition: 0.2s;" 
                                 onclick="surumOnizle('${btoa(unescape(encodeURIComponent(s.icerik)))}')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-teal small">${s.surum_numarasi}</span>
                                    <span class="text-muted" style="font-size: 0.65rem;">${tarih}</span>
                                </div>
                                <p class="text-secondary mb-0 mt-1" style="font-size: 0.7rem;">Değişiklikleri renklendir</p>
                            </div>`;
                    });
                });
        }

        function temizle(html) {
            let doc = new DOMParser().parseFromString(html, 'text/html');
            return doc.body.textContent || "";
        }

        function surumOnizle(encodedContent) {
            if (typeof Diff === 'undefined') return;
            const hamEski = decodeURIComponent(escape(atob(encodedContent)));
            const eskiSafMetin = temizle(hamEski);
            const guncelSafMetin = temizle(orijinalMetin);
            const farklar = Diff.diffChars(eskiSafMetin, guncelSafMetin);
            
            const anaAlan = document.getElementById('yazi-metni');
            anaAlan.innerHTML = ''; 

            const kutu = document.createElement('div');
            kutu.className = 'p-4 border rounded-4 bg-light shadow-sm';
            kutu.style.whiteSpace = 'pre-wrap';
            kutu.style.fontSize = '1.2rem';

            farklar.forEach((part) => {
                const span = document.createElement('span');
                if (part.added) {
                    span.style.backgroundColor = '#dcfce7'; 
                    span.style.color = '#166534';
                    span.style.fontWeight = 'bold';
                    span.style.padding = '2px';
                    span.innerText = part.value;
                    kutu.appendChild(span);
                } else if (part.removed) {
                    return; 
                } else {
                    kutu.appendChild(document.createTextNode(part.value));
                }
            });
            anaAlan.appendChild(kutu);
        }
    </script>
</body>
</html>