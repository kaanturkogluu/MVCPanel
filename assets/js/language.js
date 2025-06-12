// Dil değiştirme fonksiyonu
function changeLanguage(lang) {
    // Dil tercihini localStorage'a kaydet
    localStorage.setItem('selectedLanguage', lang);
    
    // Aktif dil butonunu güncelle
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('onclick').includes(lang)) {
            btn.classList.add('active');
        }
    });
    
    // Sayfayı yeniden yükle
    location.reload();
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Kaydedilmiş dil tercihini al
    const savedLanguage = localStorage.getItem('selectedLanguage') || 'tr';
    
    // Aktif dil butonunu işaretle
    document.querySelectorAll('.lang-btn').forEach(btn => {
        if (btn.getAttribute('onclick').includes(savedLanguage)) {
            btn.classList.add('active');
        }
    });
    
    // Tüm çeviri gerektiren elementleri güncelle
    updatePageLanguage(savedLanguage);
});

// Sayfa içeriğini güncelle
function updatePageLanguage(lang) {
    const translations = lang === 'tr' ? tr : en;
    
    // data-i18n attribute'u olan tüm elementleri bul
    document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if (translations[key]) {
            element.textContent = translations[key];
        }
    });
    
    // Placeholder'ları güncelle
    document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
        const key = element.getAttribute('data-i18n-placeholder');
        if (translations[key]) {
            element.placeholder = translations[key];
        }
    });
    
    // Title'ları güncelle
    document.querySelectorAll('[data-i18n-title]').forEach(element => {
        const key = element.getAttribute('data-i18n-title');
        if (translations[key]) {
            element.title = translations[key];
        }
    });
} 