<header class="py-3 px-4 px-md-5 mb-4 border-bottom bg-white sticky-top d-flex justify-content-between align-items-center" style="z-index: 1020;">
    
    <!-- SOL TARAF: Mobil Menü Butonu + Hoş Geldin Mesajı -->
    <div class="d-flex align-items-center gap-3">
        <!-- Bu buton SADECE MOBİLDE (d-md-none) çıkar ve yan menüyü açar -->
        <button class="btn btn-light d-md-none d-flex align-items-center justify-content-center shadow-sm border" id="mobileMenuBtn" style="width: 40px; height: 40px;">
            <i class="fa-solid fa-bars text-secondary"></i>
        </button>

        <div class="serif-italic fw-bold" style="font-size: 1.5rem; color: #0d9488; margin-bottom: 0;">
            Hoş geldin, <?= htmlspecialchars(explode(' ', $_SESSION['ad_soyad'] ?? 'Yazar')[0]) ?> 👋
        </div>
    </div>
    
    <!-- SAĞ TARAF: Yeni Yazı, Profil ve Çıkış Butonları -->
    <div class="d-flex align-items-center gap-3">
        
        <a href="yeni-yazi.php" class="btn bg-teal-light text-teal rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; transition: 0.3s;" title="Yeni Yazı Oluştur">
            <i class="fa-solid fa-plus fs-5"></i>
        </a>

        <?php 
            $isim = $_SESSION['ad_soyad'] ?? 'Kullanıcı';
            $rol = $_SESSION['rol'] ?? 'Üye';
            $avatar = $_SESSION['avatar_url'] ?? '';
            
            if(empty($avatar)) {
                $isim_url = urlencode($isim);
                $avatar = "https://ui-avatars.com/api/?name={$isim_url}&background=0d9488&color=fff&t=" . time();
            }
        ?>

        <a href="profil.php" class="text-decoration-none text-dark d-flex align-items-center gap-2" style="font-size: 0.9rem;">
            <div class="text-end d-none d-md-block lh-sm">
                <div class="fw-bold"><?= htmlspecialchars($isim) ?></div>
                <div class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;"><?= htmlspecialchars($rol) ?></div>
            </div>
            <img src="<?= htmlspecialchars($avatar) ?>" class="rounded-circle shadow-sm border border-2 border-white" width="40" height="40" style="object-fit: cover;" alt="Profil">
        </a>
        
        <a href="logout.php" class="btn btn-light text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm border d-none d-md-flex" style="width: 40px; height: 40px; transition: 0.3s;" title="Çıkış Yap" onmouseover="this.classList.replace('btn-light', 'btn-danger'); this.classList.replace('text-danger', 'text-white');" onmouseout="this.classList.replace('btn-danger', 'btn-light'); this.classList.replace('text-white', 'text-danger');">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>

    </div>
</header>

<!-- SİTENİN TÜM SAYFALARINDA ÇALIŞACAK MERKEZİ JAVASCRIPT KODU -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn'); // Topbardaki buton
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn'); // Sidebar içindeki çarpı
        const sidebar = document.getElementById('mainSidebar'); // Menü
        const overlay = document.getElementById('sidebarOverlay'); // Karartma efekti
        
        // 1. Topbardaki hamburger ikonuna basılınca menüyü AÇ
        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', (e) => {
                e.preventDefault();
                sidebar.classList.add('show');
                if(overlay) overlay.classList.add('show');
            });
        }

        // 2. Sidebar'ın içindeki çarpıya basılınca menüyü KAPAT
        if (sidebarCloseBtn && sidebar) {
            sidebarCloseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                sidebar.classList.remove('show');
                if(overlay) overlay.classList.remove('show');
            });
        }

        // 3. Karartılmış boşluğa tıklayınca menüyü KAPAT
        if(overlay && sidebar) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    });
</script>