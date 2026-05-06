<?php 
// Geliştirme aşamasındaki olası hataları ekrana basmak ve kontrol altında tutmak için hata ayıklama (error reporting) modunu açıyoruz.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum kontrolü ve veritabanı bağlantı dosyalarımızı projeye dahil ediyoruz.
require_once 'auth.php'; 
require_once 'api/baglanti.php';

$activePage = ''; 
$pageTitle = 'Hikaye Detayı'; 

// Tanımsız değişken (undefined) hatalarını önlemek için değerleri 'isset' ve 'intval' (sadece sayı) ile güvenli bir şekilde alıyoruz.
$kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : null; 
$secilen_yazi_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$begenmis_mi = false;
$kaydetmis_mi = false;
$gercek_begeni_sayisi = 0;
$gercek_yorum_sayisi = 0;

// Geçerli bir yazı ID'si geldiyse veritabanı işlemlerini başlatıyoruz.
if ($secilen_yazi_id > 0) {
    try {
        // PDO prepare ve execute kullanarak SQL Injection güvenlik açıklarını tamamen kapatıyoruz.
        // Yazıya ait toplam beğeni ve yorum sayılarını veritabanından çekiyoruz.
        $begeniSorgu = $db->prepare("SELECT COUNT(*) FROM likes WHERE yazi_id = ?");
        $begeniSorgu->execute([$secilen_yazi_id]);
        $gercek_begeni_sayisi = $begeniSorgu->fetchColumn();

        $yorumSorgu = $db->prepare("SELECT COUNT(*) FROM comments WHERE yazi_id = ?");
        $yorumSorgu->execute([$secilen_yazi_id]);
        $gercek_yorum_sayisi = $yorumSorgu->fetchColumn();

        // Eğer kullanıcı giriş yapmışsa, bu yazıyı daha önce beğenip beğenmediğini ve kaydedip kaydetmediğini sorguluyoruz.
        if ($kullanici_id) {
            $bSorgu = $db->prepare("SELECT id FROM likes WHERE kullanici_id = ? AND yazi_id = ?");
            $bSorgu->execute([$kullanici_id, $secilen_yazi_id]);
            if ($bSorgu->rowCount() > 0) $begenmis_mi = true;

            $kSorgu = $db->prepare("SELECT id FROM bookmarks WHERE kullanici_id = ? AND yazi_id = ?");
            $kSorgu->execute([$kullanici_id, $secilen_yazi_id]);
            if ($kSorgu->rowCount() > 0) $kaydetmis_mi = true;
        }
    } catch (PDOException $e) { 
        // Veritabanı tarafında bir anormallik olursa sitenin çökmemesi için try-catch bloğu ile hatayı sessizce yakalıyoruz.
    }
}

// Session'da kullanıcının avatarı yoksa, isim harflerinden otomatik ikon üreten güvenli bir varsayılan atıyoruz.
$oturum_avatar = isset($_SESSION['avatar_url']) && !empty($_SESSION['avatar_url']) ? $_SESSION['avatar_url'] : 'https://ui-avatars.com/api/?name=User&background=0d9488&color=fff';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    
    <!-- Yazı sürümlerini GitHub tarzında (fark bulucu) karşılaştırmak için JSDiff kütüphanesini projeye dahil ediyoruz. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsdiff/5.1.0/diff.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }

        .cover-image-container { aspect-ratio: 21/9; border-radius: 2.5rem; overflow: hidden; border: 1px solid #f1f5f9; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); }
        .cover-image-container img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Satır atlamalarının (Enter) ve boşlukların HTML'de bozulmadan görünmesi için white-space: pre-wrap; kullanıyoruz. */
        .post-content { font-size: 1.25rem; line-height: 1.8; color: #334155; white-space: pre-wrap; }
        .modern-card { border-radius: 2rem; border: 1px solid #f1f5f9; background: #f8fafc; }
        
        .surum-kart:hover { border-color: #0d9488 !important; transform: translateX(5px); background-color: #f0fdfa !important; }
        .text-teal { color: #0d9488 !important; }
        .avatar-img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        .interaction-bar { background: #fff; border: 1px solid #f1f5f9; border-radius: 2rem; padding: 0.5rem 1rem; display: inline-flex; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .btn-interact { background: none; border: none; color: #64748b; display: flex; align-items: center; gap: 0.5rem; transition: 0.2s; padding: 0.5rem 1rem; border-radius: 1.5rem; }
        .btn-interact:hover { background: #f8fafc; color: #0f172a; }
        
        /* Beğeni veya kaydetme durumu aktifse butonların renk değiştirmesini sağlayacak dinamik CSS sınıflarımız. */
        .btn-interact.active-like { color: #ef4444; background: #fef2f2; }
        .btn-interact.active-save { color: #0d9488; background: #f0fdfa; } 
        
        .comment-box { border-radius: 1.5rem; border: 1px solid #e2e8f0; resize: none; font-size: 1rem; padding: 1.2rem; background-color: #f8fafc; transition: 0.3s; }
        .comment-box:focus { background-color: #fff; border-color: #0d9488; box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1); outline: none; }

        /* Diff Kütüphanesi Tasarımları: Yeni eklenen kelimeler yeşil, silinen kelimeler kırmızı ve üstü çizili olacak. */
        .diff-added { background-color: #dcfce7; color: #166534; font-weight: bold; border-radius: 4px; padding: 2px 4px; }
        .diff-removed { background-color: #fee2e2; color: #991b1b; text-decoration: line-through; border-radius: 4px; padding: 2px 4px; }
        
        .preview-mode-banner { background-color: #0f172a; color: #fff; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
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
                        <!-- Tarayıcı geçmişini kullanarak geldiği sayfaya geri dönmesini sağlayan pratik JS komutu -->
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
                                <h1 class="serif-italic fw-bold text-dark mb-4" id="yazi-baslik" style="font-size: 3.5rem; letter-spacing: -1.5px; line-height: 1;">...</h1>
                                
                                <div class="py-4 border-top border-bottom mt-4 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="" id="yazar-avatar" class="avatar-img">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" id="yazar-ismi">...</span>
                                            <span class="text-muted small" id="yazi-tarih">...</span>
                                        </div>
                                    </div>
                                    
                                    <div class="interaction-bar">
                                        <!-- PHP kısa if (Ternary) yapısı ile, kullanıcı yazıyı önceden beğendiyse butonu renkli yüklüyoruz -->
                                        <button class="btn-interact <?= $begenmis_mi ? 'active-like' : '' ?>" id="btn-like" onclick="likeToggle()">
                                            <i class="<?= $begenmis_mi ? 'fa-solid' : 'fa-regular' ?> fa-heart fs-5" id="like-icon"></i> 
                                            <span class="fw-bold" id="like-count"><?= $gercek_begeni_sayisi ?></span>
                                        </button>
                                        <button class="btn-interact" onclick="document.getElementById('yorum-alani').scrollIntoView({behavior: 'smooth'})">
                                            <i class="fa-regular fa-comment fs-5"></i>
                                            <span class="fw-bold" id="comment-count"><?= $gercek_yorum_sayisi ?></span>
                                        </button>
                                        <button class="btn-interact <?= $kaydetmis_mi ? 'active-save' : '' ?>" id="btn-save" onclick="saveToggle()">
                                            <i class="<?= $kaydetmis_mi ? 'fa-solid' : 'fa-regular' ?> fa-bookmark fs-5" id="save-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </header>

                            <!-- JavaScript ile sürüm karşılaştırması tetiklendiğinde DOM üzerinden görünür hale getirilecek bilgilendirme banner'ı -->
                            <div id="preview-banner" class="preview-mode-banner d-none">
                                <span class="small fw-bold"><i class="fa-solid fa-eye me-2" style="color:#38bdf8;"></i> Şu an bir sürüm farkını inceliyorsunuz.</span>
                                <button onclick="resetView()" class="btn btn-light btn-sm rounded-pill fw-bold text-dark">Orijinale Dön</button>
                            </div>

                            <article>
                                <div class="cover-image-container mb-5">
                                    <img src="" id="yazi-resim" alt="Kapak">
                                </div>
                                <div class="post-content" id="yazi-metni"></div>
                            </article>
                            
                            <section id="yorum-alani" class="mt-5 pt-5 border-top">
                                <h3 class="serif-italic fw-bold mb-4">Hikayeye Yorum Yap</h3>
                                <div class="d-flex gap-4 mb-5 p-4 bg-light rounded-4">
                                    <img src="<?= $oturum_avatar ?>" class="avatar-img">
                                    <div class="flex-grow-1">
                                        <textarea id="yorum-input" class="form-control comment-box mb-3 shadow-none" rows="3" placeholder="Bu hikaye hakkında ne düşünüyorsun?"></textarea>
                                        <div class="text-end">
                                            <button onclick="yorumGonder()" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow-sm" style="background-color: #0f172a; border:none;">Yanıtla</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="yorumlar-listesi"></div>
                            </section>
                        </div>

                        <aside class="col-lg-4">
                            <!-- Sayfa aşağı kaydırıldığında da bu geçmiş listesinin ekranda sabit kalması için sticky-top kullanıyoruz -->
                            <div class="modern-card p-4 sticky-top shadow-sm" style="top: 100px; background: white; border: 1px solid #f1f5f9;" id="versiyon-listesi">
                                <h5 class="serif-italic mb-4 fw-bold border-bottom pb-3"><i class="fa-solid fa-clock-rotate-left text-teal me-2"></i> Yazı Geçmişi</h5>
                                <div class="text-center text-muted small py-4">Sürüm geçmişi yükleniyor...</div>
                            </div>
                        </aside>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Veri Çekme ve DOM İşlemleri -->
    <script>
        // Adres çubuğundaki parametreleri ayrıştırmak için JS'in yerleşik URLSearchParams nesnesini kullanıyoruz. (Örn: ?id=15)
        const urlParams = new URLSearchParams(window.location.search);
        const yaziId = urlParams.get('id');
        
        let orijinalIcerikHtml = ""; 
        let tumSurumler = []; 

        // Sayfanın DOM yapısı (HTML elementleri) tamamen okunduktan sonra veritabanı isteğini ateşliyoruz.
        document.addEventListener("DOMContentLoaded", () => {
            if (yaziId) yaziyiGetir();
        });

        function yaziyiGetir() {
            // Veriyi güncel API üzerinden AJAX (Fetch) yöntemiyle sayfa yenilenmeden çekiyoruz.
            fetch(`api/tek_yazi_getir.php?id=${yaziId}`)
                .then(res => res.json())
                .then(yazi => {
                    if (yazi && !yazi.hata) {
                        document.getElementById('yukleniyor').classList.add('d-none');
                        document.getElementById('icerik-alani').classList.remove('d-none');
                        
                        document.getElementById('yazi-baslik').innerText = yazi.baslik;
                        
                        // Sürüm geçmişine gidip gelindiğinde veriyi tekrar API'den çekmemek için, asıl metni global değişkene yedekliyoruz.
                        orijinalIcerikHtml = yazi.icerik;
                        document.getElementById('yazi-metni').innerHTML = orijinalIcerikHtml;
                        document.getElementById('yazi-resim').src = yazi.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000";
                        
                        // Veritabanı tarihi boşluklu geldiğinde bazı tarayıcılar (Safari vb.) hata verebilir. 'T' ile replace edip ISO formatına uygunluyoruz.
                        let tarihMetni = "...";
                        try { if(yazi.yayin_tarihi) tarihMetni = new Date(yazi.yayin_tarihi.replace(' ', 'T')).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' }); } catch(e) {}
                        document.getElementById('yazi-tarih').innerText = tarihMetni;

                        const isim = yazi.yazar_adi || yazi.yazar_ismi || "Bilinmeyen Yazar";
                        document.getElementById('yazar-ismi').innerText = isim;
                        
                        const encodedIsim = encodeURIComponent(isim);
                        document.getElementById('yazar-avatar').src = yazi.yazar_avatar || `https://ui-avatars.com/api/?name=${encodedIsim}&background=0d9488&color=fff`;
                        
                        // Makale iskeleti yüklendiğine göre, bağlantılı sürümleri ve yorumları getiren fonksiyonları çağırabiliriz.
                        surumleriYukle(); 
                        yorumlariGetir();
                    } else {
                        // Backend'den hata dönerse veya makale silindiyse kullanıcıya anlaşılır bir hata mesajı basıyoruz.
                        document.getElementById('yukleniyor').innerHTML = `
                            <div class="alert alert-danger mx-auto mt-4" style="max-width: 500px; border-radius: 1rem;">
                                <strong>Hata:</strong> ${yazi.hata || 'Yazı bulunamadı.'}
                            </div>`;
                    }
                })
                .catch(err => {
                    console.error("Fetch Hatası:", err);
                    document.getElementById('yukleniyor').innerHTML = `
                        <div class="alert alert-danger mx-auto mt-4" style="max-width: 500px; border-radius: 1rem;">
                            Sunucu ile iletişim kurulamadı. (500 Hatası Çözümü Devrede)
                        </div>`;
                });
        }

        // --- GITHUB BENZERİ VERSİYON KONTROL SİSTEMİ MANTIĞI ---
        function surumleriYukle() {
            fetch(`api/surumleri_getir.php?yazi_id=${yaziId}`)
                .then(res => res.json())
                .then(surumler => {
                    // Tüm sürümleri sürekli API'ye gitmemek için bellekteki diziye kaydediyoruz.
                    tumSurumler = surumler; 
                    const liste = document.getElementById('versiyon-listesi'); 
                    liste.innerHTML = '<h5 class="serif-italic mb-4 fw-bold border-bottom pb-3"><i class="fa-solid fa-clock-rotate-left text-teal me-2"></i> Yazı Geçmişi</h5>';

                    if(!surumler || surumler.length === 0) {
                        liste.innerHTML += '<p class="text-muted small">Düzenleme geçmişi bulunmuyor.</p>';
                        return;
                    }

                    // Dinamik HTML oluşturup onclick olayına sürüm id'sini parametre olarak yolluyoruz.
                    surumler.forEach((s) => {
                        let tarih = "...";
                        try { tarih = new Date(s.tarih.replace(' ', 'T')).toLocaleString('tr-TR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }); } catch(e) {}
                        
                        liste.innerHTML += `
                            <div class="p-3 bg-white rounded-4 border shadow-sm mb-3 surum-kart" 
                                 style="cursor:pointer; transition: 0.3s;" 
                                 onclick="surumOnizle(${s.id}, '${s.surum_numarasi}')">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-teal-light text-teal rounded-pill px-2" style="font-size: 0.65rem;">${s.surum_numarasi}</span>
                                    <span class="text-muted fw-bold" style="font-size: 0.6rem;">${tarih}</span>
                                </div>
                                <p class="text-dark small mb-0 fw-bold" style="font-size: 0.75rem;">${s.degisiklik_notu || 'Güncelleme'}</p>
                                <span class="text-muted" style="font-size: 0.65rem;">Değişiklikleri karşılaştır</span>
                            </div>`;
                    });
                });
        }

        // Karşılaştırma yaparken kütüphanenin HTML etiketlerine (örneğin <br>, <p>) takılmaması için saf metni filtreleyen güvenlik fonksiyonu
        function htmlToText(html) {
            let div = document.createElement('div');
            div.innerHTML = html;
            return (div.innerText || div.textContent || "").replace(/\s+/g, ' ').trim();
        }

        // Tıklanan sürüm ile canlı sürüm arasındaki farkı (Diff) kelime kelime bulan algoritma
        function surumOnizle(surumId, surumNo) {
            if (typeof Diff === 'undefined') return alert("Diff kütüphanesi yüklenemedi.");
            
            const secilenSurum = tumSurumler.find(s => s.id == surumId);
            if(!secilenSurum) return;

            const eskiMetin = htmlToText(secilenSurum.icerik);
            const guncelMetin = htmlToText(orijinalIcerikHtml);

            // GÜVENLİK/UX KONTROLÜ: Eğer düzenlemede metne hiç dokunulmamışsa (örn: sadece başlık değişmişse) uyar
            if (eskiMetin === guncelMetin) {
                alert(surumNo + " sürümü ile şu anki canlı yazı arasında metin farkı yok!");
                return;
            }

            // JSDiff kütüphanesi bize kelime kelime eklenenleri ve çıkarılanları analiz edip bir Array döner.
            const farklar = Diff.diffWords(eskiMetin, guncelMetin);
            
            const anaAlan = document.getElementById('yazi-metni');
            const banner = document.getElementById('preview-banner');
            anaAlan.innerHTML = ''; 
            banner.classList.remove('d-none');

            const container = document.createElement('div');
            container.className = 'p-4 rounded-4 bg-light border';
            container.style.fontSize = '1.25rem';
            container.style.lineHeight = '1.8';

            // Dönen sonuç dizisini inceliyoruz. Kelime eklenmişse yeşil (diff-added), silinmişse kırmızı (diff-removed) stili atıyoruz.
            farklar.forEach((part) => {
                const span = document.createElement('span');
                if (part.added) {
                    span.className = 'diff-added'; 
                    span.innerText = part.value;
                    container.appendChild(span);
                } else if (part.removed) {
                    span.className = 'diff-removed'; 
                    span.innerText = part.value;
                    container.appendChild(span);
                } else {
                    // Metin değişmemişse saf haliyle ekliyoruz
                    container.appendChild(document.createTextNode(part.value)); 
                }
            });

            anaAlan.appendChild(container);
            window.scrollTo({top: 0, behavior: 'smooth'}); // İnceleme için sayfayı en üste kaydır
        }

        // İnceleme modundan çıkıp ilk yedeğini aldığımız canlı ve tasarımlı HTML verisini tekrar basıyoruz
        function resetView() {
            document.getElementById('yazi-metni').innerHTML = orijinalIcerikHtml;
            document.getElementById('preview-banner').classList.add('d-none');
        }

        // --- ASENKRON ETKİLEŞİM İŞLEMLERİ ---
        // Beğeni butonuna basıldığında sayfa yenilenmeden DOM üzerinde class değiştirerek animasyon hissi veriyoruz
        function likeToggle() {
            const count = document.getElementById('like-count');
            const btn = document.getElementById('btn-like');
            const icon = document.getElementById('like-icon');
            let isLiked = btn.classList.contains('active-like');
            
            btn.classList.toggle('active-like');
            icon.classList.toggle('fa-solid'); icon.classList.toggle('fa-regular');
            
            // Eğer buton zaten aktifse sayıyı 1 düşür, değilse 1 artır.
            count.innerText = parseInt(count.innerText) + (isLiked ? -1 : 1);

            // Değişikliği veritabanına kaydetmesi için API'ye arka planda POST isteği atıyoruz.
            fetch('api/begeni_islem.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `yazi_id=${yaziId}` });
        }

        function saveToggle() {
            const btn = document.getElementById('btn-save');
            const icon = document.getElementById('save-icon');
            btn.classList.toggle('active-save');
            icon.classList.toggle('fa-solid'); icon.classList.toggle('fa-regular');

            fetch('api/kaydet_islem.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `yazi_id=${yaziId}` });
        }

        function yorumGonder() {
            const input = document.getElementById('yorum-input');
            if(!input.value.trim()) return; // Sadece boşluk gönderilmesini engelliyoruz.

            // Kullanıcının metnini JSON objesi haline getirip gönderiyoruz.
            fetch('api/yorumlar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ yazi_id: yaziId, icerik: input.value })
            })
            .then(res => res.json())
            .then(data => {
                // Yorum başarılıysa üstteki sayacı 1 artırıp listeyi yeniden fetch ederek güncelliyoruz.
                document.getElementById('comment-count').innerText = parseInt(document.getElementById('comment-count').innerText) + 1;
                input.value = '';
                yorumlariGetir();
            });
        }

        function yorumlariGetir() {
            fetch(`api/yorumlar.php?yazi_id=${yaziId}`)
                .then(res => res.json())
                .then(yorumlar => {
                    const liste = document.getElementById('yorumlar-listesi');
                    liste.innerHTML = '';
                    if(!Array.isArray(yorumlar) || yorumlar.length === 0) {
                        liste.innerHTML = '<p class="text-muted small">Henüz yorum yapılmamış.</p>';
                        return;
                    }
                    yorumlar.forEach(y => {
                        const avatar = y.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(y.ad_soyad)}&background=0d9488&color=fff`;
                        liste.innerHTML += `
                            <div class="d-flex gap-3 mb-4">
                                <img src="${avatar}" class="avatar-img" style="width: 40px; height: 40px;">
                                <div class="flex-grow-1">
                                    <div class="bg-light p-3 rounded-4 border">
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">${y.ad_soyad}</h6>
                                        <p class="mb-0 text-secondary" style="font-size: 0.95rem;">${y.icerik}</p>
                                    </div>
                                </div>
                            </div>`;
                    });
                });
        }
    </script>
</body>
</html>