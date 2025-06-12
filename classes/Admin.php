<?php
require_once __DIR__ . "/BaseModel.php";

class Admin extends BaseModel
{


    protected $table = "ac_users";


    public function __construct(string $dbKey = 'default')
    {
        parent::__construct($dbKey);
    }
}


 

?>