<?php 
// Geliştirme aşamasındaki olası hataları ekrana basmak ve kontrol altında tutmak için hata ayıklama (error reporting) modunu açıyoruz.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Güvenlik ve veritabanı bağlantısı. Oturum açılmamışsa işlem yapılmasını engelliyoruz.
require_once 'auth.php'; 
require_once 'api/baglanti.php';

$activePage = 'bookmarks'; 
$pageTitle = 'Kaydedilenler'; 

// Tanımsız değişken (undefined) hatalarını önlemek için değerleri 'isset' ile güvenli bir şekilde alıyoruz.
$kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : 0;
$dbKayitliIdler = []; 

// PDO ile SQL Injection önlemi alarak kullanıcının daha önce "kaydettim" olarak işaretlediği yazıların ID'lerini çekiyoruz.
if ($kullanici_id > 0) {
    try {
        $kayitSorgu = $db->prepare("SELECT yazi_id FROM bookmarks WHERE kullanici_id = ?");
        $kayitSorgu->execute([$kullanici_id]);
        
        // FETCH_COLUMN kullanarak iki boyutlu karmaşık bir tablo yerine, doğrudan yazı ID'lerinden oluşan tek boyutlu bir dizi (Array) elde ediyoruz.
        $dbKayitliIdler = $kayitSorgu->fetchAll(PDO::FETCH_COLUMN); 
    } catch (PDOException $e) {
        // Hata durumunda sitenin çökmemesi için sessizce geçiyoruz.
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Tüm sayfalarda ortak olan head (CSS, Bootstrap) dosyalarını çağırıyoruz -->
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }

        /* Kart tasarımı: Üzerine gelince (hover) sayfa bütünlüğüne uygun şekilde kalkma ve gölge derinleşmesi animasyonu veriyoruz. */
        .post-card { border-radius: 1.5rem; border: 1px solid #f1f5f9; transition: 0.3s; background: white; text-decoration: none; display: block; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(20, 184, 166, 0.1); border-color: #14b8a6; }
        
        /* Kart içindeki resimlerin esnekliği için object-fit: cover kullanıyor, Media Query ile mobilde ve masaüstünde farklı davranmasını sağlıyoruz. */
        .post-img { width: 100%; height: 140px; object-fit: cover; border-radius: 1rem; }
        @media (min-width: 768px) { .post-img { width: 220px; } }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            <!-- Yan menüyü (Sidebar) dahil ediyoruz -->
            <?php include 'sidebar.php'; ?>
            
            <main class="flex-grow-1" style="min-width: 0;">
                <!-- Üst menüyü (Topbar) dahil ediyoruz -->
                <?php include 'topbar.php'; ?>
                
                <div class="px-4 px-md-5 pb-5" style="max-width: 900px; margin: 0 auto;">
                    
                    <div class="pt-2 pb-4 mb-4 border-bottom">
                        <div class="text-teal fw-bold text-uppercase mb-2" style="letter-spacing: 2px; font-size: 0.75rem;">
                            <i class="fa-solid fa-bookmark me-1"></i> Kütüphanen
                        </div>
                        <h1 class="serif-italic fw-bold text-dark mb-0" style="font-size: 3.8rem; letter-spacing: -1.5px; line-height: 0.9;">Kaydedilenler</h1>
                        <p class="text-secondary fs-5 mt-3 mb-0" id="kayit-sayisi">Daha sonra okumak için ayırdığın hikayeler yükleniyor...</p>
                    </div>

                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border text-teal" role="status"></div>
                    </div>

                    <!-- Javascript ile doldurulacak boş liste alanı -->
                    <div id="kaydedilenler-listesi" class="d-flex flex-column gap-4 d-none"></div>

                </div>
            </main>
        </div>
    </div>

    <!-- Veri Çekme, Filtreleme ve DOM İşlemleri (Slaytlardaki JS Mantığına Uygun) -->
    <script>
        // Sunucu taraflı PHP dizisini, json_encode metoduyla istemci taraflı JavaScript dizisine güvenle dönüştürüyoruz.
        var kaydedilenIdler = <?php echo json_encode($dbKayitliIdler ?: []); ?>;

        // DOM yapısı hazır olduğunda fonksiyonları ateşliyoruz. (Slaytlardaki gibi window.onload)
        window.onload = function() {
            
            // Dinamik olarak kullanıcının kaç yazı kaydettiğini başlığın altına basıyoruz.
            var sayacMetni = document.getElementById('kayit-sayisi');
            sayacMetni.innerText = "Daha sonra okumak için ayırdığın " + kaydedilenIdler.length + " hikaye.";

            // Eğer kullanıcının hiç kaydettiği yazı yoksa gereksiz yere sunucuya istek atmasını engelliyor ve boş ekran tasarımını gösteriyoruz.
            if(kaydedilenIdler.length === 0) {
                document.getElementById('yukleniyor').style.display = "none";
                document.getElementById('kaydedilenler-listesi').classList.remove('d-none');
                
                // Metin birleştirme (+) ile HTML çiziyoruz
                document.getElementById('kaydedilenler-listesi').innerHTML = 
                    "<div class='text-center py-5 bg-light rounded-4 border'>" +
                        "<i class='fa-regular fa-bookmark fs-1 text-muted mb-3'></i>" +
                        "<h5 class='serif-italic fw-bold text-dark'>Kütüphanen Boş</h5>" +
                        "<p class='text-muted'>Hikayeleri okurken sağ üstteki kaydet butonuna basarak buraya ekleyebilirsin.</p>" +
                        "<a href='kesfet.php' class='btn rounded-pill px-4 mt-2 text-white' style='background-color: #0d9488;'>Keşfetmeye Başla</a>" +
                    "</div>";
                return;
            }

            // Fetch API kullanarak tüm yazıları çekiyoruz
            fetch('api/yazilari_getir.php')
                .then(function(res) {
                    return res.json();
                })
                .then(function(yazilar) {
                    document.getElementById('yukleniyor').style.display = "none";
                    var liste = document.getElementById('kaydedilenler-listesi');
                    liste.classList.remove('d-none');

                    // ALGORİTMA: API'den gelen yazılar ile kullanıcının kaydettiği ID'leri iç içe FOR döngüleriyle eşleştiriyoruz.
                    var filtrelenmisYazilar = [];
                    var i, j;
                    for(i = 0; i < yazilar.length; i++) {
                        var yaziId = parseInt(yazilar[i].id);
                        var bulundu = false;
                        
                        // Bu yazının ID'si kaydedilenler listemizde var mı?
                        for(j = 0; j < kaydedilenIdler.length; j++) {
                            if(parseInt(kaydedilenIdler[j]) === yaziId) {
                                bulundu = true;
                                break;
                            }
                        }
                        
                        if(bulundu) {
                            filtrelenmisYazilar.push(yazilar[i]);
                        }
                    }

                    // Süzdüğümüz verileri klasik FOR döngüsü ve String birleştirme yöntemi ile DOM'a aktarıyoruz.
                    var k;
                    for(k = 0; k < filtrelenmisYazilar.length; k++) {
                        var post = filtrelenmisYazilar[k];
                        
                        // Veriler boş (null) gelirse diye standart IF kontrollerimiz:
                        var rawDate = post.olusturulma_tarihi ? post.olusturulma_tarihi : (post.yayin_tarihi ? post.yayin_tarihi : "");
                        var tarihMetni = "Tarih Yok";
                        if(rawDate !== "") { tarihMetni = rawDate; }

                        var resim = "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500";
                        if(post.kapak_resmi) { resim = post.kapak_resmi; }

                        var icerik = "";
                        if(post.icerik) { icerik = post.icerik.substring(0, 120); }

                        var yazarAdi = "Bilinmeyen Yazar";
                        if(post.yazar_adi) { yazarAdi = post.yazar_adi; } 
                        else if(post.yazar_ismi) { yazarAdi = post.yazar_ismi; }

                        // Tek tırnaklar ve artı (+) işaretleriyle DOM şablonumuzu oluşturuyoruz.
                        var htmlCard = "<a href='detay.php?id=" + post.id + "' class='post-card p-3 d-flex flex-column flex-md-row align-items-center gap-4 text-dark'>" +
                                "<img src='" + resim + "' class='post-img'>" +
                                "<div class='flex-grow-1 w-100'>" +
                                    "<h3 class='serif-italic fw-bold mb-2'>" + post.baslik + "</h3>" +
                                    "<p class='text-secondary small mb-3'>" + icerik + "...</p>" +
                                    "<div class='d-flex justify-content-between align-items-center text-muted' style='font-size: 0.75rem;'>" +
                                        "<span class='fw-bold'>" + yazarAdi + "</span>" +
                                        "<span>" + tarihMetni + " <i class='fa-solid fa-chevron-right text-teal ms-2'></i></span>" +
                                    "</div>" +
                                "</div>" +
                            "</a>";
                        
                        liste.innerHTML += htmlCard;
                    }
                })
                .catch(function(err) {
                    console.error("Fetch Hatası", err);
                });
            
            // Ortak Sidebar Butonu Scripti (Slaytlara uygun Event Binding)
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
        };
    </script>
</body>
</html>