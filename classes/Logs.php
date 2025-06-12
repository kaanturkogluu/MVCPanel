<?php
require_once __DIR__ . "/BaseModel.php";
require_once __DIR__ . "/Session.php";
require_once __DIR__ . "/IpHelper.php";
require_once __DIR__ . "/Router.php";


class Logs extends BaseModel
{
    protected $table = 'ac_login_logs';
    protected $primaryKey = 'id';

    protected $userip;


    public function __construct(string $dbKey = 'default')
    {
        parent::__construct($dbKey);
        
        $session = Session::getInstance();
        $router = Router::getInstance();
        $ipHelper = IpHelper::getInstance();
        $clientIp = $ipHelper->getClientIp();
        $securityCheck = $ipHelper->checkIpSecurity($clientIp);

        if (!$securityCheck['is_secure']) {
            $session->setFlash('error', "Güvenli olmayan IP'den giriş denemesi: " . $clientIp . " - " . $securityCheck['message']);
            $router->forcedRedirect($router->getPanelUrl() . 'login.php');
            exit;
        }
        $this->userip = $clientIp;
    }

    public function createLog($user_name, $proccess)
    {

        $this->create([
            'user_name' => $user_name,
            'ip' => $this->userip,
            'proccess' => $proccess,

        ]);
    }
}

?>