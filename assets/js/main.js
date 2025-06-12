// Loading Overlay
function showLoading() {
    document.querySelector('.loading-overlay').classList.add('active');
}

function hideLoading() {
    document.querySelector('.loading-overlay').classList.remove('active');
}

// Sayfa yüklendiğinde preloader'ı göster ve tamamen yüklendikten sonra kapat
document.addEventListener('DOMContentLoaded', () => {
    showLoading();
});

window.addEventListener('load', () => {
    hideLoading();
});

// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

// Check for saved theme preference
const savedTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', savedTheme);
updateThemeIcon(savedTheme);

themeToggle.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
});

function updateThemeIcon(theme) {
    const icon = themeToggle.querySelector('i');
    icon.className = theme === 'light' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
}

// Language Selector
const langButtons = document.querySelectorAll('[data-lang]');
langButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const lang = button.getAttribute('data-lang');
        // Burada dil değiştirme işlemi yapılacak
        document.querySelector('.language-selector span').textContent = lang.toUpperCase();
    });
});

// Search Box
const searchInput = document.querySelector('.search-box input');
searchInput.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    // Burada arama işlemi yapılacak
});

// Notifications
const notificationItems = document.querySelectorAll('.notification-body .dropdown-item');
notificationItems.forEach(item => {
    item.addEventListener('click', () => {
        item.classList.remove('unread');
        updateNotificationCount();
    });
});

function updateNotificationCount() {
    const unreadCount = document.querySelectorAll('.notification-body .unread').length;
    const badge = document.querySelector('.notification-badge');
    badge.textContent = unreadCount;
    badge.style.display = unreadCount > 0 ? 'block' : 'none';
}

// Sidebar Toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('active');
    mainContent.classList.toggle('active');
    document.body.classList.toggle('sidebar-active');
});

// Submenu Toggle
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (this.querySelector('.arrow')) {
            e.preventDefault();
            this.classList.toggle('active');
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.classList.toggle('active');
            }
        }
    });
}); 