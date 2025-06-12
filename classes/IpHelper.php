<?php

require_once __DIR__ . "/../config/config.php";

class IpHelper
{
    private static $instance = null;

    /**
     * Singleton için private constructor
     */
    private function __construct()
    {
    }

    /**
     * Singleton instance'ı al
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Kullanıcının gerçek IP adresini alır
     * Proxy arkasındaki kullanıcılar için de çalışır
     * @return string
     */
    public function getClientIp(): string
    {
        $ipAddress = '';

        // Proxy üzerinden gelen IP'leri kontrol et
        $proxyHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($proxyHeaders as $header) {
            if (isset($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ipAddress = trim($ips[0]);
                if ($this->isValidIp($ipAddress)) {
                    return $ipAddress;
                }
            }
        }

        // Hiçbir IP bulunamazsa varsayılan olarak localhost
        return '127.0.0.1';
    }

    /**
     * IP adresinin geçerli olup olmadığını kontrol eder
     * @param string $ip
     * @return bool
     */
    public function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false;
    }

    /**
     * IP adresini güvenli bir şekilde temizler
     * @param string $ip
     * @return string
     */
    public function sanitizeIp(string $ip): string
    {
        $ip = trim($ip);
        $ip = strip_tags($ip);
        $ip = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
        return $ip;
    }

    /**
     * IP adresinin proxy olup olmadığını kontrol eder
     * @param string $ip
     * @return bool
     */
    public function isProxy(string $ip): bool
    {
        if (!$this->isValidIp($ip)) {
            return false;
        }

        // Proxy kontrolü için yaygın proxy portlarını kontrol et
        $proxyPorts = [80, 8080, 3128, 8081, 9090, 1080, 8888, 8118, 8123, 9050];

        // IP'nin proxy portlarını kullanıp kullanmadığını kontrol et
        foreach ($proxyPorts as $port) {
            if (strpos($ip, ':' . $port) !== false) {
                return true;
            }
        }

        // Proxy header'larını kontrol et
        $proxyHeaders = [
            'HTTP_VIA',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR_IP',
            'VIA',
            'X_FORWARDED_FOR',
            'FORWARDED_FOR',
            'X_FORWARDED',
            'FORWARDED',
            'CLIENT_IP',
            'FORWARDED_FOR_IP',
            'HTTP_PROXY_CONNECTION'
        ];

        foreach ($proxyHeaders as $header) {
            if (isset($_SERVER[$header])) {
                return true;
            }
        }

        return false;
    }

    /**
     * IP adresinin güvenli olup olmadığını kontrol eder
     * @param string $ip
     * @return bool
     */
    public function isSecureIp(string $ip): bool
    {
        if (!$this->isValidIp($ip)) {
            return false;
        }

        // Proxy kontrolü
        if ($this->isProxy($ip)) {
            return false;
        }

        // Localhost kontrolü
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return false;
        }

        return true;
    }

    /**
     * IP adresinin güvenlik durumunu kontrol eder ve sonucu döndürür
     * @param string $ip
     * @return array ['is_secure' => bool, 'is_proxy' => bool, 'message' => string]
     */
    public function checkIpSecurity(string $ip): array
    {
        $ip = $this->sanitizeIp($ip);

        if (!$this->isValidIp($ip)) {
            return [
                'is_secure' => false,
                'is_proxy' => false,
                'message' => 'Geçersiz IP adresi'
            ];
        }

        if ($this->isProxy($ip)) {
            return [
                'is_secure' => false,
                'is_proxy' => true,
                'message' => 'Proxy tespit edildi. Güvenlik nedeniyle erişim engellendi.'
            ];
        }


        // Geliştirici Modu değilse localhost kontrolü yapılır
        if (!$GLOBALS['app_config']['debug']) {
            if ($ip === '127.0.0.1' || $ip === '::1') {

                return [
                    'is_secure' => false,
                    'is_proxy' => false,
                    'message' => 'Localhost IP adresi tespit edildi'
                ];
            }

        }



        return [
            'is_secure' => true,
            'is_proxy' => false,
            'message' => 'IP adresi güvenli'
        ];
    }

    /**
     * Singleton için clone'lamayı engelle
     */
    private function __clone()
    {
    }

    /**
     * Singleton için unserialize'i engelle
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
