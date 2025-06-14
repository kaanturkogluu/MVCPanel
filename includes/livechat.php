<?php
if ($GLOBALS['app_config']['livechat']) {
    ?>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chat Styles -->
    <link rel="stylesheet" href="<?= $router->assets('css/style.css') ?>">

    <?php

    $customerId = $_SESSION['customer_id'];
    // Destek odası adı
    $roomName = 'support_room';
    ?>

    <!-- Chat Widget -->
    <div id="chatWidget" class="chat-widget" style="display: none;">
        <div class="chat-header" id="chatHeader">
            <div class="chat-title">
                <span class="status" id="connectionStatus"></span>
                <span>Canlı Destek</span>
            </div>
            <div class="chat-controls">
                <button id="minimizeChat" class="control-button" title="Küçült">
                    <i class="fas fa-minus"></i>
                </button>
                <button id="closeChat" class="control-button" title="Kapat">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="chat-body">
            <div id="chatMessages" class="chat-messages"></div>
        </div>
        <div class="chat-footer">
            <textarea id="messageInput" placeholder="Mesajınızı yazın..." rows="1"></textarea>
            <button id="sendMessage" class="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Minimize edilmiş chat -->
    <div id="chatMinimized" class="chat-minimized" style="display: none;">
        <div class="minimized-content">
            <span class="status" id="minimizedStatus"></span>
            <span class="minimized-label">Canlı Destek</span>
        </div>
    </div>

    <link rel="stylesheet" href="<?= $router->assets('css/') ?>chat.css">

    <script>
        // Chat script'ini yükle
        function loadChatScript() {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = "<?= $router->assets('js/customerlivechat.js') ?>";
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Chat script yüklenemedi'));
                document.head.appendChild(script);
            });
        }

        // Mobil cihaz kontrolü ekleyelim
        function isMobileDevice() {
            return window.innerWidth <= 768;
        }

        // Chat başlatma fonksiyonunu güncelleyelim
        async function initializeChat() {
            try {
                await loadChatScript();
                console.log('Chat script yüklendi');

                if (typeof window.CustomerLiveChat !== 'function') {
                    throw new Error('CustomerLiveChat sınıfı bulunamadı');
                }

                const chatWidget = document.getElementById('chatWidget');
                if (chatWidget) {
                    // Mobil cihazda farklı davranış
                    if (isMobileDevice()) {
                        // Mobilde başlangıçta tam ekran göster
                        chatWidget.style.display = 'flex';
                        chatWidget.style.position = 'fixed';
                        chatWidget.style.top = '0';
                        chatWidget.style.left = '0';
                        chatWidget.style.right = '0';
                        chatWidget.style.bottom = '0';
                        chatWidget.style.width = '100%';
                        chatWidget.style.height = '100%';
                        chatWidget.style.borderRadius = '0';
                        chatWidget.style.zIndex = '99999';

                        // Body scroll'u engelle
                        document.body.style.overflow = 'hidden';

                        // Sidebar'ı kapat
                        const sidebarToggle = document.getElementById('sidebarToggle');
                        if (sidebarToggle) {
                            // Sidebar açıksa kapat
                            const sidebar = document.querySelector('.sidebar');
                            if (sidebar && sidebar.classList.contains('active')) {
                                sidebarToggle.click(); // Sidebar toggle butonuna tıkla
                            }
                        }
                    } else {
                        chatWidget.style.display = 'flex';
                        document.body.style.overflow = '';
                    }
                }

                // Chat instance'ını oluştur
                const chat = new window.CustomerLiveChat('<?php echo $customerId; ?>', 'support');
                console.log('Chat başlatıldı');

                // Chat header'a tıklama olayını ekle
                const chatHeader = document.getElementById('chatHeader');
                if (chatHeader) {
                    chatHeader.addEventListener('click', (e) => {
                        // Eğer tıklanan element veya parent elementi control-button ise işlem yapma
                        if (e.target.closest('.control-button')) {
                            return;
                        }

                        // Chat widget'ı küçült/büyüt
                        const chatWidget = document.getElementById('chatWidget');
                        const chatMinimized = document.getElementById('chatMinimized');

                        if (chatWidget.style.display === 'none') {
                            chatWidget.style.display = 'flex';
                            chatMinimized.style.display = 'none';
                        } else {
                            chatWidget.style.display = 'none';
                            chatMinimized.style.display = 'flex';
                        }
                    });
                }

                // Event listener'ları ekle
                chat.on('connectionStatusChanged', (data) => {
                    console.log('Bağlantı durumu değişti:', data.status);
                });

                chat.on('chatApproved', () => {
                    console.log('Chat onaylandı');
                    // Mobilde sidebar'ı kapat
                    if (isMobileDevice()) {
                        const sidebarToggle = document.getElementById('sidebarToggle');
                        if (sidebarToggle) {
                            // Sidebar açıksa kapat
                            const sidebar = document.querySelector('.sidebar');
                            if (sidebar && sidebar.classList.contains('active')) {
                                sidebarToggle.click(); // Sidebar toggle butonuna tıkla
                            }
                        }
                    }
                });

                chat.on('maxReconnectAttemptsReached', () => {
                    console.log('Maksimum yeniden bağlanma denemesi aşıldı');
                    alert('Bağlantı kurulamadı. Lütfen daha sonra tekrar deneyin.');
                });

                return chat;
            } catch (error) {
                console.error('Chat başlatma hatası:', error);
                alert('Chat başlatılırken bir hata oluştu: ' + error.message);
                return null;
            }
        }

        // Sidebar'daki destek butonuna tıklama
        document.addEventListener('DOMContentLoaded', () => {
            const supportButton = document.querySelector('.support-button');
            if (supportButton) {
                supportButton.addEventListener('click', async (e) => {
                    e.preventDefault();

                    if (confirm('Canlı destek ile bağlantı kurmak istiyor musunuz?')) {
                        const chat = await initializeChat();
                        if (chat) {
                            console.log('Chat başarıyla başlatıldı');
                        }
                    }
                });
            } else {
                console.error('Destek butonu bulunamadı');
            }
        });

        // Chat'i kapatma fonksiyonu ekleyelim
        function closeChat() {
            const chatWidget = document.getElementById('chatWidget');
            const chatMinimized = document.getElementById('chatMinimized');

            if (chatWidget) {
                chatWidget.style.display = 'none';
                document.body.style.overflow = ''; // Normal scroll'u geri getir
            }

            if (chatMinimized) {
                chatMinimized.style.display = 'flex';
            }
        }

        // Ekran boyutu değiştiğinde kontrol et
        window.addEventListener('resize', () => {
            const chatWidget = document.getElementById('chatWidget');
            if (chatWidget && chatWidget.style.display !== 'none') {
                if (isMobileDevice()) {
                    chatWidget.style.position = 'fixed';
                    chatWidget.style.top = '0';
                    chatWidget.style.left = '0';
                    chatWidget.style.right = '0';
                    chatWidget.style.bottom = '0';
                    chatWidget.style.width = '100%';
                    chatWidget.style.height = '100%';
                    chatWidget.style.borderRadius = '0';
                    chatWidget.style.zIndex = '99999';
                    document.body.style.overflow = 'hidden';
                } else {
                    chatWidget.style.position = 'fixed';
                    chatWidget.style.top = 'auto';
                    chatWidget.style.left = 'auto';
                    chatWidget.style.right = '20px';
                    chatWidget.style.bottom = '20px';
                    chatWidget.style.width = '350px';
                    chatWidget.style.height = '500px';
                    chatWidget.style.borderRadius = '10px';
                    chatWidget.style.zIndex = '9999';
                    document.body.style.overflow = '';
                }
            }
        });
    </script>
<?php } ?>