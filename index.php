<?php 

require_once __DIR__ . "/classes/Autoloader.php";
 

$session = Session::getInstance();

require_once __DIR__ . "/config/checkSession.php";

if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}
header('Location: pages/panel.php');
exit;
?>