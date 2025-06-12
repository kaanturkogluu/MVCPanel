<?php 
require_once __DIR__."/../classes/Autoloader.php";

$login  = new MyLogin();
$login->logout(); 

$router = Router::getInstance();
$router->forcedRedirect($router->getBaseUrl(). "panel/login.php");
?>