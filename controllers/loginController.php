<?php

require_once __DIR__ . "/../config/controllerValidation.php";


// Eğer bir class birden fazla alanda kullanılacaksa nesne bu kısımda oluşturlacak


switch ($action) {

    case 'login':

     
      


        // ------- Brute Force Koruması --------- //

        $maxDenemeHakki = 4;
        //Denerken ban yer isen , $bansuersini 100  yap
        $banSuresi = 1800; // 30 dakika (saniye)
        $denemeSayisi = $session->get('deneme_sayisi', 0);
        $sonDenemeZamani = $session->get('son_deneme_vakti', 0);
        $simdi = time();
        // Daha önce ban yediyse süresine bak
        if ($denemeSayisi >= $maxDenemeHakki) {
            $kalanZaman = $banSuresi - ($simdi - $sonDenemeZamani);

            if ($kalanZaman > 0) {
                $dakika = ceil($kalanZaman / 60);
                $session->setFlash('error', "Çok fazla geçersiz deneme yapıldı, {$dakika} dakika sonra tekrar deneyin.");
                $router->forcedRedirect($router->getPanelUrl() . 'login.php');
                exit;
            } else {
                // Ban süresi doldu, sıfırla
                $session->remove('deneme_sayisi');
                $session->remove('son_deneme_vakti');
                $denemeSayisi = 0;
            }
        }

        // Giriş işlemi
        $login = new MyLogin();

        $giris = $login->login($request->post('username'), $request->post('password'));

        if ($giris) {


            // Log Kaydı
            $logs = new Logs();
            $logs->createLog($giris['username'],'login');

            unset($giris['password']);
            $session->login($giris);
            $session->remove('deneme_sayisi');
            $session->remove('son_deneme_vakti');
            $session->setFlash('success', "Oturum açıldı");
            $router->forcedRedirect($router->getPanelUrl());
            exit;
        } else {
            // Başarısız giriş -> deneme sayısını artır
            $denemeSayisi++;
            $session->set('deneme_sayisi', $denemeSayisi);

            // Eğer ilk başarısız denemeyse, zaman başlat
            if ($denemeSayisi == 1) {
                $session->set('son_deneme_vakti', $simdi);
            }

            $kalanHakk = $maxDenemeHakki - $denemeSayisi;
            ++$kalanHakk ;
            $session->setFlash('error', "Hatalı kullanıcı adı veya şifre. Kalan deneme hakkınız: {$kalanHakk}");

            $router->forcedRedirect($router->getBaseUrl() . 'panel/login.php');

        }

        break;



}





