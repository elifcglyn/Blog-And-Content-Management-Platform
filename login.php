<?php
session_start();
require_once 'api/baglanti.php';

$hataMesaji = "";
$basariMesaji = "";

// FORM GÖNDERİLDİĞİNDE ÇALIŞACAK KISIM
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $islem = $_POST['islem'] ?? ''; // 'giris' veya 'kayit' gelecek
    $email = trim($_POST['email'] ?? '');
    $sifre = $_POST['sifre'] ?? '';

    // 1. KAYIT OL İŞLEMİ
    if ($islem === 'kayit') {
        $ad_soyad = trim($_POST['ad_soyad'] ?? '');
        
        if (empty($ad_soyad) || empty($email) || empty($sifre)) {
            $hataMesaji = "Lütfen tüm alanları doldurun.";
        } else {
            try {
                // E-posta daha önce kayıtlı mı kontrol et
                $kontrol = $db->prepare("SELECT id FROM users WHERE email = ?");
                $kontrol->execute([$email]);
                
                if ($kontrol->rowCount() > 0) {
                    $hataMesaji = "Bu e-posta adresi zaten kullanılıyor. Lütfen giriş yapın.";
                } else {
                    // Şifreyi güvenli hale getir (Kırılamaz Hash)
                    $guvenli_sifre = password_hash($sifre, PASSWORD_DEFAULT);
                    
                    // E-postadan otomatik bir kullanıcı adı üret
                    $email_parca = explode('@', $email);
                    $otomatik_username = $email_parca[0] . rand(100, 999);

                    // VERİTABANINA EKLE (hesap_durumu sütunu için 'Aktif' eklendi)
                    $ekle = $db->prepare("INSERT INTO users (ad_soyad, email, sifre, username, rol, hesap_durumu) VALUES (?, ?, ?, ?, 'Yazar', 'Aktif')");
                    
                    if ($ekle->execute([$ad_soyad, $email, $guvenli_sifre, $otomatik_username])) {
                        $basariMesaji = "Hesabınız başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.";
                    } else {
                        $hataMesaji = "Kayıt olurken bir hata oluştu.";
                    }
                }
            } catch (PDOException $e) {
                // Hata durumunda sayfanın çökmesini engeller
                $hataMesaji = "Sistemsel bir hata oluştu: " . $e->getMessage();
            }
        }
    } 
    // 2. GİRİŞ YAP İŞLEMİ
    elseif ($islem === 'giris') {
        if (empty($email) || empty($sifre)) {
            $hataMesaji = "Lütfen e-posta ve şifrenizi girin.";
        } else {
            try {
                // Kullanıcıyı e-posta ile bul
                $sorgu = $db->prepare("SELECT * FROM users WHERE email = ?");
                $sorgu->execute([$email]);
                $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

                // Şifre doğrulama
                if ($kullanici && (password_verify($sifre, $kullanici['sifre']) || $sifre === $kullanici['sifre'])) {
                    // Oturumu başlat
                    $_SESSION['kullanici_id'] = $kullanici['id'];
                    $_SESSION['ad_soyad']     = $kullanici['ad_soyad'];
                    $_SESSION['username']     = $kullanici['username'];
                    $_SESSION['rol']          = $kullanici['rol'];
                    $_SESSION['avatar_url']   = $kullanici['avatar_url'];
                    
                    header("Location: index.php"); 
                    exit();
                } else {
                    $hataMesaji = "E-posta adresi veya şifre hatalı.";
                }
            } catch (PDOException $e) {
                $hataMesaji = "Giriş sırasında hata oluştu: " . $e->getMessage();
            }
        }
    }
}
?>
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
            background-color: #0f172a;
            background-image: url('assets/auth-bg.jpg'); 
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

        .right-panel { background-color: #ffffff; }
        .form-label { font-size: 0.875rem; font-weight: 500; color: #0f172a; margin-bottom: 0.4rem; }
        .custom-input {
            border-radius: 0.5rem; border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem; font-size: 0.95rem; box-shadow: none;
            background-color: #f8fafc;
        }
        .custom-input:focus { border-color: #0d9488; box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2); outline: none; background-color: white;}
        
        .btn-teal {
            background-color: #0d9488; color: white; border: none;
            border-radius: 0.5rem; padding: 0.75rem; font-weight: bold; transition: 0.3s;
        }
        .btn-teal:hover { background-color: #0f766e; color: white; transform: translateY(-2px); }
        
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
                
                <div class="animate-fade-in-up mb-4">
                    <h2 id="form-title" class="font-serif fst-italic mb-2 text-dark" style="font-size: 3rem; letter-spacing: -0.03em;">
                        Hoş Geldin
                    </h2>
                    <p id="form-subtitle" class="text-muted small">İçerik stüdyosuna giriş yapın.</p>
                </div>

                <?php if(!empty($hataMesaji)): ?>
                    <div class="alert alert-danger rounded-3 fw-medium small mb-4 animate-fade-in-up" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $hataMesaji ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($basariMesaji)): ?>
                    <div class="alert alert-success rounded-3 fw-medium small mb-4 animate-fade-in-up" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> <?= $basariMesaji ?>
                    </div>
                <?php endif; ?>

                <form id="auth-form" action="login.php" method="POST" class="animate-fade-in-up delay-2">
                    
                    <input type="hidden" name="islem" id="islem-turu" value="giris">
                    
                    <div class="mb-3 d-none" id="name-field">
                        <label for="ad_soyad" class="form-label">Ad Soyad</label>
                        <input type="text" id="ad_soyad" name="ad_soyad" class="form-control custom-input" placeholder="Örn: Elif Çağlayan">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" id="email" name="email" class="form-control custom-input" placeholder="mail@ornek.com" required>
                    </div>

                    <div class="mb-4 position-relative">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" id="password" name="sifre" class="form-control custom-input pe-5" placeholder="••••••••" required>
                        
                        <button type="button" class="toggle-btn" onclick="sifreGosterGizle()">
                            <i class="fa-regular fa-eye" id="eye-icon"></i>
                        </button>
                    </div>

                    <button type="submit" id="submit-btn" class="btn btn-teal w-100 mb-4 shadow-sm text-uppercase" style="letter-spacing: 1px;">
                        Giriş Yap
                    </button>
                </form>

                <div class="text-center pt-4 border-top animate-fade-in-up delay-4">
                    <span class="text-muted small me-1" id="toggle-text">Hesabınız yok mu?</span>
                    <button type="button" class="btn btn-link text-decoration-none fw-bold p-0" style="color: #0d9488;" onclick="modDegistir()">
                        Kayıt Olun
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        let girisModu = true; 

        function modDegistir() {
            girisModu = !girisModu;
            
            // DOM Elementleri
            const title = document.getElementById('form-title');
            const subtitle = document.getElementById('form-subtitle');
            const submitBtn = document.getElementById('submit-btn');
            const nameField = document.getElementById('name-field');
            const nameInput = document.getElementById('ad_soyad');
            const toggleBtn = document.querySelector('.text-center button');
            const toggleText = document.getElementById('toggle-text');
            const islemInput = document.getElementById('islem-turu');

            if (girisModu) {
                // Giriş Moduna Geç
                title.innerText = "Hoş Geldin";
                subtitle.innerText = "İçerik stüdyosuna giriş yapın.";
                submitBtn.innerText = "GİRİŞ YAP";
                toggleText.innerText = "Hesabınız yok mu?";
                toggleBtn.innerText = "Kayıt Olun";
                islemInput.value = "giris"; // PHP'ye giriş yapacağını söyler
                
                nameField.classList.add('d-none');
                nameInput.removeAttribute('required');
            } else {
                // Kayıt Moduna Geç
                title.innerText = "Hemen Başla";
                subtitle.innerText = "Yeni bir yazar hesabı oluşturun.";
                submitBtn.innerText = "HESAP OLUŞTUR";
                toggleText.innerText = "Zaten hesabınız var mı?";
                toggleBtn.innerText = "Giriş Yapın";
                islemInput.value = "kayit"; // PHP'ye kayıt yapacağını söyler
                
                nameField.classList.remove('d-none');
                nameInput.setAttribute('required', 'true');
            }
        }

        function sifreGosterGizle() {
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