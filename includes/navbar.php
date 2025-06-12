<!-- Top Header -->
<?php
if ($devoloperMode) {

    ?>
    <div class="row  alert-warning"
        style="display:block; width: 100%; ; text-align: center; padding:8px 16px; ">
        Geliştirici Modu Aktif
    </div>
    <?php
}
?>

<header class="top-header">

    <div class="header-left">
        <button class="btn btn-link d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="ms-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <div class="header-right">
        <!-- Search -->
        <div class="search-box">
            <input type="text" data-i18n-placeholder="search" placeholder="Ara...">
            <i class="bi bi-search"></i>
        </div>

        <!-- Theme Toggle -->
        <button class="btn btn-link" id="themeToggle">
            <i class="bi bi-moon-fill"></i>
        </button>

        <!-- Language Selector -->
        <div class="language-selector">
            <button onclick="changeLanguage('tr')" class="lang-btn" title="Türkçe">
                <span class="lang-text">TR</span>
            </button>
            <button onclick="changeLanguage('en')" class="lang-btn" title="English">
                <span class="lang-text">EN</span>
            </button>
        </div>

        <!-- Notifications -->
        <div class="dropdown">
            <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                    <span class="visually-hidden">okunmamış bildirim</span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                <div class="notification-header">
                    <h6 class="mb-0">Bildirimler</h6>
                    <a href="#" class="text-muted small">Tümünü Okundu İşaretle</a>
                </div>
                <div class="notification-body">
                    <a class="dropdown-item unread" href="#">
                        <div class="d-flex align-items-center">
                            <div class="notification-icon bg-primary">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0">Yeni kullanıcı kaydı</p>
                                <small class="text-muted">5 dakika önce</small>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item unread" href="#">
                        <div class="d-flex align-items-center">
                            <div class="notification-icon bg-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0">Sistem güncellemesi</p>
                                <small class="text-muted">1 saat önce</small>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex align-items-center">
                            <div class="notification-icon bg-info">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0">Yeni mesaj</p>
                                <small class="text-muted">2 saat önce</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="notification-footer">
                    <a href="#" class="text-center d-block">Tüm Bildirimleri Gör</a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="dropdown">
            <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                <div class="avatar-placeholder">
                    <i class="bi bi-person-circle"></i>
                </div>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="#"><i class="bi bi-person"></i> <span
                        data-i18n="profile">Profil</span></a>
                <a class="dropdown-item" href="#"><i class="bi bi-gear"></i> <span
                        data-i18n="settings">Ayarlar</span></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= $router->controllers('logoutController') ?>"><i
                        class="bi bi-box-arrow-right"></i> <span data-i18n="logout">Çıkış</span></a>
            </div>
        </div>
    </div>
</header>