<?php 
  $activePage = 'yeni-yazi'; // Sidebar'da Yeni Yazı linki aktif olur
  $pageTitle = 'Yeni Hikaye Yarat'; // Topbar Başlığı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        /* Kapak Fotoğrafı Alanı */
        .cover-area {
            aspect-ratio: 21/9;
            background-color: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: 0.3s;
        }
        .cover-area:hover { border-color: #cbd5e1; }
        .cover-area img { width: 100%; height: 100%; object-fit: cover; }
        .cover-overlay {
            position: absolute; inset: 0; background: rgba(15, 23, 42, 0.6);
            display: flex; gap: 10px; align-items: center; justify-content: center;
            opacity: 0; transition: 0.3s;
        }
        .cover-area:hover .cover-overlay { opacity: 1; }

        /* Başlık Inputu */
        .title-input {
            border: none; outline: none; box-shadow: none;
            font-size: 3rem; padding: 0; background: transparent;
            color: #0f172a; font-weight: bold;
        }
        .title-input::placeholder { color: #cbd5e1; }
        .title-input:focus { box-shadow: none; background: transparent; }

        /* Editör Özelleştirmeleri */
        .ql-container.ql-snow { border: none !important; font-size: 1.1rem; font-family: inherit; min-height: 500px; }
        .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid #f1f5f9 !important; background: #f8fafc; border-radius: 1.5rem 1.5rem 0 0; padding: 12px; }
        .editor-wrapper { border: 1px solid #f1f5f9; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: white; }

        .sidebar-card { border-radius: 1.5rem; border: 1px solid #f1f5f9; background: white; }
        .bg-teal-light { background-color: #f0fdfa; border-color: #ccfbf1 !important; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5 d-flex justify-content-center">
                    <div style="max-width: 1000px; width: 100%;">
                        
                        <div class="d-flex justify-content-between align-items-end mb-4 pb-3 border-bottom">
                            <div>
                                <div class="text-teal fw-bold text-uppercase mb-1" style="letter-spacing: 2px; font-size: 0.75rem;">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Postify Editor v2.5
                                </div>
                                <h1 class="serif-italic fs-2 fw-bold text-dark mb-0">Hikayeni Yaz</h1>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-dark rounded-pill px-3 fw-bold btn-sm" onclick="alert('Ön izleme hazırlanıyor...')">
                                    <i class="fa-regular fa-eye me-1"></i> Ön İzleme
                                </button>
                                <button class="btn btn-dark rounded-pill px-4 fw-bold btn-sm" style="background-color: #0f172a;" onclick="yaziyiYayinla()">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Yayınla
                                </button>
                            </div>
                        </div>

                        <div class="row g-4">
                            
                            <div class="col-lg-8">
                                
                                <input type="file" id="kapak-input" accept="image/*" class="d-none" onchange="kapakResmiYukle(event)">
                                <div class="cover-area mb-4" id="kapak-alani" onclick="document.getElementById('kapak-input').click()">
                                    <div class="text-center text-muted" id="kapak-placeholder">
                                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-secondary"></i>
                                        <h6 class="fw-bold mb-0">Yazı Kapak Fotoğrafı Ekle</h6>
                                    </div>
                                    <div class="cover-overlay" id="kapak-butonlar" style="display: none;">
                                        <button class="btn btn-light btn-sm" onclick="event.stopPropagation(); document.getElementById('kapak-input').click()">Değiştir</button>
                                        <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); resmiSil()">Sil</button>
                                    </div>
                                </div>

                                <input type="text" id="baslik" class="form-control title-input mb-4 serif-italic" placeholder="Hikayenin Başlığı...">

                                <div class="editor-wrapper mb-4">
                                    <div id="toolbar">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-header" value="1"></button>
                                        <button class="ql-header" value="2"></button>
                                        <button class="ql-blockquote"></button>
                                        <button class="ql-code-block"></button>
                                        <button class="ql-image"></button>
                                    </div>
                                    <div id="editor-container">
                                        <h2>Yeni bir hikaye başlasın...</h2>
                                    </div>
                                </div>
                            </div>

                            <aside class="col-lg-4">
                                
                                <div class="sidebar-card p-4 mb-4">
                                    <h6 class="serif-italic mb-3 fw-bold"><i class="fa-solid fa-tag text-teal me-2"></i> Kategori Seçimi</h6>
                                    <select id="kategori" class="form-select rounded-pill fw-medium text-secondary bg-light border-0 py-2">
                                        <option value="1">Yazılım</option>
                                        <option value="2">Teknoloji</option>
                                        <option value="3">Bilim</option>
                                        <option value="4">Finans & Ekonomi</option>
                                        <option value="5">Sağlık & Yaşam</option>
                                    </select>
                                </div>

                                <div class="sidebar-card p-4 bg-teal-light">
                                    <h6 class="serif-italic mb-3 fw-bold"><i class="fa-solid fa-clock-rotate-left text-teal me-2"></i> Sürüm Notu</h6>
                                    <textarea id="surum-notu" class="form-control border-0 rounded-4 bg-transparent p-0" rows="4" placeholder="v1.0 - İlk yayın... (Opsiyonel)" style="box-shadow: none; resize: none;"></textarea>
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
        // 1. Editörü Başlat
        var quill = new Quill('#editor-container', {
            modules: { toolbar: '#toolbar' },
            theme: 'snow'
        });

        // 2. Kapak Fotoğrafı İşlemleri
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
            document.getElementById('kapak-input').value = "";
            document.getElementById('kapak-placeholder').style.display = 'block';
            document.getElementById('kapak-butonlar').style.display = 'none';
            const kapakAlani = document.getElementById('kapak-alani');
            const eskiResim = kapakAlani.querySelector('img');
            if(eskiResim) eskiResim.remove();
        }

        // 3. API'ye POST İsteği Atma (Yazıyı Kaydetme)
        function yaziyiYayinla() {
            const baslik = document.getElementById('baslik').value;
            const kategoriId = document.getElementById('kategori').value;
            const icerikHTML = quill.root.innerHTML; 
            const ozetMetin = quill.getText().substring(0, 150) + "..."; 

            if (!baslik.trim()) {
                alert("Lütfen bir başlık giriniz!");
                return;
            }

            const veriPaketi = {
                yazar_id: 1, 
                kategori_id: parseInt(kategoriId), 
                baslik: baslik,
                ozet: ozetMetin, 
                icerik: icerikHTML,
                kapak_resmi: base64Kapak 
            };

            fetch("api/yazi_ekle.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(veriPaketi),
            })
            .then(response => response.json())
            .then(data => {
                if(data.hata) {
                    alert("Hata: " + data.hata);
                } else {
                    alert("Yazın başarıyla veritabanına eklendi!");
                    // Formu sıfırla
                    document.getElementById('baslik').value = "";
                    resmiSil();
                    quill.root.innerHTML = "<h2>Yeni bir hikaye başlasın...</h2>";
                }
            })
            .catch(error => {
                console.error("Bağlantı hatası:", error);
                alert("Sunucuya bağlanılamadı!");
            });
        }

        // Ortak Sidebar Scripti
        if(document.getElementById('sidebarToggleBtn')) {
            document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
                document.getElementById('mainSidebar').classList.toggle('collapsed');
            });
        }
    </script>
</body>
</html>