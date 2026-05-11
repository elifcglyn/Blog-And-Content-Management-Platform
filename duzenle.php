<?php 
// Olası hataları yakalamak ve ekranda görmek için hata ayıklama (error reporting) modunu açıyoruz. (Ders 08)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum başlatma ve giriş kontrolü yapan dosyalarımızı en üstte çağırıyoruz ki yetkisiz erişim olmasın.
session_start();
require_once 'auth.php'; 
require_once 'api/baglanti.php';

$activePage = 'yazilarim'; 
$pageTitle = 'Yazıyı Düzenle'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Tasarım ve kütüphane linklerini içeren ortak dosyamızı dahil ediyoruz. -->
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            <?php include 'sidebar.php'; ?>
            
            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                <?php include 'topbar.php'; ?>
                
                <div class="px-4 px-md-5 pb-5" style="max-width: 800px; margin: 0 auto;">
                    
                    <div class="mb-5 mt-4">
                        <h2 class="serif-italic fw-bold mb-2">Hikayeyi Güncelle</h2>
                        <p class="text-secondary small">Yaptığın değişiklikler sürüm geçmişine (v1.1) kaydedilecektir.</p>
                    </div>

                    <!-- Verilerin güncelleneceği HTML Formu. Sayfa yenilenmesini JS ile engelleyeceğiz. -->
                    <form id="duzenle-form" class="animate-fade-in">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color: #64748b;">Hikaye Başlığı</label>
                            <!-- required özelliği ile HTML5 tabanlı basit form doğrulaması (Validation) yapıyoruz. -->
                            <input type="text" id="baslik" class="form-control rounded-4 p-3 border-0 bg-light shadow-sm" required placeholder="Başlık girin...">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color: #64748b;">Hikaye İçeriği</label>
                            <textarea id="icerik" class="form-control rounded-4 p-3 border-0 bg-light shadow-sm" rows="12" required placeholder="İçeriğinizi buraya yazın..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <button type="button" onclick="window.history.back()" class="btn btn-link text-secondary text-decoration-none fw-bold">
                                <i class="fa-solid fa-xmark me-2"></i> İptal Et
                            </button>
                            <button type="submit" id="submit-btn" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow" style="background-color: #0d9488; border: none;">
                                <i class="fa-solid fa-check me-2"></i> Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>

                </div>
            </main>
        </div>
    </div>

    <!-- DOM Manipülasyonu ve AJAX (Fetch) İstekleri -->
    <script>
        // Adres çubuğundaki (URL) ?id= parametresini yakalamak için JavaScript'in yerleşik nesnesini kullanıyoruz.
        var urlParams = new URLSearchParams(window.location.search);
        var yaziId = urlParams.get('id');

        // Sayfanın HTML yapısı tamamen yüklendiğinde çalışacak fonksiyon (window.onload).
        window.onload = function() {
            
            // Eğer URL'de ID yoksa kullanıcıyı güvenli bir şekilde listeye geri gönderiyoruz.
            if (!yaziId) {
                alert("Yazı ID bulunamadı!");
                window.location.href = 'yazilarim.php';
                return;
            }

            // AŞAMA 1: Mevcut Verileri Veritabanından Çekip Forma Doldurma
            fetch('api/yazilari_getir.php')
                .then(function(res) {
                    return res.json();
                })
                .then(function(yazilar) {
                    // API'den gelen yazılar içinden bizim ID'mize uyanı bulmak için klasik FOR döngüsü kullanıyoruz.
                    var yazi = null;
                    var i;
                    for(i = 0; i < yazilar.length; i++) {
                        if (parseInt(yazilar[i].id) === parseInt(yaziId)) {
                            yazi = yazilar[i];
                            break; // Bulduğumuzda döngüyü kırıp performansı koruyoruz (Break).parseInt veritabanı uyuşmazlığını önlemek için kullanıyoruz.
                        }
                    }

                    // Eğer eşleşen yazı bulunduysa DOM manipülasyonu ile form elemanlarının değerini (value) dolduruyoruz.
                    if (yazi) {
                        document.getElementById('baslik').value = yazi.baslik;
                        document.getElementById('icerik').value = yazi.icerik;
                    } else {
                        alert("Yazı veritabanında bulunamadı.");
                    }
                })
                .catch(function(err) {
                    console.error("Veri çekme hatası:", err);
                });

            // AŞAMA 2: Form Submit (Gönderme) İşlemini Yakalama ve AJAX ile Güncelleme
            var duzenleForm = document.getElementById('duzenle-form');
            if (duzenleForm) {
                // Formun gönderilme (submit) olayını dinliyoruz
                duzenleForm.onsubmit = function(e) {
                    // Sayfanın yenilenmesini engelleyerek işlemi tamamen arka planda (AJAX) yapıyoruz.
                    e.preventDefault();
                    
                    var submitBtn = document.getElementById('submit-btn');
                    submitBtn.disabled = true; // Çoklu tıklamayı (Spam) önlemek için butonu kilitliyoruz.
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Kaydediliyor...';

                    // API'ye yollanacak güncel verileri DOM'dan okuyarak bir JavaScript Nesnesi (Object) hazırlıyoruz.
                    var veri = {
                        id: parseInt(yaziId),
                        baslik: document.getElementById('baslik').value,
                        icerik: document.getElementById('icerik').value
                    };

                    // Güncelleme yapan PHP API dosyamıza POST isteği atıyoruz (Ders 04).
                    fetch('api/yazi_guncelle.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(veri) // Veriyi JSON formatına çevirerek (stringify) yolluyoruz.
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        // Eğer backend başarılı yanıt dönerse, kullanıcıyı tebrik edip güncellediği yazının detay sayfasına yolluyoruz.
                        if (data.success) {
                            alert("Tebrikler! Hikaye başarıyla güncellendi.");
                            window.location.href = 'detay.php?id=' + yaziId;
                        } else {
                            // Backend'den hata dönerse butonu tekrar aktif edip uyarı veriyoruz.
                            alert("Hata: " + (data.error ? data.error : "Güncelleme yapılamadı."));
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Değişiklikleri Kaydet';
                        }
                    })
                    .catch(function(err) {
                        console.error("Fetch Hatası:", err);
                        alert("Sunucuyla bağlantı kurulamadı. Lütfen api/yazi_guncelle.php dosyasını kontrol edin.");
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Değişiklikleri Kaydet';
                    });
                };
            }

            // Ortak Sidebar Butonu Scripti (Event Binding)
            var sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            if (sidebarToggleBtn) {
                sidebarToggleBtn.onclick = function() {
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