<?php
// DERS 8: Oturum Yönetimi (Session) başlatılıyor
session_start();
require_once 'baglanti.php';

$hataMesaji = "";

// DERS 8: Formdan veri POST metodu ile mi geldi kontrolü
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // DERS 8: $_POST süper globali ile verileri alma
    $email = $_POST['email'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if (!empty($email) && !empty($sifre)) {
        try {
            // Hocanın istediği SELECT sorgusu (Sizin kodunuzun aynısı)
            $sorgu = $db->prepare("SELECT * FROM users WHERE email = ? AND sifre = ?");
            $sorgu->execute([$email, $sifre]);
            $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

            if ($kullanici) {
                // Şifre doğruysa SESSION (Oturum) oluştur (Ders 8)
                $_SESSION['kullanici_id'] = $kullanici['id'];
                $_SESSION['kullanici_isim'] = $kullanici['isim'] ?? 'Admin'; // Veritabanındaki kolon ismine göre ayarlayın
                
                // Başarılı girişte ana sayfaya veya yönetim paneline yönlendir
                header("Location: index.html"); 
                exit();
            } else {
                $hataMesaji = "E-posta veya şifre hatalı!";
            }
        } catch (PDOException $e) {
            $hataMesaji = "Veritabanı Hatası: " . $e->getMessage();
        }
    } else {
        $hataMesaji = "Lütfen tüm alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Girişi - Modern Blog</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Sizin modern tasarım çizginizi koruyan CSS */
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: none;
        }
        .login-header {
            background: linear-gradient(135deg, #312e81, #581c87);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
        }
        .btn-primary {
            background-color: #0d9488;
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: #0f766e;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                
                <div class="card login-card">
                    <div class="login-header">
                        <h3 class="mb-0 fw-bold"><i class="fa-solid fa-lock me-2"></i> Yönetim Paneli</h3>
                        <p class="text-white-50 mt-2 mb-0">İçerik yönetimi için giriş yapın</p>
                    </div>
                    
                    <div class="card-body p-4">
                        
                        <?php if(!empty($hataMesaji)): ?>
                            <div class="alert alert-danger rounded-3" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $hataMesaji; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold text-secondary">E-posta Adresi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control border-start-0" id="email" name="email" required placeholder="ornek@mail.com">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="sifre" class="form-label fw-bold text-secondary">Şifre</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-key text-muted"></i></span>
                                    <input type="password" class="form-control border-start-0" id="sifre" name="sifre" required placeholder="••••••••">
                                </div>
                            </div>
                            
                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-primary btn-lg text-uppercase" style="letter-spacing: 1px;">Giriş Yap</button>
                            </div>
                            
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="index.html" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Siteye Dön</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>