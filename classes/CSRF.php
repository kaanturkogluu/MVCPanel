<?php

require_once __DIR__."/Session.php";
class CSRF
{
    private static $instance = null;
    private $tokenLength = 32; // Token uzunluğu
    private $tokenName = 'csrf_token';
    private $session;

    /**
     * Singleton için private constructor
     */
    private function __construct()
    {
        $this->session = Session::getInstance();
        $this->initializeToken();
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
     * Token'ı başlat veya yenile
     */
    private function initializeToken()
    {
        if (!$this->session->has($this->tokenName)) {
            $this->regenerateToken();
        }
    }

    /**
     * Yeni token oluştur
     */
    private function regenerateToken()
    {
        $token = $this->generateToken();
        $this->session->set($this->tokenName, $token);
        return $token;
    }

    /**
     * Güvenli token oluştur
     */
    private function generateToken()
    {
        return bin2hex(random_bytes($this->tokenLength));
    }

    /**
     * Mevcut token'ı al
     */
    public function getToken()
    {
        return $this->session->get($this->tokenName);
    }

    /**
     * Token'ı yenile
     */
    public function refreshToken()
    {
        return $this->regenerateToken();
    }

    /**
     * Token doğrula
     */
    public function validateToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $storedToken = $this->getToken();
        if (empty($storedToken)) {
            return false;
        }

        return hash_equals($storedToken, $token);
    }

    /**
     * Form için hidden input oluştur
     */
    public function getTokenField()
    {
        $token = $this->getToken();
        return '<input type="hidden" name="' . $this->tokenName . '" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * POST isteğindeki token'ı doğrula
     */
    public function validateRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[$this->tokenName] ?? null;
            if (!$this->validateToken($token)) {
                throw new Exception('CSRF token doğrulaması başarısız!');
            }
        }
    }

    /**
     * AJAX isteği için token header'ı oluştur
     */
    public function getTokenHeader()
    {
        return 'X-CSRF-Token: ' . $this->getToken();
    }

    /**
     * AJAX isteğindeki token'ı doğrula
     */
    public function validateAjaxRequest()
    {
        $headers = getallheaders();
        $token = $headers['X-CSRF-Token'] ?? null;
        
        if (!$this->validateToken($token)) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token doğrulaması başarısız!']);
            exit;
        }
    }

    /**
     * Token'ı temizle
     */
    public function clearToken()
    {
        $this->session->remove($this->tokenName);
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