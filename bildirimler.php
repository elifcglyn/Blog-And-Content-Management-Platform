<?php 
  $activePage = 'notif'; // Sidebar'da Bildirimler linkini aktif yapar
  $pageTitle = 'Bildirimler'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'header_include.php'; ?>
    <title><?= $pageTitle ?> - Postify</title>

    <style>
        .notif-card {
            background-color: #f8fafc;
            border-radius: 1.5rem;
            border: 1px solid #f1f5f9;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .notif-card:hover { transform: translateY(-2px); border-color: #e2e8f0; }
        .icon-circle {
            width: 45px; height: 45px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .bg-like { background-color: #fff1f2; color: #ef4444; }
        .bg-comment { background-color: #f0f9ff; color: #0ea5e9; }
        .bg-system { background-color: #f0fdf4; color: #22c55e; }
        
        .new-badge {
            background-color: #f0fdfa; color: #0d9488;
            font-weight: bold; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="d-flex flex-nowrap min-vh-100">
            
            <?php include 'sidebar.php'; ?>

            <main class="flex-grow-1" style="min-width: 0; background-color: #fff;">
                
                <?php include 'topbar.php'; ?>

                <div class="px-4 px-md-5 pb-5">
                    
                    <header class="d-flex justify-content-between align-items-center mb-5">
                        <div>
                            <h1 class="serif-italic fs-1 fw-bold text-dark mb-1">Bildirimler</h1>
                            <p class="text-secondary mb-0">Etkileşimleri ve güncellemeleri yönet.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <span class="new-badge">2 YENİ</span>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3">Tümünü Okundu İşaretle</button>
                        </div>
                    </header>

                    <div class="notif-list" style="max-width: 800px;">
                        
                        <div class="notif-card">
                            <div class="icon-circle bg-like"><i class="fa-solid fa-heart"></i></div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-dark"><strong>Sarah Chen</strong> yazını beğendi</p>
                                <p class="mb-0 text-teal fw-bold small">"React Compiler: Geleceğe Bakış"</p>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">5 DK ÖNCE</small>
                            </div>
                        </div>

                        <div class="notif-card">
                            <div class="icon-circle bg-comment"><i class="fa-solid fa-comment"></i></div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-dark"><strong>Alex Johnson</strong> yorum yaptı</p>
                                <p class="mb-0 text-teal fw-bold small">"Tailwind v4"</p>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">23 DK ÖNCE</small>
                            </div>
                        </div>

                        <div class="notif-card">
                            <div class="icon-circle bg-system"><i class="fa-solid fa-code-branch"></i></div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-dark"><strong>Sistem</strong> bir güncelleme var</p>
                                <p class="mb-0 text-teal fw-bold small">"Postify v2.5 Yayında!"</p>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">1 SA ÖNCE</small>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
            document.getElementById('mainSidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>