<?php
/**
 * Flash mesajları göster
 */

function showNotification()
{
    global $session;
    
    // Session'dan flash mesajlarını al
    $flashes = $session->getFlash();
    
    if (!empty($flashes)) {
        foreach ($flashes as $flash) {
            $type = $flash['type'];
            $message = $flash['message'];
            
            // Mesaj tipine göre CSS class'ı belirle
            $alertClass = '';
            $icon = '';
            
            switch ($type) {
                case 'success':
                    $alertClass = 'alert-success';
                    $icon = '<i class="fas fa-check-circle"></i>';
                    break;
                case 'error':
                    $alertClass = 'alert-danger';
                    $icon = '<i class="fas fa-exclamation-circle"></i>';
                    break;
                case 'warning':
                    $alertClass = 'alert-warning';
                    $icon = '<i class="fas fa-exclamation-triangle"></i>';
                    break;
                case 'info':
                    $alertClass = 'alert-info';
                    $icon = '<i class="fas fa-info-circle"></i>';
                    break;
                default:
                    $alertClass = 'alert-info';
                    $icon = '<i class="fas fa-info-circle"></i>';
            }
            
            // HTML çıktısı
            echo <<<HTML
            <div class="alert {$alertClass} alert-dismissible fade show" role="alert">
                {$icon}
                <span class="alert-message">{$message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            HTML;
        }
    }
}

showNotification();
?>

<style>
/* Alert Container */
.alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    max-width: 500px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    z-index: 9999;
    margin-bottom: 10px;
    opacity: 1;
    visibility: visible;
    animation: slideIn .75s ease-out forwards;
}

/* Alert Types */
.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    color: #856404;
}

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

/* Alert Icon */
.alert i {
    font-size: 1.25rem;
    margin-right: 10px;
}

/* Alert Message */
.alert-message {
    flex-grow: 1;
    margin-right: 10px;
    font-size: 14px;
    line-height: 1.4;
}

/* Close Button */
.btn-close {
    padding: 0.5rem;
    margin: -0.5rem -0.5rem -0.5rem auto;
    background-color: transparent;
    border: 0;
    opacity: 0.5;
    transition: opacity 0.15s ease-in-out;
    cursor: pointer;
}

.btn-close:hover {
    opacity: 0.75;
}

/* Animations */
@keyframes slideIn {
    0% {
        transform: translateX(100%);
        opacity: 0;
        visibility: visible;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
    }
}

@keyframes fadeOut {
    0% {
        opacity: 1;
        visibility: visible;
    }
    100% {
        opacity: 0;
        visibility: hidden;
    }
}

/* Auto-hide animation */
/* .alert.fade {
    animation: fadeOut 7.5s ease-out forwards;
} */

/* Responsive Design */
@media (max-width: 576px) {
    .alert {
        left: 20px;
        right: 20px;
        min-width: auto;
    }
}
</style>

<script>
    // Bildirim kapatma işlemi için JavaScript
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach((alert, index) => {
            // Her bildirimi biraz daha aşağıya kaydır (çakışma olmasın)
            alert.style.top = (20 + (index * 80)) + 'px';
            
            // Kapatma butonuna tıklandığında fade ve remove işlemi
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    alert.classList.add('fade');
                    setTimeout(() => {
                        alert.remove();
                    }, 1500); // Fade animasyonu süresi
                });
            }
        });
    });
</script>
 
 