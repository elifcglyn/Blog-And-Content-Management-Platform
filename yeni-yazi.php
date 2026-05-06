<?php 
  require_once 'auth.php'; // Giriş kontrolü şart!
  require_once 'api/baglanti.php';

  $activePage = 'yeni-yazi'; 
  $pageTitle = 'Yeni Hikaye Yarat'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        .serif-italic { font-family: 'Instrument Serif', serif; font-style: italic; }
        
        /* Kapak Fotoğrafı Alanı */
        .cover-area {
            aspect-ratio: 21/9; background-color: #f8fafc; border: 2px dashed #e2e8f0;
            border-radius: 2rem; display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden; cursor: pointer; transition: 0.3s;
        }
        .cover-area:hover { border-color: #0d9488; }
        .cover-area img { width: 100%; height: 100%; object-fit: cover; }
        .cover-overlay {
            position: absolute; inset: 0; background: rgba(15, 23, 42, 0.6);
            display: flex; gap: 10px; align-items: center; justify-content: center;
            opacity: 0; transition: 0.3s;
        }
        .cover-area:hover .cover-overlay { opacity: 1; }

        .title-input {
            border: none; outline: none; box-shadow: none;
            font-size: 3rem; padding: 0; background: transparent;
            color: #0f172a; font-weight: bold; width: 100%;
        }
        .title-input::placeholder { color: #cbd5e1; }

        .editor-wrapper { border: 1px solid #f1f5f9; border-radius: 1.5rem; background: white; overflow: hidden; }
        .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid #f1f5f9 !important; padding: 15px; }
        .ql-container.ql-snow { border: none !important; min-height: 400px; font-size: 1.15rem; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5 d-flex justify-content-center">
                    <div style="max-width: 1000px; width: 100%;">
                        
                        <div class="d-flex justify-content-between align-items-end mb-4 pb-3 border-bottom">
                            <div>
                                <div class="text-teal fw-bold text-uppercase mb-1" style="letter-spacing: 2px; font-size: 0.75rem; color: #0d9488;">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Postify Editor v2.5
                                </div>
                                <h1 class="serif-italic fs-2 fw-bold text-dark mb-0">Hikayeni Yaz</h1>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-dark rounded-pill px-3 fw-bold btn-sm" onclick="alert('Ön izleme yakında!')">
                                    <i class="fa-regular fa-eye me-1"></i> Ön İzleme
                                </button>
                                <button id="btn-yayinla" class="btn text-white rounded-pill px-4 fw-bold btn-sm" style="background-color: #0d9488;" onclick="yaziyiYayinla()">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Yayınla
                                </button>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-8">
                                <input type="file" id="kapak-input" accept="image/*" class="d-none" onchange="kapakResmiYukle(event)">
                                <div class="cover-area mb-4 shadow-sm" id="kapak-alani" onclick="document.getElementById('kapak-input').click()">
                                    <div class="text-center text-muted" id="kapak-placeholder">
                                        <i class="fa-solid fa-image fa-2x mb-2" style="color: #cbd5e1;"></i>
                                        <h6 class="fw-bold mb-0">Kapak Fotoğrafı Ekle</h6>
                                    </div>
                                    <div class="cover-overlay" id="kapak-butonlar" style="display: none;">
                                        <button class="btn btn-light btn-sm rounded-pill px-3" onclick="event.stopPropagation(); document.getElementById('kapak-input').click()">Değiştir</button>
                                        <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="event.stopPropagation(); resmiSil()">Sil</button>
                                    </div>
                                </div>

                                <input type="text" id="baslik" class="title-input mb-4 serif-italic" placeholder="Buraya etkileyici bir başlık yaz...">

                                <div class="editor-wrapper shadow-sm">
                                    <div id="toolbar">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-header" value="1"></button>
                                        <button class="ql-header" value="2"></button>
                                        <button class="ql-blockquote"></button>
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-image"></button>
                                    </div>
                                    <div id="editor-container"></div>
                                </div>
                            </div>

                            <aside class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                                    <h6 class="serif-italic mb-3 fw-bold"><i class="fa-solid fa-tag text-teal me-2"></i> Kategori</h6>
                                    <select id="kategori" class="form-select border-0 rounded-pill py-2 shadow-none fw-medium">
                                        <option value="1">Yazılım & Kodlama</option>
                                        <option value="2">Tasarım & UI/UX</option>
                                        <option value="3">Yapay Zeka</option>
                                        <option value="4">Günlük</option>
                                    </select>
                                </div>

                                <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background-color: #0d9488;">
                                    <h6 class="serif-italic mb-2 fw-bold">Yazar Notu</h6>
                                    <p class="small opacity-75">Hikayen yayınlandığında takipçilerine bildirim gidecektir.</p>
                                    <textarea id="surum-notu" class="form-control bg-white bg-opacity-10 border-0 text-white placeholder-light rounded-3" rows="3" placeholder="Bu yazı hakkında minik bir not..."></textarea>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Giriş yapan kullanıcının ID'sini PHP'den alıyoruz (Sihirli dokunuş!)
        const GERCEK_KULLANICI_ID = <?= $_SESSION['kullanici_id'] ?>;

        var quill = new Quill('#editor-container', {
            modules: { toolbar: '#toolbar' },
            placeholder: 'Anlatacakların dünyayı değiştirebilir...',
            theme: 'snow'
        });

        let base64Kapak = null;

        function kapakResmiYukle(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    base64Kapak = e.target.result;
                    const kapakAlani = document.getElementById('kapak-alani');
                    document.getElementById('kapak-placeholder').style.display = 'none';
                    document.getElementById('kapak-butonlar').style.display = 'flex';
                    const eskiResim = kapakAlani.querySelector('img');
                    if(eskiResim) eskiResim.remove();
                    const img = document.createElement('img');
                    img.src = base64Kapak;
                    kapakAlani.insertBefore(img, kapakAlani.firstChild);
                };
                reader.readAsDataURL(file);
            }
        }

        function resmiSil() {
            base64Kapak = null;
            document.getElementById('kapak-placeholder').style.display = 'block';
            document.getElementById('kapak-butonlar').style.display = 'none';
            const img = document.getElementById('kapak-alani').querySelector('img');
            if(img) img.remove();
        }

        function yaziyiYayinla() {
            const baslik = document.getElementById('baslik').value.trim();
            const icerikHTML = quill.root.innerHTML;
            const safMetin = quill.getText();
            const kategoriId = document.getElementById('kategori').value;

            if (!baslik) { alert("Hikayene bir başlık koymalısın!"); return; }
            if (safMetin.length < 10) { alert("Hikayen biraz kısa kalmadı mı?"); return; }

            // Loading durumu
            const btn = document.getElementById('btn-yayinla');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Yayınlanıyor...';

            // Okunma süresi hesabı
            const kelimeSayisi = safMetin.trim().split(/\s+/).length;
            const okunmaSuresi = Math.ceil(kelimeSayisi / 200) || 1;

            const veriPaketi = {
                yazar_id: GERCEK_KULLANICI_ID, // ARTIK SENİN ID'N!
                kategori_id: parseInt(kategoriId),
                baslik: baslik,
                ozet: safMetin.substring(0, 160) + "...",
                icerik: icerikHTML,
                kapak_resmi: base64Kapak,
                okunma_suresi: okunmaSuresi
            };

            fetch("api/yazi_ekle.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(veriPaketi),
            })
            .then(res => res.json())
            .then(data => {
                if(data.hata) {
                    alert("Eyvah bir hata: " + data.hata);
                    btn.disabled = false;
                    btn.innerHTML = "Yayınla";
                } else {
                    alert("Tebrikler Elif! Yazın başarıyla yayınlandı. 🚀");
                    window.location.href = "profil.php"; // Doğrudan profile git ki görebil elini!
                }
            })
            .catch(err => {
                console.error(err);
                alert("Bağlantı koptu!");
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>