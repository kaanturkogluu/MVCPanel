<?php
//Bu sayfa ile Oynamayın 
require_once __DIR__ . "/../classes/Autoloader.php";




function showError($errorMessage = null)
{
    if (!$GLOBALS['app_config']['debug']) {
        $error = $errorMessage; // değişken tanımlanıyor

        ob_start();
        include __DIR__ . "/../pages/404.php"; // içerik çalıştırılıyor
        $content = ob_get_clean(); // tampondan içerik alınıp temizleniyor

        echo $content;
        exit; // sayfa çalıştıktan sonra işlem sonlandırılır
    }

}



$request = Request::getInstance();
$session = Session::getInstance();
$router = Router::getInstance();

$csrf = CSRF::getInstance();


$method = $_SERVER['REQUEST_METHOD'];
$action = null;

if ($method === 'POST') {
    $action = $_POST['action'] ?? null;
    $token = $_POST['csrf_token'] ?? null;
} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? null;
    $token = $_GET['csrf_token'] ?? null;
} else {
    showError('Sistem de geçerli olan bir method kullanılmadı , Get veya Post kullanın');
}



if (!$csrf->validateToken($token)) {

    showError('Form içersinde csrf_token bilgisi eksik . Sistem isteği sistem dışından geldiğini zannediyor . 
   Form içerisine <mark> <br> echo $csrf->getTokenField()  <br>  </mark> fonksiyonu eksik.   fonksiyonu çağırırken echo ile ekrana bastirmayı unutma');
}

// $action değeri boşsa yönlendirme yapılabilir:
if ($action === null) {

    showError("Form içerisinde name i action olan  ve yapılacak işlemi belirtecek olan input eksik veya hatalı, Formu kontrol et ");

}






?>