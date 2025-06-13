document.addEventListener('DOMContentLoaded', function() {
    const chatWidget = document.getElementById('chatWidget');
    const chatMinimized = document.getElementById('chatMinimized');
    const chatHeader = document.getElementById('chatHeader');
    const minimizeBtn = document.getElementById('minimizeChat');
    const closeBtn = document.getElementById('closeChat');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendMessage');
    const chatMessages = document.getElementById('chatMessages');
    const supportButton = document.querySelector('.support-button');

    // Mobil cihaz kontrolü
    const isMobile = window.innerWidth <= 768;

    // Chat durumunu localStorage'da sakla
    let chatState = localStorage.getItem('chatState') || 'closed';
    
    // Sayfa yüklendiğinde chat durumunu kontrol et
    if (chatState === 'open') {
        openChat();
    } else if (chatState === 'minimized') {
        chatMinimized.classList.add('active');
    }

    // Chat'i açma fonksiyonu
    function openChat() {
        chatWidget.classList.add('active');
        chatMinimized.classList.remove('active');
        chatState = 'open';
        localStorage.setItem('chatState', chatState);
        messageInput.focus();

        // Mobil cihazlarda body scroll'u engelle
        if (isMobile) {
            document.body.classList.add('chat-open');
            // Scroll pozisyonunu kaydet
            document.body.style.top = `-${window.scrollY}px`;
        }
    }

    // Chat'i kapatma fonksiyonu
    function closeChat() {
        chatWidget.classList.remove('active');
        chatMinimized.classList.remove('active');
        chatState = 'closed';
        localStorage.setItem('chatState', chatState);

        // Mobil cihazlarda body scroll'u geri aç
        if (isMobile) {
            document.body.classList.remove('chat-open');
            // Kaydedilen scroll pozisyonuna geri dön
            const scrollY = document.body.style.top;
            document.body.style.top = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
    }

    // Canlı destek butonuna tıklandığında
    supportButton.addEventListener('click', function(e) {
        e.preventDefault();
        openChat();
    });

    // Chat başlığına tıklandığında minimize et
    chatHeader.addEventListener('click', function(e) {
        if (e.target === chatHeader || e.target.parentElement === chatHeader) {
            minimizeChat();
        }
    });

    // Minimize butonuna tıklandığında
    minimizeBtn.addEventListener('click', function() {
        minimizeChat();
    });

    // Kapat butonuna tıklandığında
    closeBtn.addEventListener('click', closeChat);

    // Minimize edilmiş chat'e tıklandığında
    chatMinimized.addEventListener('click', openChat);

    // Chat'i minimize etme fonksiyonu
    function minimizeChat() {
        chatWidget.classList.remove('active');
        chatMinimized.classList.add('active');
        chatState = 'minimized';
        localStorage.setItem('chatState', chatState);

        // Mobil cihazlarda body scroll'u geri aç
        if (isMobile) {
            document.body.classList.remove('chat-open');
            const scrollY = document.body.style.top;
            document.body.style.top = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
    }

    // Ekran boyutu değiştiğinde mobil kontrolünü güncelle
    window.addEventListener('resize', function() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth <= 768;

        // Mobil durumu değiştiyse ve chat açıksa
        if (wasMobile !== isMobile && chatState === 'open') {
            if (isMobile) {
                document.body.classList.add('chat-open');
                document.body.style.top = `-${window.scrollY}px`;
            } else {
                document.body.classList.remove('chat-open');
                const scrollY = document.body.style.top;
                document.body.style.top = '';
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        }
    });

    // Mesaj gönderme fonksiyonu
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            // Mesajı chat alanına ekle
            addMessage(message, 'sent');
            
            // Mesajı temizle
            messageInput.value = '';
            
            // Otomatik cevap simülasyonu (gerçek uygulamada API'ye gönderilecek)
            setTimeout(() => {
                addMessage('Mesajınız için teşekkürler. En kısa sürede size dönüş yapacağız.', 'received');
            }, 1000);
        }
    }

    // Mesaj ekleme fonksiyonu
    function addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = text;
        
        const timeSpan = document.createElement('span');
        timeSpan.className = 'time';
        timeSpan.textContent = new Date().toLocaleTimeString('tr-TR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageDiv.appendChild(contentDiv);
        messageDiv.appendChild(timeSpan);
        chatMessages.appendChild(messageDiv);
        
        // Otomatik scroll
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Enter tuşu ile mesaj gönderme
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Gönder butonuna tıklandığında
    sendBtn.addEventListener('click', sendMessage);

    // Dil değişikliğinde chat widget'ını güncelle
    document.addEventListener('languageChanged', function() {
        // Dil değişikliğinde gerekli güncellemeleri yap
        const elements = document.querySelectorAll('[data-i18n]');
        elements.forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (window.currentLanguage && window.currentLanguage[key]) {
                element.textContent = window.currentLanguage[key];
            }
        });
    });
}); 