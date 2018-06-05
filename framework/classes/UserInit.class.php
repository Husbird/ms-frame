<?php
//ООП +++
class UserInit {

    /**
     * public $model_name;//имя модели
     *     public $action;//вызываемое действие
     *     public $params = array();
     */
    private $mysqli = null;
    const USER_TABLE_NAME = "user";
    private $userData = null;

    function __construct() {
        Core::app()->setLog(__METHOD__."[".__LINE__."] начинаю проверку прав пользователя ... ");
        $this->mysqli = Core::app()->DBase;
        //проверка авторизованного пользователя (если есть куки)
        if ( isset($_COOKIE['hash']) AND isset($_COOKIE['id']) AND isset($_COOKIE['ip']) ) {
            Core::app()->setLog(__METHOD__."[".__LINE__."] пользователь Авторизирован (имеются cookie) ... ");

            $id = Core::app()->clearInt($_COOKIE['id']);
            Core::app()->setLog(__METHOD__."[".__LINE__."] id: ".$id);

            $ip = Core::app()->checkStr($_COOKIE['ip']); //получаем ip из куки
            Core::app()->setLog(__METHOD__."[".__LINE__."] ip (из cookie): ".$ip);

            $ipTrue = Core::app()->getRealIp();
            Core::app()->setLog(__METHOD__."[".__LINE__."] ip (текущий): ".$ipTrue);

            $hesh = Core::app()->checkStr($_COOKIE['hash']); //получаем хеш из куки
            Core::app()->setLog(__METHOD__."[".__LINE__."] hesh (из cookie): ".$hesh);

            //проверяем есть ли пользователь с таким id
            Core::app()->setLog(__METHOD__."[".__LINE__."] проверяю пользователя (ищу в БД): ");
            $this->userData = Core::app()->DBProcess->selectDataOnID($id, self::USER_TABLE_NAME);

            if ( is_array($this->userData) ) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] пользователь идентифицирован ... Оk! ");
            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] пользователь не найден! Принудительный LogOut!!!");
                //тут нужно отправлять письмо с ошибкой админу!!!!
                new Logout();
                exit();
            }

            Core::app()->setLog(__METHOD__."[".__LINE__."] сверяю данные ... ");
            //если хеш из БД совпадает с хешем из куки
            Core::app()->setLog(__METHOD__."[".__LINE__."] сверяю хеш из БД (".$this->userData['hash'].") 
                и cookie (".$_COOKIE['hash'].") ...");
            if ($this->userData['hash'] === $_COOKIE['hash']) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] хеш совпал ... Оk! ");

                //если хеш совпал - сверяем ip текущего устройства с ip из куки
                Core::app()->setLog(__METHOD__."[".__LINE__."] сверяю ip из cookie (".$ip.") 
                    и текущий ... (".$ipTrue.")", "n");
                /*if ($ipTrue === $ip) {*/ if ($ipTrue) {   // проверка по ip отключена !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    Core::app()->setLog(__METHOD__."[".__LINE__."] ip совпал ... Оk! ");
                    //echo "BIBLE!";
                    $root = $this->userData['adm_mss'];//привелегии пользователя 
                    Core::app()->setLog(__METHOD__."[".__LINE__."] права пользователя определены как: ".$root." ...");
                    Core::app()->setLog(__METHOD__."[".__LINE__."] прередаём их в: Core::setRole($)");

                    Core::setRole($root);//присваиваем пользователю права! (в объект приложения)

                    //проверяю метку авторизации в сессии на случай если пользователь закрывал броузер
                    //данную метку использует фронт контроллер (MsController) в своей логике
                    Core::app()->setLog(__METHOD__."[".__LINE__."] проверяю метку авторизации в сессии (\$_SESSION['auth']) ...");
                    if (!$_SESSION['auth']) {
                        Core::app()->setLog(__METHOD__."[".__LINE__."] метка отсутствует ... 
                            (что то не так проверь вкл/выкл сессии)");
                        Core::app()->setLog(__METHOD__."[".__LINE__."] ставлю метку успешной авторизации в сессию: \$_SESSION['auth'] = true ...");
                        $_SESSION['auth'] = true; //ставим метку успешной авторизации в сессию
                    } else {
                        Core::app()->setLog(__METHOD__."[".__LINE__."] метка уже установлена ... Ok!");
                    }
                    Core::app()->setLog(__METHOD__."[".__LINE__."] пользователь успешно прошел проверу ... Ok!");

                    Core::app()->setLog(__METHOD__."[".__LINE__."] пишу его данные в ядро приложения ...!");
                    Core::app()->get_OS();//получаем и пишем в свойство приложения OS пользователя
                    Core::app()->setUserData($this->userData);//пишу данные пользователя!

                } else {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] ip - не совпал (log_out), id пользователя: ".$id);
                    //тут нужно отправлять письмо с ошибкой админу!!!!
                    //new Logout(); !!!!!!!!!!!!!!!!!!!!!!!!!!! если проверка ip - включить!
                }

            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] хеш - не совпал (log_out), id пользователя: ".$id);
                //тут нужно отправлять письмо с ошибкой админу!!!!
                new Logout();
            }

        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] пользователь зашел как Гость! Всё Оk!", "n");
            //чистим метку авторизации в сессии если она есть
            if ($_SESSION['auth']) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] но есть метка авторизации, что то не так! (удаляю метку) ...");
                unset($_SESSION['auth']);
            }
        }
    }
}
?>