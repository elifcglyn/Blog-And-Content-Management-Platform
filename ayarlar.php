<?php 
// Oturum (Session) işlemlerini başlatıyoruz. (Ders 08)
session_start();
require_once 'api/baglanti.php';

// GÜVENLİK DUVARI: Eğer oturum açılmış bir kullanıcı yoksa (kullanici_id boşsa), 
// bu sayfaya erişimi engelliyor ve doğrudan login sayfasına yönlendiriyoruz.
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

// PDO ile Güvenli Veri Çekme: Sadece $_SESSION'daki verilere güvenmek yerine, 
// kullanıcının en güncel verilerini (email, username, avatar) veritabanından çekiyoruz.
$sorgu = $db->prepare("SELECT email, username, ad_soyad, avatar_url FROM users WHERE id = ?");
$sorgu->execute([$_SESSION['kullanici_id']]);
$user = $sorgu->fetch(PDO::FETCH_ASSOC);

$activePage = 'ayarlar'; 
$pageTitle = 'Hesap Ayarları';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Ortak kütüphaneleri (Bootstrap, FontAwesome) barındıran üst dosyamızı dahil ediyoruz. -->
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        
        body { background-color: #fff; }
        
        /* Satır hizalamaları için esnek Flexbox yapısı kullanıyoruz. */
        .settings-row {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 2rem; margin-bottom: 2rem; border-bottom: 1px solid #f8fafc;
        }
        
        /* Modern Input Tasarımı: Tıklandığında (focus) çirkin tarayıcı çerçevesi yerine özel bir gölge (box-shadow) veriyoruz. */
        .clean-input {
            width: 100%; background-color: #f8fafc; border: none;
            border-radius: 0.5rem; padding: 0.6rem 0.8rem; font-size: 0.875rem;
            color: #0f172a; outline: none; transition: all 0.2s;
        }
        .clean-input:focus { box-shadow: 0 0 0 2px #0d9488; background-color: #fff; }
        
        .action-btn { background: transparent; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer; }
        .btn-edit { color: #1e293b; }
        .btn-save { color: #0d9488; font-weight: bold; margin-right: 0.75rem; }
        .btn-cancel { color: #94a3b8; }
        
        /* Avatar Değiştirme Alanı: Kullanıcı resmin üzerine gelince transform: scale ile hafifçe büyütüyoruz. */
        .avatar-wrapper {
            position: relative; width: 45px; height: 45px; cursor: pointer;
            transition: transform 0.2s;
        }
        .avatar-wrapper:hover { transform: scale(1.1); }
        .avatar-main { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid #f1f5f9; }
        
        /* Avatarın köşesindeki minik kamera ikonunun CSS ile tam köşeye oturtulması */
        .camera-icon {
            position: absolute; bottom: -2px; right: -2px;
            background: #0d9488; color: white; width: 18px; height: 18px;
            border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
        }
        .btn-danger-link { color: #dc2626; text-decoration: none; font-weight: 500; font-size: 0.875rem; display: block; margin-top: 1rem; }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5 d-flex justify-content-center">
                    <div style="max-width: 768px; width: 100%;">
                        
                        <h1 class="fw-bold text-dark mb-5 pb-3" style="font-size: 3.5rem; letter-spacing: -2px; font-family: 'Instrument Serif', serif; font-style: italic;">Settings</h1>

                        <div>
                            <!-- EMAİL GÜNCELLEME ALANI -->
                            <div class="settings-row">
                                <div class="flex-grow-1">
                                    <p class="fw-bold text-dark mb-1" style="font-size: 0.875rem;">Email address</p>
                                    
                                    <!-- Başlangıçta sadece metin görünür. htmlspecialchars ile XSS açıklarını kapatıyoruz -->
                                    <div id="email-display">
                                        <p class="text-secondary mb-0" style="font-size: 0.875rem;" id="email-text"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                    
                                    <!-- Kullanıcı Edit'e basınca DOM ile d-none sınıfı silinip bu input açılacak -->
                                    <div id="email-input-container" class="d-none mt-2">
                                        <input type="email" id="email-input" class="clean-input" value="<?= htmlspecialchars($user['email']) ?>">
                                    </div>
                                </div>
                                <div class="ms-4">
                                    <button id="email-edit-btn" class="action-btn btn-edit" onclick="toggleEdit('email', true)">Edit</button>
                                    <div id="email-action-btns" class="d-none">
                                        <button class="action-btn btn-save" onclick="saveField('email')">Save</button>
                                        <button class="action-btn btn-cancel" onclick="toggleEdit('email', false)">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- KULLANICI ADI GÜNCELLEME ALANI -->
                            <div class="settings-row">
                                <div class="flex-grow-1">
                                    <p class="fw-bold text-dark mb-1" style="font-size: 0.875rem;">Username and subdomain</p>
                                    <div id="username-display">
                                        <p class="text-secondary mb-0" style="font-size: 0.875rem;" id="username-text">@<?= htmlspecialchars($user['username']) ?></p>
                                    </div>
                                    <div id="username-input-container" class="d-none mt-2">
                                        <div class="d-flex align-items-center clean-input">
                                            <span class="text-muted small">@</span>
                                            <input type="text" id="username-input" class="border-0 bg-transparent flex-grow-1 ms-1 small" style="outline:none;" value="<?= htmlspecialchars($user['username']) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="ms-4">
                                    <button id="username-edit-btn" class="action-btn btn-edit" onclick="toggleEdit('username', true)">Edit</button>
                                    <div id="username-action-btns" class="d-none">
                                        <button class="action-btn btn-save" onclick="saveField('username')">Save</button>
                                        <button class="action-btn btn-cancel" onclick="toggleEdit('username', false)">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- PROFİL RESMİ (AVATAR) GÜNCELLEME ALANI -->
                            <div class="settings-row align-items-center">
                                <div>
                                    <p class="fw-bold text-dark mb-1" style="font-size: 0.875rem;">Profile information</p>
                                    <p class="text-secondary mb-0" style="font-size: 0.875rem;">Edit your photo, name, and bio</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-secondary fw-medium small d-none d-sm-block"><?= htmlspecialchars($user['ad_soyad']) ?></span>
                                    
                                    <!-- Resmi tıklanabilir yaptık. Tıklanınca gizli olan <input type="file"> elemanını JS ile tetikliyoruz. -->
                                    <div class="avatar-wrapper" onclick="document.getElementById('avatar-upload').click();">
                                        <?php 
                                            // Avatar yoksa API'den otomatik harf logolu avatar üretiyoruz.
                                            $avatar = !empty($user['avatar_url']) ? $user['avatar_url'] : "https://ui-avatars.com/api/?name=".urlencode($user['ad_soyad'])."&background=0d9488&color=fff";
                                        ?>
                                        <img src="<?= $avatar ?>" class="avatar-main" id="current-avatar">
                                        <div class="camera-icon"><i class="fa-solid fa-camera"></i></div>
                                    </div>
                                    
                                    <!-- Dosya seçildiği an (onchange) AJAX ile resmi yollayacak fonksiyon tetikleniyor. -->
                                    <input type="file" id="avatar-upload" class="d-none" accept="image/*" onchange="uploadAvatar(this)">
                                </div>
                            </div>

                            <!-- HESAP SİLME / DONDURMA -->
                            <div class="pt-2">
                                <div class="pt-4 border-top">
                                    <button onclick="accountAction('deactivate')" class="action-btn text-teal fw-bold d-block mb-3 p-0" style="font-size: 0.875rem;">Deactivate account</button>
                                    <button onclick="accountAction('delete')" class="action-btn btn-danger-link p-0">Delete account</button>
                                </div>
                            </div>

                        </div>

                        <footer class="mt-5 pt-5 pb-4 border-top text-center text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.2em;">
                            Postify Professional Edition • 2026
                        </footer>

                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- AJAX ve DOM MANİPÜLASYONU -->
    <script>
        // İptal butonuna basıldığında verinin eski haline dönmesi için, sayfa yüklenirken verileri JS Nesnesine (Object) yedekliyoruz.
        var currentData = {
            email: "<?= $user['email'] ?>",
            username: "<?= $user['username'] ?>"
        };

        // DOM Manipülasyonu: Metinlerin gizlenip Input kutularının (form) açılmasını sağlayan arayüz fonksiyonu.
        function toggleEdit(field, isEditing) {
            document.getElementById(field + '-display').classList.toggle('d-none', isEditing);
            document.getElementById(field + '-input-container').classList.toggle('d-none', !isEditing);
            document.getElementById(field + '-edit-btn').classList.toggle('d-none', isEditing);
            document.getElementById(field + '-action-btns').classList.toggle('d-none', !isEditing);
            
            // Kullanıcı UX (Deneyimi) için input açılır açılmaz imleci içine odaklıyoruz (focus).
            if(isEditing) {
                document.getElementById(field + '-input').focus();
            }
        }

        // BİLGİLERİ KAYDET (Email/Username Güncellemesi)
        function saveField(field) {
            var newValue = document.getElementById(field + '-input').value;
            
            // Sayfayı yenilemeden (Asenkron) form verisi yollamak için fetch kullanıyoruz (Ders 04).
            fetch('api/profil_guncelle.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: "field=" + field + "&value=" + encodeURIComponent(newValue)
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if(data.status === 'success') {
                    // Veri başarıyla güncellendiyse DOM üzerindeki metni değiştirip formları kapatıyoruz.
                    currentData[field] = newValue;
                    var prefix = (field === 'username') ? '@' : '';
                    document.getElementById(field + '-text').innerText = prefix + newValue;
                    toggleEdit(field, false);
                } else {
                    alert('Hata: ' + data.message);
                }
            });
        }

        // PROFİL RESMİ YÜKLEME (AJAX + FormData Kullanımı)
        function uploadAvatar(input) {
            // Kullanıcı iptal demeyip gerçekten bir dosya seçtiyse...
            if (input.files && input.files[0]) {
                
                // Normalde formlarla dosya yollanır. Biz AJAX ile dosya yollayacağımız için JS'in yerleşik 'FormData' nesnesini oluşturuyoruz.
                var formData = new FormData();
                formData.append('avatar', input.files[0]);

                // Fetch API ile resmi POST metoduyla doğrudan sunucuya aktarıyoruz.
                fetch('api/avatar_yukle.php', {
                    method: 'POST',
                    body: formData // Headers kısmına multipart/form-data yazmamıza gerek yok, FormData bunu otomatik halleder.
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if(data.status === 'success') {
                        // Resim başarıyla yüklenirse sayfa yenilenmeden kullanıcının profil resmini DOM üzerinden anında güncelliyoruz.
                        document.getElementById('current-avatar').src = data.new_url;
                        
                        // Menü (Topbar) üzerindeki minik resmi de yakalayıp değiştiriyoruz ki UX kusursuz olsun.
                        var topbarAvatar = document.querySelector('header img');
                        if(topbarAvatar) {
                            topbarAvatar.src = data.new_url;
                        }
                    } else {
                        alert('Resim yüklenemedi: ' + data.message);
                    }
                });
            }
        }

        // HESAP İŞLEMLERİ (Silme veya Dondurma)
        function accountAction(type) {
            // Silme gibi kritik bir işlem öncesi derste gördüğümüz confirm() metodu ile JavaScript tabanlı teyit alıyoruz.
            var msg = "";
            if (type === 'delete') {
                msg = 'DİKKAT! Hesabınızı kalıcı olarak silmek üzeresiniz. Bu işlem geri alınamaz. Onaylıyor musunuz?';
            } else {
                msg = 'Hesabınızı dondurmak istediğinize emin misiniz?';
            }
            
            if(confirm(msg)) {
                fetch('api/hesap_islemleri.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: "action=" + type
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if(data.status === 'success') {
                        alert('İşlem başarılı. Hoşça kalın!');
                        window.location.href = 'logout.php'; // İşlem başarılıysa oturumu kapatma sayfasına yönlendiriyoruz.
                    }
                });
            }
        }
    </script>
</body>
</html>