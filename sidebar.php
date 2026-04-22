<aside class="sidebar vh-100 sticky-top d-flex flex-column p-3" id="mainSidebar">
    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
        <a href="index.php" class="text-decoration-none text-teal fw-bold fs-5 d-flex align-items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i> <span class="brand-text">Postify</span>
        </a>
        <button class="toggle-btn" id="sidebarToggleBtn"><i class="fa-solid fa-bars"></i></button>
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
        <a href="auth.php" class="nav-link-custom text-danger">
            <i class="fa-solid fa-right-from-bracket"></i> <span class="nav-text">Çıkış Yap</span>
        </a>
    </div>
</aside>