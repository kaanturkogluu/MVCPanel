<?php

require_once __DIR__ . "/../classes/Autoloader.php";


$router = Router::getInstance();
$session = Session::getInstance();
require_once __DIR__ . "/../config/checkSession.php";
require_once __DIR__ . "/../config/config.php";

$devoloperMode = $GLOBALS['app_config']['debug'];
?>
<!DOCTYPE html>
<html lang="tr" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?= $siteTitle ?> Panel</title>
    <link href="<?= $router->assets("css/bootstrap.min.css") ?>" rel="stylesheet">
    <link href="<?= $router->assets('css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= $router->assets('css/style.css') ?>" rel="stylesheet">
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
    </div>

    <?php


    require __DIR__ . "/notification.php";
    ?>