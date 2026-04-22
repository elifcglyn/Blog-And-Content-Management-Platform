<?php 
  $activePage = 'yazilarim'; 
  $pageTitle = 'Yazıyı Düzenle'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
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

                    <form id="duzenle-form" class="animate-fade-in">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color: #64748b;">Hikaye Başlığı</label>
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

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const yaziId = urlParams.get('id');

        document.addEventListener("DOMContentLoaded", () => {
            if (!yaziId) {
                alert("Yazı ID bulunamadı!");
                window.location.href = 'yazilarim.php';
                return;
            }

            // Mevcut verileri çekip forma doldur
            fetch('api/yazilari_getir.php')
                .then(res => res.json())
                .then(yazilar => {
                    const yazi = yazilar.find(y => y.id == yaziId);
                    if(yazi) {
                        document.getElementById('baslik').value = yazi.baslik;
                        document.getElementById('icerik').value = yazi.icerik;
                    } else {
                        alert("Yazı veritabanında bulunamadı.");
                    }
                })
                .catch(err => console.error("Veri çekme hatası:", err));
        });

        document.getElementById('duzenle-form').addEventListener('submit', (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Kaydediliyor...';

            const veri = {
                id: parseInt(yaziId),
                baslik: document.getElementById('baslik').value,
                icerik: document.getElementById('icerik').value
            };

            // ÖNEMLİ: api/yazi_guncelle.php dosyasının varlığından emin ol!
            fetch('api/yazi_guncelle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(veri)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Tebrikler! Hikaye başarıyla güncellendi.");
                    window.location.href = 'detay.php?id=' + yaziId;
                } else {
                    alert("Hata: " + (data.error || "Güncelleme yapılamadı."));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Değişiklikleri Kaydet';
                }
            })
            .catch(err => {
                console.error("Fetch Hatası:", err);
                alert("Sunucuyla bağlantı kurulamadı. api/yazi_guncelle.php dosyasını kontrol et!");
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Değişiklikleri Kaydet';
            });
        });

        // Sidebar Toggle
        if(document.getElementById('sidebarToggleBtn')) {
            document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                document.getElementById('mainSidebar').classList.toggle('collapsed');
            });
        }
    </script>
</body>
</html>