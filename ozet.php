<?php 
  require_once 'auth.php'; 
  // Sadece ilk ismi almak için
  $kullanici_ilk_isim = explode(' ', $_SESSION['ad_soyad'] ?? 'Yazar')[0];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postify Özet - Modern Blog</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { 
            margin: 0; padding: 0; 
            font-family: system-ui, -apple-system, sans-serif; 
            overflow: hidden; 
            background-color: #000;
        }
        .serif-italic { font-family: 'Instrument Serif', Georgia, serif; font-style: italic; }
        
        #wrapped-container {
            position: fixed; inset: 0; z-index: 50;
            display: flex; flex-direction: column;
            transition: background 1s ease-in-out;
            color: white;
        }

        .progress-container { display: flex; gap: 0.5rem; width: 100%; max-width: 800px; margin: 0 auto; }
        .progress-track { height: 6px; flex: 1; background-color: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; width: 0%; background-color: white; border-radius: 10px; }
        
        @keyframes slideUpFade {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes zoomInFade {
            0% { opacity: 0; transform: scale(0.85); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes slideRightFade {
            0% { opacity: 0; transform: translateX(40px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        .anim-slide-up { animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-zoom-in { animation: zoomInFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-slide-right { animation: slideRightFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        .nav-btn {
            background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);
            border: none; color: white; width: 50px; height: 50px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.3s;
        }
        .nav-btn:hover { background: rgba(255, 255, 255, 0.2); transform: scale(1.1); }
        .nav-btn:disabled { opacity: 0; pointer-events: none; }

        /* Z-INDEX ÇAKIŞMASI BURADA ÇÖZÜLDÜ */
        .click-zone { position: absolute; top: 0; bottom: 0; z-index: 10; opacity: 0; }
        .click-zone.left { left: 0; width: 25%; cursor: w-resize; }
        .click-zone.right { right: 0; width: 25%; cursor: e-resize; }

        .text-shadow { text-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .gold-gradient-text {
            background: linear-gradient(to right, #fde68a, #f59e0b);
            background-clip: text; color: transparent;
        }
    </style>
</head>
<body>

    <div id="wrapped-container" style="background: linear-gradient(to bottom right, #4f46e5, #312e81);">
        
        <!-- ÜST BAR: Z-INDEX 1050 YAPILARAK TIKLANABİLİR HALE GETİRİLDİ -->
        <div class="p-4 p-md-5 w-100 position-relative" style="z-index: 1050;">
            <div class="progress-container mb-4" id="progress-bars"></div>
            
            <div class="d-flex justify-content-between align-items-center mx-auto" style="max-width: 800px;">
                <div class="fw-bold text-uppercase d-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 2px; opacity: 0.8;">
                    <i id="slide-icon" class="fa-solid fa-wand-magic-sparkles fs-5"></i> Postify Özet
                </div>
                <!-- LİNK PHP OLARAK DÜZELTİLDİ -->
                <a href="index.php" class="nav-btn text-decoration-none" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </div>

        <div class="click-zone left" onclick="oncekiSlayt()"></div>
        <div class="click-zone right" onclick="sonrakiSlayt()"></div>

        <div class="flex-grow-1 d-flex align-items-center justify-content-center p-4 position-relative z-1" id="slide-content">
        </div>

        <!-- ALT BAR: Z-INDEX 1050 YAPILDI -->
        <div class="p-4 p-md-5 w-100 position-relative" style="z-index: 1050;">
            <div class="d-flex justify-content-between mx-auto" style="max-width: 800px;">
                <button class="nav-btn" id="btn-prev" onclick="oncekiSlayt()"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="nav-btn" id="btn-next" onclick="sonrakiSlayt()"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

    </div>

    <script>
        // İSTATİSTİKLER (Gerçekte Backend'den gelebilir, şu an şov amaçlı statik/dinamik karma)
        const KULLANICI_ISTATISTIK = {
            isim: "<?= htmlspecialchars($kullanici_ilk_isim) ?>", // PHP Session'dan geliyor!
            totalMinutes: 342,
            topCategory: "Teknoloji",
            favoriteAuthor: "Caner Kaya",
            totalPostsRead: 45,
            persona: "Bilgi Avcısı"
        };

        const SLAYTLAR = [
            {
                id: "intro",
                theme: "linear-gradient(to bottom right, #4f46e5, #312e81)", 
                icon: "fa-wand-magic-sparkles",
                animation: "anim-slide-up",
                html: `
                    <div class="text-center w-100 px-3">
                        <h2 class="fw-bold text-uppercase mb-4" style="letter-spacing: 3px; font-size: 1.2rem; color: rgba(255,255,255,0.7);">Nisan 2026 Özeti</h2>
                        <h1 class="serif-italic fw-bold text-shadow" style="font-size: clamp(3rem, 8vw, 5rem); line-height: 1.1;">Hoş geldin ${KULLANICI_ISTATISTIK.isim},<br>bu ay kelimelerin içinde kayboldun...</h1>
                    </div>
                `
            },
            {
                id: "time",
                theme: "linear-gradient(to bottom right, #0d9488, #064e3b)", 
                icon: "fa-clock",
                animation: "anim-zoom-in",
                html: `
                    <div class="text-center w-100 px-3">
                        <h2 class="fw-bold text-uppercase mb-4" style="letter-spacing: 3px; font-size: 1.2rem; color: rgba(255,255,255,0.7);">Zaman Nasıl Geçti?</h2>
                        <div>
                            <span class="serif-italic fw-bold text-shadow" style="font-size: clamp(6rem, 15vw, 10rem); line-height: 1;">${KULLANICI_ISTATISTIK.totalMinutes}</span>
                            <span class="d-block fw-light mt-2" style="font-size: 2rem; color: rgba(255,255,255,0.8);">dakika</span>
                        </div>
                    </div>
                `
            },
            {
                id: "category",
                theme: "linear-gradient(to bottom right, #e11d48, #7c2d12)", 
                icon: "fa-arrow-trend-up",
                animation: "anim-slide-right",
                html: `
                    <div class="text-center w-100 px-3">
                        <h2 class="fw-bold text-uppercase mb-4" style="letter-spacing: 3px; font-size: 1.2rem; color: rgba(255,255,255,0.7);">Kalbin Ne İçin Atıyor?</h2>
                        <h1 class="serif-italic fw-bold text-shadow" style="font-size: clamp(4rem, 12vw, 8rem); line-height: 1;">${KULLANICI_ISTATISTIK.topCategory}</h1>
                    </div>
                `
            },
            {
                id: "outro",
                theme: "linear-gradient(to bottom right, #0f172a, #000000)", 
                icon: "fa-award",
                animation: "anim-slide-up",
                html: `
                    <div class="text-center w-100 px-3">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-5" style="width: 120px; height: 120px; background: linear-gradient(to top right, #fbbf24, #ea580c); box-shadow: 0 0 100px rgba(251,191,36,0.4);">
                            <i class="fa-solid fa-award text-white" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="serif-italic fw-bold mb-5 gold-gradient-text" style="font-size: clamp(3rem, 8vw, 6rem); line-height: 1;">${KULLANICI_ISTATISTIK.persona}</h1>
                        <a href="index.php" class="btn btn-light rounded-pill fw-bold text-uppercase mt-4" style="padding: 1rem 2rem; font-size: 0.9rem; letter-spacing: 2px;">
                            Teşekkürler, Ana Sayfaya Dön
                        </a>
                    </div>
                `
            }
        ];

        let aktifSlayt = 0;
        let sureSayaci;
        const SLAYT_SURESI = 6000; 

        document.addEventListener("DOMContentLoaded", function() {
            ilerlemeCubuklariniOlustur();
            slaytiGoster(aktifSlayt);
        });

        function ilerlemeCubuklariniOlustur() {
            const container = document.getElementById('progress-bars');
            container.innerHTML = '';
            SLAYTLAR.forEach((_, index) => {
                container.innerHTML += `
                    <div class="progress-track">
                        <div class="progress-fill" id="fill-${index}"></div>
                    </div>
                `;
            });
        }

        function slaytiGoster(index) {
            clearTimeout(sureSayaci);

            const slayt = SLAYTLAR[index];
            
            document.getElementById('wrapped-container').style.background = slayt.theme;
            const iconEl = document.getElementById('slide-icon');
            iconEl.className = `fa-solid ${slayt.icon} fs-5`;

            const contentContainer = document.getElementById('slide-content');
            contentContainer.innerHTML = ''; 
            contentContainer.className = `flex-grow-1 d-flex align-items-center justify-content-center p-4 position-relative z-1 ${slayt.animation}`;
            
            setTimeout(() => {
                contentContainer.innerHTML = slayt.html;
            }, 50);

            document.getElementById('btn-prev').disabled = (index === 0);
            
            // İlerleme Çubukları Yönetimi
            for(let i = 0; i < SLAYTLAR.length; i++) {
                const fill = document.getElementById(`fill-${i}`);
                if (i < index) {
                    fill.style.transition = 'none';
                    fill.style.width = '100%';
                } else if (i === index) {
                    fill.style.transition = 'none';
                    fill.style.width = '0%';
                    setTimeout(() => {
                        fill.style.transition = `width ${SLAYT_SURESI}ms linear`;
                        fill.style.width = '100%';
                    }, 50);
                } else {
                    fill.style.transition = 'none';
                    fill.style.width = '0%';
                }
            }

            // Otomatik geçiş
            if (index < SLAYTLAR.length - 1) {
                sureSayaci = setTimeout(() => {
                    sonrakiSlayt();
                }, SLAYT_SURESI);
            }
        }

        function sonrakiSlayt() {
            if (aktifSlayt < SLAYTLAR.length - 1) {
                aktifSlayt++;
                slaytiGoster(aktifSlayt);
            } else {
                // Son slaytta ileri basılırsa veya biterse ana sayfaya dön
                window.location.href = 'index.php';
            }
        }

        function oncekiSlayt() {
            if (aktifSlayt > 0) {
                aktifSlayt--;
                slaytiGoster(aktifSlayt);
            }
        }
    </script>
</body>
</html>