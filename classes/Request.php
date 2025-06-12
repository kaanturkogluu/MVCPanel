<?php

require_once __DIR__."/Cleaner.php";
class Request
{
    private static $instance = null;
    private $cleaner;

    private function __construct()
    {
        $this->cleaner = new Cleaner();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * POST verilerini güvenli şekilde alır
     * @param string|null $key Belirli bir alan için
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return Cleaner::cleanAll($_POST);
        }
        
        return isset($_POST[$key]) ? Cleaner::cleanAll($_POST[$key]) : $default;
    }

    /**
     * GET verilerini güvenli şekilde alır
     * @param string|null $key Belirli bir alan için
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return Cleaner::cleanAll($_GET);
        }
        
        return isset($_GET[$key]) ? Cleaner::cleanAll($_GET[$key]) : $default;
    }

    /**
     * FILES verilerini güvenli şekilde alır
     * @param string|null $key Belirli bir alan için
     * @return mixed
     */
    public function files($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }
        
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    /**
     * Belirli bir POST alanını belirli bir tipte alır
     * @param string $key Alan adı
     * @param string $type Veri tipi (string, int, float, email, url, date)
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function postType($key, $type = 'string', $default = null)
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = $_POST[$key];

        switch ($type) {
            case 'int':
                return Cleaner::cleanInt($value);
            case 'float':
                return Cleaner::cleanFloat($value);
            case 'email':
                return Cleaner::cleanEmail($value);
            case 'url':
                return Cleaner::cleanURL($value);
            case 'date':
                return Cleaner::cleanDate($value);
            case 'boolean':
                return Cleaner::cleanBoolean($value);
            case 'string':
            default:
                return Cleaner::cleanString($value);
        }
    }

    /**
     * Belirli bir GET alanını belirli bir tipte alır
     * @param string $key Alan adı
     * @param string $type Veri tipi (string, int, float, email, url, date)
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function getType($key, $type = 'string', $default = null)
    {
        if (!isset($_GET[$key])) {
            return $default;
        }

        $value = $_GET[$key];

        switch ($type) {
            case 'int':
                return Cleaner::cleanInt($value);
            case 'float':
                return Cleaner::cleanFloat($value);
            case 'email':
                return Cleaner::cleanEmail($value);
            case 'url':
                return Cleaner::cleanURL($value);
            case 'date':
                return Cleaner::cleanDate($value);
            case 'boolean':
                return Cleaner::cleanBoolean($value);
            case 'string':
            default:
                return Cleaner::cleanString($value);
        }
    }

    /**
     * POST isteği olup olmadığını kontrol eder
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * GET isteği olup olmadığını kontrol eder
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * AJAX isteği olup olmadığını kontrol eder
     * @return bool
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
} 