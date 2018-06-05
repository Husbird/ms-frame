<?php
class Logout{
    
/**
* public $model_name;//имя модели
*     public $action;//вызываемое действие
*     public $params = array();
*/
    
    function __construct(){
        unset($_SESSION['auth']);
        //очистиь даннные пользователя из cookie
        setcookie("id", "",time()-100,"/");
        setcookie("hash", "",time()-100,"/");
        setcookie("ip", "",time()-100,"/");
        header("location:/");
        Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] произведён Logout!", "n");
        exit();
    }
}
?>