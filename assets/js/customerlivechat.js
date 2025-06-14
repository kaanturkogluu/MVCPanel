// CustomerLiveChat sınıfı
class CustomerLiveChat {
    constructor(userId, roomName) {
        this.userId = userId;
        this.roomName = roomName;
        this.socket = null;
        this.isConnected = false;
        this.isApproved = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000; // 3 saniye
        this.messageQueue = [];
        this.eventListeners = new Map();

        // DOM elementleri
        this.chatWidget = document.getElementById('chatWidget');
        this.chatMinimized = document.getElementById('chatMinimized');
        this.messagesContainer = document.getElementById('chatMessages');
        this.messageInput = document.getElementById('messageInput');
        this.sendButton = document.getElementById('sendMessage');
        this.connectionStatus = document.getElementById('connectionStatus');
        this.minimizedStatus = document.getElementById('minimizedStatus');

        // WebSocket bağlantısını başlat
        this.connect();
        
        // Event listener'ları ekle
        this.setupEventListeners();
    }

    // WebSocket bağlantısını başlat
    connect() {
        try {
            // WebSocket URL'sini oluştur
            const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const host = window.location.hostname;
            const port = '8080'; // WebSocket sunucu portu
            const wsUrl = `${protocol}//${host}:${port}?user=client&id=${this.userId}&room=${this.roomName}`;

            this.socket = new WebSocket(wsUrl);
            this.setupSocketEvents();
            
            // Bağlantı durumunu güncelle
            this.updateConnectionStatus('connecting');
        } catch (error) {
            console.error('WebSocket bağlantı hatası:', error);
            this.handleConnectionError();
        }
    }

    // WebSocket event'lerini ayarla
    setupSocketEvents() {
        this.socket.onopen = () => {
            console.log('WebSocket bağlantısı açıldı');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.updateConnectionStatus('connected');
            
            // Bekleyen mesajları gönder
            this.flushMessageQueue();
        };

        this.socket.onclose = () => {
            console.log('WebSocket bağlantısı kapandı');
            this.isConnected = false;
            this.isApproved = false;
            this.updateConnectionStatus('disconnected');
            this.handleConnectionError();
        };

        this.socket.onerror = (error) => {
            console.error('WebSocket hatası:', error);
            this.updateConnectionStatus('error');
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleIncomingMessage(data);
        };
    }

    // Bağlantı hatası durumunda yeniden bağlanmayı dene
    handleConnectionError() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`Yeniden bağlanma denemesi ${this.reconnectAttempts}/${this.maxReconnectAttempts}`);
            
            setTimeout(() => {
                this.connect();
            }, this.reconnectDelay * this.reconnectAttempts);
        } else {
            console.error('Maksimum yeniden bağlanma denemesi aşıldı');
            this.triggerEvent('maxReconnectAttemptsReached');
        }
    }

    // Gelen mesajları işle
    handleIncomingMessage(data) {
        switch (data.type) {
            case 'admin_approval':
                this.isApproved = true;
                this.addSystemMessage('Destek temsilcisi bağlandı. Sohbet başladı.');
                this.triggerEvent('chatApproved');
                break;

            case 'message':
                this.addMessage(data.message, 'received', data.timestamp);
                break;

            case 'error':
                this.addSystemMessage(data.message, 'error');
                break;

            default:
                console.log('Bilinmeyen mesaj tipi:', data);
        }
    }

    // Mesaj gönder
    sendMessage(message) {
        if (!this.isConnected) {
            this.messageQueue.push(message);
            this.addSystemMessage('Bağlantı kurulana kadar mesajınız bekletilecek.');
            return;
        }

        if (!this.isApproved) {
            // İlk mesaj ise onay bekleyen mesaj olarak gönder
            const initialMessage = {
                type: 'initial_message',
                content: message,
                timestamp: new Date().toISOString()
            };
            this.socket.send(JSON.stringify(initialMessage));
            this.addSystemMessage('Mesajınız gönderildi. Lütfen destek temsilcisinin bağlanmasını bekleyin.');
        } else {
            // Normal mesaj gönder
            const messageData = {
                type: 'message',
                content: message,
                timestamp: new Date().toISOString()
            };
            this.socket.send(JSON.stringify(messageData));
            this.addMessage(message, 'sent', messageData.timestamp);
        }
    }

    // Bekleyen mesajları gönder
    flushMessageQueue() {
        while (this.messageQueue.length > 0) {
            const message = this.messageQueue.shift();
            this.sendMessage(message);
        }
    }

    // Mesaj ekle
    addMessage(message, type, timestamp) {
        const messageElement = document.createElement('div');
        messageElement.className = `message ${type}`;
        
        const content = document.createElement('div');
        content.className = 'message-content';
        content.textContent = message;
        
        const time = document.createElement('span');
        time.className = 'time';
        time.textContent = this.formatTimestamp(timestamp);
        
        messageElement.appendChild(content);
        messageElement.appendChild(time);
        
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
    }

    // Sistem mesajı ekle
    addSystemMessage(message, type = 'info') {
        const messageElement = document.createElement('div');
        messageElement.className = `message system ${type}`;
        messageElement.textContent = message;
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
    }

    // Bağlantı durumunu güncelle
    updateConnectionStatus(status) {
        const statusClasses = {
            connecting: 'connecting',
            connected: 'connected',
            disconnected: 'disconnected',
            error: 'error'
        };

        const statusTexts = {
            connecting: 'Bağlanıyor...',
            connected: 'Bağlı',
            disconnected: 'Bağlantı kesildi',
            error: 'Bağlantı hatası'
        };

        // Status elementlerini güncelle
        [this.connectionStatus, this.minimizedStatus].forEach(element => {
            if (element) {
                element.className = `status ${statusClasses[status]}`;
                element.title = statusTexts[status];
            }
        });

        // Event tetikle
        this.triggerEvent('connectionStatusChanged', { status });
    }

    // Event listener'ları ayarla
    setupEventListeners() {
        // Mesaj gönderme
        this.sendButton.addEventListener('click', () => {
            const message = this.messageInput.value.trim();
            if (message) {
                this.sendMessage(message);
                this.messageInput.value = '';
            }
        });

        // Enter tuşu ile mesaj gönderme
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendButton.click();
            }
        });

        // Minimize butonu
        document.getElementById('minimizeChat').addEventListener('click', () => {
            this.chatWidget.style.display = 'none';
            this.chatMinimized.style.display = 'flex';
        });

        // Kapat butonu
        document.getElementById('closeChat').addEventListener('click', () => {
            if (confirm('Canlı destek oturumunu kapatmak istediğinize emin misiniz?')) {
                this.disconnect();
                this.chatWidget.style.display = 'none';
                this.chatMinimized.style.display = 'none';
            }
        });

        // Minimize edilmiş chat'e tıklama
        this.chatMinimized.addEventListener('click', () => {
            this.chatMinimized.style.display = 'none';
            this.chatWidget.style.display = 'flex';
        });
    }

    // Event dinleyicisi ekle
    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, new Set());
        }
        this.eventListeners.get(event).add(callback);
    }

    // Event tetikle
    triggerEvent(event, data = {}) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => callback(data));
        }
    }

    // Bağlantıyı kapat
    disconnect() {
        if (this.socket) {
            this.socket.close();
            this.socket = null;
        }
        this.isConnected = false;
        this.isApproved = false;
        this.updateConnectionStatus('disconnected');
    }

    // Yardımcı fonksiyonlar
    formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

// Global scope'a ekle
window.CustomerLiveChat = CustomerLiveChat;