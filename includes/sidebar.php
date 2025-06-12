<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../classes/Router.php";
$router = Router::getInstance();
$devoloperMode = $GLOBALS['app_config']['debug'];

// Mevcut URL'yi al
$current_url = $_SERVER['REQUEST_URI'];
$current_page = basename(parse_url($current_url, PHP_URL_PATH));

// Active class kontrolü için yardımcı fonksiyon
function isActive($url, $exact = true) {
    global $current_url, $current_page;
    if ($exact) {
        return $current_page === $url;
    }
    return strpos($current_url, $url) !== false;
}
?>
<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-header">
        <h3 data-i18n="dashboard">Dashboard</h3>
    </div>
    <div class="sidebar-menu">
        <a href="<?= $router->getPanelUrl() ?>/index.php" class="menu-item <?php echo isActive('index.php') ? 'active' : ''; ?>">
            <i class="bi bi-house-door"></i>
            <span data-i18n="dashboard">Panel</span>
        </a>

        <a href="#" class="menu-item <?php echo isActive('users.php') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span data-i18n="users">Kullanıcılar</span>
            <i class="bi bi-chevron-down arrow"></i>
        </a>
        <div class="submenu">
            <a href="#" class="submenu-item <?php echo isActive('users.php?action=new') ? 'active' : ''; ?>">
                <i class="bi bi-person-plus"></i>
                <span data-i18n="newUser">Yeni Kullanıcı</span>
            </a>
            <a href="#" class="submenu-item <?php echo isActive('users.php?action=roles') ? 'active' : ''; ?>">
                <i class="bi bi-person-gear"></i>
                <span data-i18n="userRoles">Kullanıcı Rolleri</span>
            </a>
            <a href="#" class="submenu-item <?php echo isActive('users.php?action=permissions') ? 'active' : ''; ?>">
                <i class="bi bi-shield-lock"></i>
                <span data-i18n="permissions">İzinler</span>
            </a>
        </div>

        <?php if ($devoloperMode): ?>
            <a href="#" class="menu-item <?php echo isActive('docs/', false) ? 'active' : ''; ?>">
                <i class="bi bi-code-square"></i>
                <span data-i18n="developerTools">Geliştirici Araçları</span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>
            <div class="submenu">
                <a href="<?= $router->getPanelUrl() ?>/docs/docs.html" target="_blank" class="submenu-item <?php echo isActive('docs/docs.html', false) ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-code"></i>
                    <span data-i18n="documents">Dökümanlar</span>
                </a>
                <a href="<?= $router->getPanelUrl() ?>/docs/vt.html" target="_blank" class="submenu-item <?php echo isActive('docs/vt.html', false) ? 'active' : ''; ?>">
                    <i class="bi bi-database"></i>
                    <span data-i18n="databaseDocs">Veritabanı Dokümanları</span>
                </a>
                <a href="https://icons.getbootstrap.com/" target="_blank" class="submenu-item <?php echo isActive('icons.html', false) ? 'active' : ''; ?>">
                    <i class="bi bi-journal-text"></i>
                    <span data-i18n="icons">İkonlar</span>
                </a>
            </div>
        <?php endif; ?>

        <a href="#" class="menu-item <?php echo isActive('settings.php') ? 'active' : ''; ?>">
            <i class="bi bi-gear"></i>
            <span data-i18n="settings">Ayarlar</span>
            <i class="bi bi-chevron-down arrow"></i>
        </a>
        <div class="submenu">
            <a href="#" class="submenu-item <?php echo isActive('settings.php?page=general') ? 'active' : ''; ?>">
                <i class="bi bi-globe"></i>
                <span data-i18n="generalSettings">Genel Ayarlar</span>
            </a>
            <a href="#" class="submenu-item <?php echo isActive('settings.php?page=email') ? 'active' : ''; ?>">
                <i class="bi bi-envelope"></i>
                <span data-i18n="emailSettings">E-posta Ayarları</span>
            </a>
            <a href="#" class="submenu-item <?php echo isActive('settings.php?page=security') ? 'active' : ''; ?>">
                <i class="bi bi-shield"></i>
                <span data-i18n="security">Güvenlik</span>
            </a>
        </div>

        <a href="<?=$router->controllers('logoutController')?>" class="menu-item <?php echo isActive('logout.php') ? 'active' : ''; ?>">
            <i class="bi bi-box-arrow-right"></i>
            <span data-i18n="logout">Çıkış</span>
        </a>
    </div>
    <!-- Destek Alanı -->
    <div class="sidebar-support">
        <div class="support-header">
            <i class="bi bi-headset"></i>
            <span data-i18n="support">Destek</span>
        </div>
        <div class="support-content">
            <p data-i18n="needHelp">Yardıma mı ihtiyacınız var?</p>
            <a href="#" class="support-button <?php echo isActive('support.php?type=live') ? 'active' : ''; ?>">
                <i class="bi bi-chat-dots"></i>
                <span data-i18n="liveSupport">Canlı Destek</span>
            </a>
           
        </div>
    </div>
</nav>