<?php 
  // Oturum açmamış kullanıcıların bu sayfaya erişmesini engellemek için auth.php'yi ilk satırda çağırıyoruz.
  require_once 'auth.php'; 
  $activePage = 'yazilarim'; 
  $pageTitle = 'Yazılarım'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Tüm sayfalardaki ortak head etiketlerini (CSS, Bootstrap linkleri) tek bir dosyadan çekiyoruz. -->
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
    <style>
        /* Kartın normal ve üzerine gelindiğindeki (hover) animasyonlu görünümü */
        .post-card { border-radius: 1.5rem; border: 1px solid #f1f5f9; transition: 0.3s; background: white; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(0,0,0,0.05); border-color: #0d9488; }
        
        .post-img { width: 100%; height: 140px; object-fit: cover; border-radius: 1.2rem; }
        /* Ekran boyutu 768px'in üzerindeyse resmi yatayda küçülterek esnek (responsive) bir tasarım sağlıyoruz. */
        @media (min-width: 768px) { .post-img { width: 180px; } }
        
        .btn-edit { color: #0d9488; background: #f0fdfa; border: 1px solid #ccfbf1; }
        .btn-edit:hover { background: #0d9488; color: white; }
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            <!-- Yan menüyü (Sidebar) PHP ile dinamik olarak sayfaya dahil ediyoruz. -->
            <?php include 'sidebar.php'; ?>
            
            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                <!-- Üst menüyü (Topbar) sayfaya dahil ediyoruz. -->
                <?php include 'topbar.php'; ?>
                
                <div class="px-4 px-md-5 pb-5" style="max-width: 1000px; margin: 0 auto;">
                    
                    <div class="mb-5 border-bottom pb-4 mt-4">
                        <div class="text-teal fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 2px; color: #0d9488;">
                            <i class="fa-solid fa-layer-group me-1"></i> İçerik Yönetimi
                        </div>
                        <h1 class="serif-italic display-5 fw-bold text-dark">Yazılarım</h1>
                        <p class="text-secondary fs-6">Sadece senin tarafından yayınlanan hikayeler burada listelenir.</p>
                    </div>

                    <!-- Veriler API'den gelene kadar ekranda dönecek olan yükleme animasyonu (Spinner) -->
                    <div id="yukleniyor" class="text-center py-5">
                        <div class="spinner-border" role="status" style="color: #0d9488;"></div>
                        <p class="serif-italic mt-3 text-muted">Senin hikayelerin yükleniyor...</p>
                    </div>

                    <!-- JavaScript ile dinamik olarak doldurulacak boş liste kapsayıcısı -->
                    <div id="yazilar-listesi" class="d-flex flex-column gap-4" style="display: none;"></div>

                </div>
            </main>
        </div>
    </div>

    <!-- Veritabanı ve DOM İşlemleri (Slaytlardaki JS Mantığına Uygun) -->
    <script>
        // Giriş yapan aktif kullanıcının ID'sini PHP Session üzerinden alıp JavaScript değişkenine aktarıyoruz.
        var KULLANICI_ID = <?= $_SESSION['kullanici_id'] ?>;

        // HTML iskeleti hazır olduğunda veri çekme fonksiyonunu tetikliyoruz.
        window.onload = function() {
            yazilariGetir();
        };

        function yazilariGetir() {
            // Sunucudaki tüm yazıları JSON formatında getiren API'ye AJAX (fetch) isteği atıyoruz.
            fetch('api/yazilari_getir.php')
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    
                    // ALGORİTMA: Tüm veriler içinden sadece giriş yapan kullanıcının yazılarını bulmak için bir for döngüsü açtık.
                    var yazilar = [];
                    var i;
                    for (i = 0; i < data.length; i++) {
                        if (parseInt(data[i].yazar_id) === parseInt(KULLANICI_ID)) {
                            yazilar.push(data[i]); // Eşleşenleri yeni dizimize ekliyoruz.
                        }
                    }

                    // Veriler geldiği için yükleniyor animasyonunu DOM'dan gizliyoruz.
                    document.getElementById('yukleniyor').style.display = "none";
                    var liste = document.getElementById('yazilar-listesi');
                    liste.style.display = "flex";
                    liste.innerHTML = ""; 

                    // Eğer kullanıcının hiç yazısı yoksa, onu yeni yazı sayfasına yönlendiren uyarıyı basıyoruz.
                    if (yazilar.length === 0) {
                        liste.innerHTML = "<div class='text-center py-5 bg-light rounded-4 border'>" +
                            "<h4 class='serif-italic text-muted'>Henüz hiç yazı paylaşmamışsın.</h4>" +
                            "<a href='yeni-yazi.php' class='btn text-white rounded-pill px-4 mt-3 fw-bold' style='background-color: #0d9488;'>İlk Hikayeni Yaz</a>" +
                            "</div>";
                        return;
                    }

                    // Kullanıcının yazıları varsa, for döngüsü ile dönüp her birini HTML kartı olarak oluşturuyoruz.
                    var j;
                    for (j = 0; j < yazilar.length; j++) {
                        var post = yazilar[j];

                        // Tarih, resim ve özet verileri boş gelebilme ihtimaline karşı standart If kontrolleri ile önlem alıyoruz.
                        var tarih = "Tarih Yok";
                        if (post.yayin_tarihi) {
                            tarih = post.yayin_tarihi;
                        }

                        var resim = "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=500";
                        if (post.kapak_resmi) {
                            resim = post.kapak_resmi;
                        }

                        var ozet = "";
                        if (post.ozet) {
                            ozet = post.ozet;
                        } else if (post.icerik) {
                            ozet = post.icerik.substring(0, 120) + "..."; // İçeriğin sadece ilk 120 karakterini al
                        }

                        // DOM Manipülasyonu: Metin birleştirme operatörü (+) ile HTML kartımızı çiziyoruz.
                        var htmlCard = "<div class='post-card p-3 d-flex flex-column flex-md-row align-items-center gap-4'>" +
                            "<img src='" + resim + "' class='post-img shadow-sm'>" +
                            "<div class='flex-grow-1 w-100'>" +
                                "<div class='d-flex align-items-center gap-2 mb-1'>" +
                                    "<span class='badge rounded-pill bg-light text-dark border fw-medium' style='font-size: 0.65rem;'>ID: #" + post.id + "</span>" +
                                    "<span class='text-muted' style='font-size: 0.75rem;'><i class='fa-regular fa-calendar me-1'></i> " + tarih + "</span>" +
                                "</div>" +
                                "<h4 class='serif-italic fw-bold text-dark mb-1'>" + post.baslik + "</h4>" +
                                "<p class='text-secondary small mb-0'>" + ozet + "</p>" +
                            "</div>" +
                            "<div class='d-flex gap-2 mt-3 mt-md-0 w-100 w-md-auto justify-content-end'>" +
                                "<a href='detay.php?id=" + post.id + "' class='btn btn-light rounded-pill btn-sm fw-bold px-3 border shadow-sm' title='Görüntüle'>" +
                                    "<i class='fa-regular fa-eye'></i>" +
                                "</a>" +
                                "<a href='duzenle.php?id=" + post.id + "' class='btn btn-edit rounded-pill btn-sm fw-bold px-3 shadow-sm' title='Düzenle'>" +
                                    "<i class='fa-regular fa-pen-to-square'></i>" +
                                "</a>" +
                                "<button class='btn btn-outline-danger rounded-circle btn-sm p-2 shadow-sm' onclick='yaziSil(" + post.id + ")' title='Sil'>" +
                                    "<i class='fa-regular fa-trash-can'></i>" +
                                "</button>" +
                            "</div>" +
                        "</div>";

                        liste.innerHTML += htmlCard; // Kartı HTML listesinin sonuna ekliyoruz.
                    }
                })
                .catch(function(err) {
                    // Sunucu ile iletişim koparsa kullanıcının haberdar olması için uyarı basıyoruz.
                    document.getElementById('yukleniyor').innerHTML = "<p class='text-danger text-center'>Veriler yüklenirken bir sorun oluştu.</p>";
                });
        }

        // Tıklanan gönderinin ID'sini alarak silme işlemi başlatan fonksiyon.
        function yaziSil(id) {
            // Kullanıcıdan silme işlemi öncesi JavaScript'in yerleşik confirm() metoduyla onay istiyoruz.
            if (confirm('Bu hikayeyi silmek istediğine emin misin?')) {
                
                // Onay verilirse GET isteği ile silme API'sine ulaşıyoruz.
                fetch('api/yazi_sil.php?id=' + id, { method: 'GET' })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if(data.basarili) {
                        // Silme başarılıysa, sayfayı yenilemeye gerek kalmadan yazilariGetir() fonksiyonunu çağırarak listeyi anında güncelliyoruz.
                        yazilariGetir(); 
                    } else {
                        alert("Silme hatası: " + data.hata);
                    }
                });
            }
        }
    </script>
</body>
</html>