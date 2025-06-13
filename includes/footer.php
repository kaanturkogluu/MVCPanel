<!-- Chat Widget -->
<div class="chat-widget" id="chatWidget">
    <div class="chat-header" id="chatHeader">
        <h5>
            <span class="status"></span>
            <span data-i18n="liveSupport">Canlı Destek</span>
        </h5>
        <div class="actions">
            <button type="button" id="minimizeChat" title="Küçült">
                <i class="bi bi-dash-lg"></i>
            </button>
            <button type="button" id="closeChat" title="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div class="chat-body">
        <div class="chat-messages" id="chatMessages">
            <div class="message received">
                <div class="message-content">
                    Merhaba! Size nasıl yardımcı olabilirim?
                </div>
                <span class="time">Şimdi</span>
            </div>
        </div>
    </div>
    <div class="chat-footer">
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Mesajınızı yazın..." data-i18n-placeholder="typeMessage">
            <button type="button" id="sendMessage" title="Gönder">
                <i class="bi bi-send"></i>
            </button>
        </div>
    </div>
</div>

<!-- Minimized Chat -->
<div class="chat-minimized" id="chatMinimized">
    <span class="status"></span>
    <span data-i18n="liveSupport">Canlı Destek</span>
</div>
  <!-- Footer -->
  <footer class="footer">
        <div class="footer-content">
            <div class="footer-left">
                &copy; 2025 Acboztech. Tüm hakları saklıdır.
            </div>
            <!-- <div class="footer-right">
                <a href="#" class="social-link">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="bi bi-linkedin"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="bi bi-github"></i>
                </a>
            </div> -->
        </div>
    </footer>
</div>
<script src="<?= $router->assets('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= $router->assets('js/main.js') ?>"></script>
<script src="<?= $router->assets('js/languages/tr.js') ?>"></script>
<script src="<?= $router->assets('js/languages/en.js') ?>"></script>
<script src="<?= $router->assets('js/language.js') ?>"></script>



<script src="<?= $router->assets('js/chat.js') ?>"></script>

</body>

</html>