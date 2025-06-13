<?php
/**
 * Oturum kontrolü
 */
require_once __DIR__."/../classes/Router.php";
$router = Router::getInstance();

// Oturum açılmış mı kontrol et
if (!$session->isLoggedIn()) {
    // Oturum açılmamışsa login sayfasına yönlendir
    $session->setFlash('error', 'Lütfen önce giriş yapın.');
    header('Location: ' . $router->getPanelUrl() . 'login.php');
    exit;
}

// Oturum süresini kontrol et
if (!$session->checkSessionTimeout()) {
    // Oturum süresi dolmuşsa login sayfasına yönlendir
    $session->setFlash('warning', 'Oturum süreniz doldu. Lütfen tekrar giriş yapın.');
    header('Location: ' . $router->getPanelUrl() . 'login.php');
    exit;
}

// Kullanıcı bilgilerini al
$user = $session->getUser();
$userKeys = ['username', 'user_token', 'id'];

if (!$user) {
    $session->setFlash('error', 'Kullanıcı bilgileri bulunamadı.');
    header('Location: ' . $router->getPanelUrl() . 'login.php');
    exit;
}

// Gerekli kullanıcı bilgilerinin varlığını kontrol et
$missingKeys = [];
foreach ($userKeys as $key) {
    if (!isset($user[$key]) || empty($user[$key])) {
        $missingKeys[] = $key;
    }
}

if (!empty($missingKeys)) {
    $session->setFlash('error', 'Eksik kullanıcı bilgileri: ' . implode(', ', $missingKeys));
    header('Location: ' . $router->getPanelUrl() . 'login.php');
    exit;
}

?>