<?php



require_once __DIR__ . "/../config/controllerValidation.php";
 


// Eğer bir class birden fazla alanda kullanılacaksa nesne bu kısımda oluşturlacak


switch ($action) {

    case 'actiontipi':
        //post için verileri almak 
        // $name = $request->postType('inputnamei', 'veri Tipi grilecek)

        $name = $request->post('username', 'string');
        $email = $request->post('useremail', 'string');

        //diger kodlar 
        break;


}





