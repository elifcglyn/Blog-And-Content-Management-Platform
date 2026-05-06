<style>
    /* Yan Menü (Sidebar) Ana CSS Ayarları */
    .sidebar {
        background-color: #f8fafc;
        border-right: 1px solid #f1f5f9;
        width: 260px;
        min-width: 260px;
        transition: transform 0.3s ease-in-out;
        z-index: 1050; /* Her şeyin üstünde kalması için */
    }
    
    .nav-link-custom {
        color: #64748b; font-weight: 500; padding: 0.8rem 1rem;
        border-radius: 0.75rem; transition: all 0.3s ease;
        display: flex; align-items: center; gap: 0.75rem;
    }
    
    .nav-link-custom:hover { background-color: #f1f5f9; color: #0f172a; }
    .nav-link-custom.active { background-color: #f0fdfa; color: #0d9488; font-weight: bold; }

    /* MOBİL İÇİN RESPONSIVE (DUYARLI) TASARIM */
    @media (max-width: 767.98px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            /* Mobilde başlangıçta ekranın solunda (dışarıda) saklıdır */
            transform: translateX(-100%); 
        }
        
        /* JavaScript bu sınıfı eklediğinde menü ekrana kayarak girer */
        .sidebar.show {
            transform: translateX(0);
        }
        
        /* Yan menü açıldığında arka planı karartan şeffaf katman */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 1040;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
    }
</style>

<!-- Arkaplan karartma div'i (Sadece mobilde menü açılınca görünür) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar vh-100 sticky-top d-flex flex-column p-3" id="mainSidebar">
    
    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
        <a href="index.php" class="text-decoration-none text-teal fw-bold fs-5 d-flex align-items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i> <span class="brand-text">Postify</span>
        </a>
        <!-- Sadece Mobilde Görünecek Menüyü Kapatma (Çarpı) Butonu -->
        <button class="btn btn-link text-secondary d-md-none p-0" id="sidebarCloseBtn">
            <i class="fa-solid fa-xmark fs-4"></i>
        </button>
    </div>
    
    <ul class="nav flex-column gap-2 mt-2 w-100">
        <li><a href="index.php" class="nav-link-custom <?= ($activePage == 'home') ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> <span class="nav-text">Ana Sayfa</span></a></li>
        <li><a href="kesfet.php" class="nav-link-custom <?= ($activePage == 'kesfet') ? 'active' : '' ?>"><i class="fa-solid fa-compass"></i> <span class="nav-text">Keşfet</span></a></li>
        <li><a href="analiz.php" class="nav-link-custom <?= ($activePage == 'analiz') ? 'active' : '' ?>"><i class="fa-solid fa-chart-simple"></i> <span class="nav-text">İstatistikler</span></a></li>
        <li><a href="kaydedilenler.php" class="nav-link-custom <?= ($activePage == 'bookmarks') ? 'active' : '' ?>"><i class="fa-solid fa-bookmark"></i> <span class="nav-text">Kaydedilenler</span></a></li>
        <li><a href="bildirimler.php" class="nav-link-custom <?= ($activePage == 'notif') ? 'active' : '' ?>"><i class="fa-solid fa-bell"></i> <span class="nav-text">Bildirimler</span></a></li>
        
        <hr class="nav-text border-secondary opacity-25">
        
        <li>
            <a href="yazilarim.php" class="nav-link-custom <?= ($activePage == 'yazilarim') ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i> <span class="nav-text">Yazılarım</span>
            </a>
        </li>
        <li><a href="profil.php" class="nav-link-custom <?= ($activePage == 'profil') ? 'active' : '' ?>"><i class="fa-solid fa-user"></i> <span class="nav-text">Profilim</span></a></li>
        <li><a href="ayarlar.php" class="nav-link-custom <?= ($activePage == 'ayarlar') ? 'active' : '' ?>"><i class="fa-solid fa-gear"></i> <span class="nav-text">Ayarlar</span></a></li>
    </ul>

    <div class="mt-auto border-top pt-3 nav-text">
        <a href="logout.php" class="nav-link-custom text-danger">
            <i class="fa-solid fa-right-from-bracket"></i> <span class="nav-text">Çıkış Yap</span>
        </a>
    </div>
</aside>