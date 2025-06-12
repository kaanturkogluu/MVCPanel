<?php

require_once __DIR__ . "/../config/config.php";

class Router
{
    private static $instance = null;
    private static $base_url = null;

    /**
     * Singleton için private constructor
     */
    private function __construct($config = null)
    {
        if ($config === null) {
            global $siteconfig;
            $config = $GLOBALS['site_config']['base_url'];
        }
        if (self::$base_url == null) {
            self::$base_url = $config;
        }
    }

    /**
     * Singleton instance'ı al
     */
    public static function getInstance($config = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Base URL'i al
     */
    public function getBaseUrl()
    {
        return self::$base_url;
    }

    /**
     * Assets URL'ini al
     * @param string|null $url Asset dosya yolu (opsiyonel)
     * @return string
     */
    public function assets($url = '')
    {
        return self::$base_url . "/panel/assets/" . $url;
    }

    /**
     * Panel URL'ini al
     */
    public function getPanelUrl()
    {
        return self::$base_url . "/panel/";
    }

    /**
     * Zorla yönlendirme yap
     */
    public function forcedRedirect($url)
    {
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
        exit;
    }

    /**
     * Header ile yönlendirme yap
     */
    public function redirect($path)
    {
        header("Location: " . $path);
        exit;
    }

    /**
     * Controller dosya yolunu al
     */
    public function controllers($controllerName)
    {
        return self::getPanelUrl() . 'controllers/' . rtrim($controllerName, ".php") . ".php";
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
?>