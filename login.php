<?php

require_once __DIR__ . "/classes/Autoloader.php";
$session = Session::getInstance();
$router = Router::getInstance();
 
if ($session->isLoggedIn()) {
    // Oturum açılmışsa panele
 
    header('Location: ' . $router->getPanelUrl() . 'index.php');
    exit;
}


$router = Router::getInstance();
$csrf = CSRF::getInstance();
$session = Session::getInstance();
$site_title = "Giriş Ekranı";



?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">

                            <div class="mb-md-5 mt-md-4 pb-5">

                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Please enter your login and password!</p>

                                <?php require_once __DIR__ . "/includes/notification.php"; ?>
                                <form action="<?= $router->controllers('loginController') ?>" method="post"
                                    autocomplete="on">
                                    <input type="hidden" name="action" value="login">
                                    <?= $csrf->getTokenField(); ?>

                                    <div class="form-outline form-white mb-4">
                                        <input type="text" name="username" id="username"
                                            class="form-control form-control-lg" autocomplete="username" required />
                                        <label class="form-label" for="username">Kullanıcı Adı</label>
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                        <input type="password" name="password" id="password"
                                            class="form-control form-control-lg" autocomplete="current-password"
                                            required />
                                        <label class="form-label" for="password">Şifre</label>
                                    </div>

                                    <p class="small mb-5 pb-lg-2">
                                        <a class="text-white-50" href="#!">Şifreni mi unuttun?</a>
                                    </p>

                                    <button class="btn btn-outline-light btn-lg px-5" type="submit">
                                        Giriş Yap
                                    </button>
                                </form>

                                <div class="d-flex justify-content-center text-center mt-4 pt-1">
                                    <a href="#!" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-google fa-lg"></i></a>
                                </div>

                            </div>

                            <div>
                                <p class="mb-0">Don't have an account? <a href="#!" class="text-white-50 fw-bold">Sign
                                        Up</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>