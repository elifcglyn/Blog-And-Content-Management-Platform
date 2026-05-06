<?php 
session_start();
require_once 'api/baglanti.php';

// Güvenlik: Giriş yapmayan giremez
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

// Veritabanından en güncel bilgileri çekiyoruz (Yedek olarak Session'dan da besleniyoruz)
$sorgu = $db->prepare("SELECT email, username, ad_soyad, avatar_url FROM users WHERE id = ?");
$sorgu->execute([$_SESSION['kullanici_id']]);
$user = $sorgu->fetch(PDO::FETCH_ASSOC);

$activePage = 'ayarlar'; 
$pageTitle = 'Hesap Ayarları';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');
        
        body { background-color: #fff; }
        .settings-row {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 2rem; margin-bottom: 2rem; border-bottom: 1px solid #f8fafc;
        }
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
        
        .avatar-wrapper {
            position: relative; width: 45px; height: 45px; cursor: pointer;
            transition: transform 0.2s;
        }
        .avatar-wrapper:hover { transform: scale(1.1); }
        .avatar-main { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid #f1f5f9; }
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
                            <div class="settings-row">
                                <div class="flex-grow-1">
                                    <p class="fw-bold text-dark mb-1" style="font-size: 0.875rem;">Email address</p>
                                    <div id="email-display">
                                        <p class="text-secondary mb-0" style="font-size: 0.875rem;" id="email-text"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
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

                            <div class="settings-row align-items-center">
                                <div>
                                    <p class="fw-bold text-dark mb-1" style="font-size: 0.875rem;">Profile information</p>
                                    <p class="text-secondary mb-0" style="font-size: 0.875rem;">Edit your photo, name, and bio</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-secondary fw-medium small d-none d-sm-block"><?= htmlspecialchars($user['ad_soyad']) ?></span>
                                    
                                    <div class="avatar-wrapper" onclick="document.getElementById('avatar-upload').click();">
                                        <?php 
                                            $avatar = !empty($user['avatar_url']) ? $user['avatar_url'] : "https://ui-avatars.com/api/?name=".urlencode($user['ad_soyad'])."&background=0d9488&color=fff";
                                        ?>
                                        <img src="<?= $avatar ?>" class="avatar-main" id="current-avatar">
                                        <div class="camera-icon"><i class="fa-solid fa-camera"></i></div>
                                    </div>
                                    <input type="file" id="avatar-upload" class="d-none" accept="image/*" onchange="uploadAvatar(this)">
                                </div>
                            </div>

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

    <script>
        let currentData = {
            email: "<?= $user['email'] ?>",
            username: "<?= $user['username'] ?>"
        };

        function toggleEdit(field, isEditing) {
            document.getElementById(field + '-display').classList.toggle('d-none', isEditing);
            document.getElementById(field + '-input-container').classList.toggle('d-none', !isEditing);
            document.getElementById(field + '-edit-btn').classList.toggle('d-none', isEditing);
            document.getElementById(field + '-action-btns').classList.toggle('d-none', !isEditing);
            if(isEditing) document.getElementById(field + '-input').focus();
        }

        // BİLGİLERİ KAYDET (Email/Username)
        function saveField(field) {
            const newValue = document.getElementById(field + '-input').value;
            
            fetch('api/profil_guncelle.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `field=${field}&value=${encodeURIComponent(newValue)}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    currentData[field] = newValue;
                    document.getElementById(field + '-text').innerText = (field === 'username' ? '@' : '') + newValue;
                    toggleEdit(field, false);
                } else {
                    alert('Hata: ' + data.message);
                }
            });
        }

        // PROFİL RESMİ YÜKLEME
        function uploadAvatar(input) {
            if (input.files && input.files[0]) {
                let formData = new FormData();
                formData.append('avatar', input.files[0]);

                fetch('api/avatar_yukle.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById('current-avatar').src = data.new_url;
                        // Üst bardaki resmi de hemen güncelle
                        const topbarAvatar = document.querySelector('header img');
                        if(topbarAvatar) topbarAvatar.src = data.new_url;
                    } else {
                        alert('Resim yüklenemedi: ' + data.message);
                    }
                });
            }
        }

        // HESAP İŞLEMLERİ
        function accountAction(type) {
            let msg = type === 'delete' ? 'DİKKAT! Hesabınızı kalıcı olarak silmek üzeresiniz. Bu işlem geri alınamaz. Onaylıyor musunuz?' : 'Hesabınızı dondurmak istediğinize emin misiniz?';
            if(confirm(msg)) {
                fetch('api/hesap_islemleri.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=${type}`
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        alert('İşlem başarılı. Hoşça kalın!');
                        window.location.href = 'logout.php';
                    }
                });
            }
        }
    </script>
</body>
</html>