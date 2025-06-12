<?php

// Veritabanı yapılandırması
$GLOBALS['db_config'] = [
    'default' => [
        'host' => 'localhost',
        'dbname' => 'yenipanel',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'second_db' => [
        'host' => 'localhost',
        'dbname' => 'ikinci_veritabani',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ]
];

// Site yapılandırması
$GLOBALS['site_config'] = [
    'base_url' => 'http://localhost/',
    'timezone' => 'Europe/Istanbul',
    'environment' => 'development'
];

// Uygulama yapılandırması
$GLOBALS['app_config'] = [
    'debug' => true, //Geliştirici Modu 
    'timezone' => 'Europe/Istanbul',
    'session_lifetime' => 120
];



// Hata raporlama
if ($GLOBALS['app_config']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Zaman dilimi ayarı
date_default_timezone_set($GLOBALS['app_config']['timezone']);

?>