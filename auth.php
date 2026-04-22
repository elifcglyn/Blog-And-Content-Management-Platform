<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postify - Giriş Yap veya Kayıt Ol</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;500;700&display=swap');
        
        body { 
            font-family: 'DM Sans', sans-serif; 
            background-color: #ffffff;
            overflow-x: hidden;
        }
        .font-serif { font-family: 'Instrument Serif', Georgia, serif; }
        
        /* Animasyonlar */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-4 { animation-delay: 0.4s; }

        /* Sol Panel Tasarımı */
        .left-panel {
            background-color: #020617; 
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .left-panel-bg {
            position: absolute; inset: 0; z-index: 0; opacity: 0.6;
            background-image: url('assets/auth-bg.jpg'); /* Resim yolunu kendi klasör yapına göre düzelt! */
            background-size: cover; background-position: center;
        }
        
        .left-panel-overlay {
            position: absolute; inset: 0; z-index: 1;
            background: linear-gradient(to bottom, rgba(2,6,23,0.4), transparent, rgba(2,6,23,0.8));
        }
        
        .left-panel-content { position: relative; z-index: 10; color: white; }

        .sparkle-box {
            padding: 0.6rem; background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 0.75rem; backdrop-filter: blur(10px);
            color: #5eead4; 
        }

        /* Sağ Panel Form Elemanları */
        .right-panel { background-color: #ffffff; }
        .form-label { font-size: 0.875rem; font-weight: 500; color: #0f172a; margin-bottom: 0.4rem; }
        .custom-input {
            border-radius: 0.5rem; border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem; font-size: 0.95rem; box-shadow: none;
        }
        .custom-input:focus { border-color: #0d9488; box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2); outline: none; }
        
        .btn-teal {
            background-color: #0d9488; color: white; border: none;
            border-radius: 0.5rem; padding: 0.75rem; font-weight: 500; transition: 0.3s;
        }
        .btn-teal:hover { background-color: #0f766e; color: white; }
        
        .toggle-btn { background: transparent; border: none; position: absolute; right: 10px; top: 38px; color: #64748b; }
    </style>
</head>
<body>

    <div class="container-fluid min-vh-100 d-flex flex-column flex-md-row p-0">
        
        <div class="col-12 col-md-6 left-panel p-5 p-md-5">
            <div class="left-panel-bg"></div>
            <div class="left-panel-overlay"></div>
            
            <div class="left-panel-content animate-fade-in-up d-flex align-items-center gap-3 mt-md-4">
                <div class="sparkle-box">
                    <i class="fa-solid fa-wand-magic-sparkles fs-5"></i>
                </div>
                <h1 class="font-serif fst-italic mb-0" style="font-size: 3rem; letter-spacing: -0.02em;">Postify</h1>
            </div>

            <div class="left-panel-content animate-fade-in-up delay-2 mt-5 mt-md-0">
                <h2 class="font-serif fst-italic" style="font-size: clamp(3rem, 6vw, 4.5rem); line-height: 1; letter-spacing: -0.03em;">
                    Söz uçar,<br>
                    <span style="color: #5eead4;">versiyon</span> kalır.
                </h2>
            </div>

            <div class="left-panel-content animate-fade-in-up delay-4 mt-5 mt-md-0 pb-md-4 text-light fw-light small">
                © 2026 Postify Platform.
            </div>
        </div>

        <div class="col-12 col-md-6 right-panel d-flex flex-column justify-content-center align-items-center p-4 p-md-5">
            <div class="w-100" style="max-width: 360px;">
                
                <div class="animate-fade-in-up">
                    <h2 id="form-title" class="font-serif fst-italic mb-4 text-dark" style="font-size: 3rem; letter-spacing: -0.03em;">
                        Hoş Geldin
                    </h2>
                </div>

                <form id="auth-form" action="api/login.php" method="POST" class="animate-fade-in-up delay-2">
                    
                    <div class="mb-3 d-none" id="name-field">
                        <label for="name" class="form-label">Ad Soyad</label>
                        <input type="text" id="name" name="ad_soyad" class="form-control custom-input">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" id="email" name="email" class="form-control custom-input" required>
                    </div>

                    <div class="mb-4 position-relative">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" id="password" name="sifre" class="form-control custom-input pe-5" required>
                        
                        <button type="button" class="toggle-btn" onclick="togglePassword()">
                            <i class="fa-regular fa-eye" id="eye-icon"></i>
                        </button>
                    </div>

                    <button type="button" onclick="window.location.href='index.php'" id="submit-btn" class="btn btn-teal w-100 mb-4">Giriş Yap</button>
                </form>

                <div class="text-center pt-4 border-top animate-fade-in-up delay-4">
                    <button type="button" class="btn btn-link text-decoration-none fw-medium p-0" style="color: #0d9488;" onclick="toggleMode()">
                        Hesap oluşturun
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        let isLoginMode = true; 

        function toggleMode() {
            isLoginMode = !isLoginMode;
            
            const title = document.getElementById('form-title');
            const submitBtn = document.getElementById('submit-btn');
            const nameField = document.getElementById('name-field');
            const nameInput = document.getElementById('name');
            const toggleLink = document.querySelector('.text-center button');
            const form = document.getElementById('auth-form');

            if (isLoginMode) {
                // Giriş Modu
                title.innerText = "Hoş Geldin";
                submitBtn.innerText = "Giriş Yap";
                toggleLink.innerText = "Hesap oluşturun";
                nameField.classList.add('d-none');
                nameInput.removeAttribute('required');
                form.action = "api/login.php"; // Doğru PHP dosyasına gider
            } else {
                // Kayıt Modu
                title.innerText = "Hemen Başla";
                submitBtn.innerText = "Hesap Oluştur";
                toggleLink.innerText = "Giriş yapın";
                nameField.classList.remove('d-none');
                nameInput.setAttribute('required', 'true');
                form.action = "api/kayit.php"; // Doğru PHP dosyasına gider
            }
        }

        function togglePassword() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (pwdInput.type === "password") {
                pwdInput.type = "text";
                eyeIcon.className = 'fa-regular fa-eye-slash';
            } else {
                pwdInput.type = "password";
                eyeIcon.className = 'fa-regular fa-eye';
            }
        }
    </script>
</body>
</html>