<?php 
  $activePage = 'ayarlar'; // Profil/Ayarlar menüsü aktif olur
  $pageTitle = 'Hesap Ayarları';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        /* Satır Tasarımları */
        .settings-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #f8fafc;
        }

        /* Minimalist Inputlar */
        .clean-input {
            width: 100%;
            background-color: #f8fafc;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #0f172a;
            outline: none;
            transition: box-shadow 0.2s;
        }
        .clean-input:focus {
            box-shadow: 0 0 0 2px #0d9488;
        }

        /* Buton Stilleri */
        .action-btn {
            background: transparent;
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0;
            cursor: pointer;
            transition: color 0.2s;
        }
        .btn-edit { color: #1e293b; }
        .btn-edit:hover { color: #000; }
        
        .btn-save { color: #0d9488; font-weight: bold; margin-right: 0.75rem; }
        .btn-save:hover { color: #0f766e; }
        
        .btn-cancel { color: #94a3b8; }
        .btn-cancel:hover { color: #475569; }

        .btn-danger-link { color: #dc2626; text-decoration: none; font-weight: 500; font-size: 0.875rem; display: block; margin-bottom: 1.5rem; }
        .btn-danger-link:hover { color: #b91c1c; }

        /* Profil Avatar */
        .avatar-sm {
            width: 40px; height: 40px; border-radius: 50%;
            object-fit: cover; border: 2px solid #f1f5f9;
            transition: transform 0.2s;
        }
        .profile-link:hover .avatar-sm { transform: scale(1.05); }
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
                        
                        <h1 class="fw-bold text-dark mb-5 pb-3" style="font-size: 3rem; letter-spacing: -1px;">Settings</h1>

                        <div>
                            
                            <div class="settings-row">
                                <div class="flex-grow-1">
                                    <p class="fw-bold text-dark mb-1" style="font-size: 0.875rem;">Email address</p>
                                    
                                    <div id="email-display">
                                        <p class="text-secondary mb-0" style="font-size: 0.875rem;" id="email-text">elifalanur7@gmail.com</p>
                                    </div>
                                    
                                    <div id="email-input-container" class="d-none mt-2">
                                        <input type="email" id="email-input" class="clean-input" value="elifalanur7@gmail.com">
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
                                        <p class="text-secondary mb-0" style="font-size: 0.875rem;" id="username-text">@elifcaglayan</p>
                                    </div>
                                    
                                    <div id="username-input-container" class="d-none mt-2">
                                        <div class="d-flex align-items-center clean-input" style="padding: 0 0.75rem;">
                                            <span class="text-muted" style="font-size: 0.875rem;">@</span>
                                            <input type="text" id="username-input" class="border-0 bg-transparent flex-grow-1 py-2 ms-1" style="font-size: 0.875rem; box-shadow: none; outline: none;" value="elifcaglayan">
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
                                <a href="profil.php" class="text-decoration-none d-flex align-items-center gap-3 profile-link">
                                    <span class="text-secondary fw-medium" style="font-size: 0.875rem; transition: color 0.3s;">Elif Çağlayan</span>
                                    <img src="https://ui-avatars.com/api/?name=Elif+Caglayan&background=f97316&color=fff" class="avatar-sm">
                                </a>
                            </div>

                            <div class="pt-2">
                                <div class="d-flex justify-content-between align-items-center mb-4 cursor-pointer" style="cursor: pointer;">
                                    <p class="text-dark fw-medium mb-0" style="font-size: 0.875rem;">Profile design</p>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-muted" style="font-size: 0.75rem;"></i>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-5 cursor-pointer" style="cursor: pointer;">
                                    <p class="text-dark fw-medium mb-0" style="font-size: 0.875rem;">Custom domain</p>
                                    <p class="text-muted mb-0" style="font-size: 0.875rem;">None</p>
                                </div>

                                <div class="pt-4 border-top">
                                    <a href="#" class="text-decoration-none fw-medium d-block mb-3" style="color: #0d9488; font-size: 0.875rem;">Deactivate account</a>
                                    <a href="#" class="btn-danger-link">Delete account</a>
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
        // Ders 4: Nesne (Object) kullanımı
        let userData = {
            email: "elifalanur7@gmail.com",
            username: "elifcaglayan"
        };

        // Ders 5: DOM Manipülasyonu ile Görünüm Değiştirme (React State yerine)
        function toggleEdit(field, isEditing) {
            const displayEl = document.getElementById(field + '-display');
            const inputContainer = document.getElementById(field + '-input-container');
            const editBtn = document.getElementById(field + '-edit-btn');
            const actionBtns = document.getElementById(field + '-action-btns');
            const inputEl = document.getElementById(field + '-input');

            if (isEditing) {
                displayEl.classList.add('d-none');
                inputContainer.classList.remove('d-none');
                editBtn.classList.add('d-none');
                actionBtns.classList.remove('d-none');
                setTimeout(() => inputEl.focus(), 50);
            } else {
                displayEl.classList.remove('d-none');
                inputContainer.classList.add('d-none');
                editBtn.classList.remove('d-none');
                actionBtns.classList.add('d-none');
                inputEl.value = userData[field];
            }
        }

        // Ders 8: Form/Veri Kaydetme (Gerçekte PHP'ye POST atar)
        function saveField(field) {
            const newValue = document.getElementById(field + '-input').value;
            const textDisplay = document.getElementById(field + '-text');

            if (!newValue.trim()) {
                alert("Bu alan boş bırakılamaz!");
                return;
            }

            userData[field] = newValue;

            if (field === 'username') {
                textDisplay.innerText = "@" + newValue;
            } else {
                textDisplay.innerText = newValue;
            }

            toggleEdit(field, false);
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