<?php
require_once __DIR__ . "/BaseModel.php";
require_once __DIR__ . "/Session.php";

class MyLogin extends BaseModel
{
    protected $table = 'ac_users';
    protected $primaryKey = 'id';


    public function login($username, $password)
    {
       
        $user = parent::findAll(['username' => $username], null, 1)[0];
    
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

   
    public function logout(){
        $session = Session::getInstance();
        $session->logout();
    }
 

}
?>