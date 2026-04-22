<?php 
  $activePage = 'home'; 
  $pageTitle = 'Hikaye Detayı'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsdiff/5.1.0/diff.min.js"></script>

    <style>
        .cover-image-container {
            aspect-ratio: 16/9;
            border-radius: 2.5rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #f1f5f9;
        }
        .cover-image-container img { width: 100%; height: 100%; object-fit: cover; }
        .post-content { font-size: 1.15rem; line-height: 1.8; color: #334155; white-space: pre-wrap; }
        .modern-card { border-radius: 2rem; border: 1px solid #f1f5f9; background: #f8fafc; }
        
        /* Yeni eklenen kısımların boyanacağı stil */
        .diff-added { 
            background-color: #dcfce7 !important; 
            color: #166534 !important; 
            font-weight: bold; 
            padding: 0 2px; 
            border-radius: 4px;
        }
        .surum-kart:hover { border-color: #0d9488 !important; transform: translateY(-2px); }
        .text-teal { color: #0d9488 !important; }
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
                        <div class="spinner-border text-teal" role="status"></div>
                        <p class="serif-italic mt-3 text-muted fs-5">Hikaye yükleniyor...</p>
                    </div>

                    <div class="row g-5 d-none" id="icerik-alani" style="max-width: 1200px; margin: 0 auto;">
                        
                        <div class="col-lg-8">
                            <header class="mb-5">
                                <h1 class="serif-italic display-4 fw-bold text-dark mb-4" id="yazi-baslik">...</h1>
                                <div class="py-4 border-top border-bottom mt-4 d-flex align-items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Admin&background=0d9488&color=fff" class="avatar" style="width:48px; height:48px; border-radius:50%;">
                                    <div>
                                        <span class="fw-bold d-block">Admin</span>
                                        <span class="text-muted small" id="yazi-tarih">...</span>
                                    </div>
                                </div>
                            </header>

                            <article>
                                <div class="cover-image-container mb-5">
                                    <img src="" id="yazi-resim" alt="Kapak">
                                </div>
                                <div class="post-content" id="yazi-metni"></div>
                            </article>
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
        let orijinalMetin = ""; // Yazının veritabanındaki en güncel halini burada tutacağız

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
                        
                        // Orijinal metni hem ekrana bas hem hafızaya al
                        orijinalMetin = yazi.icerik;
                        document.getElementById('yazi-metni').innerHTML = orijinalMetin;
                        
                        document.getElementById('yazi-resim').src = yazi.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000";
                        document.getElementById('yazi-tarih').innerText = new Date(yazi.olusturulma_tarihi).toLocaleDateString('tr-TR');
                        
                        surumleriYukle(); 
                    }
                });
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
                        const tarih = new Date(s.tarih).toLocaleString('tr-TR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
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

// 1. Yardımcı Fonksiyon: HTML etiketlerini (p, h1, div vb.) tamamen siler
function temizle(html) {
    let doc = new DOMParser().parseFromString(html, 'text/html');
    return doc.body.textContent || "";
}

function surumOnizle(encodedContent) {
    if (typeof Diff === 'undefined') return;

    // 2. Eski sürümü çöz ve etiketlerden arındır
    const hamEski = decodeURIComponent(escape(atob(encodedContent)));
    const eskiSafMetin = temizle(hamEski);
    
    // 3. Güncel orijinal metni etiketlerden arındır
    const guncelSafMetin = temizle(orijinalMetin);
    
    // 4. Farkı hesapla (Karakter bazlı)
    const farklar = Diff.diffChars(eskiSafMetin, guncelSafMetin);
    
    const anaAlan = document.getElementById('yazi-metni');
    anaAlan.innerHTML = ''; // Önce ekranı temizle

    // 5. Şık bir kutu oluştur
    const kutu = document.createElement('div');
    kutu.className = 'p-4 border rounded-4 bg-light shadow-sm';
    kutu.style.whiteSpace = 'pre-wrap';
    kutu.style.fontSize = '1.2rem';

    farklar.forEach((part) => {
        const span = document.createElement('span');
        
        if (part.added) {
            // YENİ EKLENENLER (Merhaba, :) gibi kısımlar)
            span.style.backgroundColor = '#dcfce7'; // Yeşil
            span.style.color = '#166534';
            span.style.fontWeight = 'bold';
            span.style.padding = '2px';
            span.innerText = part.value;
            kutu.appendChild(span);
        } else if (part.removed) {
            // ESKİDE OLUP ŞİMDİ OLMAYANLAR (Bunları göstermiyoruz ki kafa karışmasın)
            return; 
        } else {
            // DEĞİŞMEYEN KISIMLAR (emirhan kısmı)
            kutu.appendChild(document.createTextNode(part.value));
        }
    });

    anaAlan.appendChild(kutu);
}
    </script>
</body>
</html>