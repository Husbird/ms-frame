<?php
class ParserGet {
    
    public $incoming_request;
    public $params = array();//изначально полученные параметры запроса
    //результат работы парсера
    public $translit;//транслит
    public $controller_name;//имя контроллера
    public $action;//действие
    public $action_atribute; //атрибут действия (необходим для формирования GET запросов, например для формирования ссылок навигации страниц)
    public $model_name;//имя модели
    public $table_name;//имя таблицы БД
    
    public $result = array();//общий результат работы парсера
    
    function __construct($incoming_request) {
        //var_dump($incoming_request);die;
        $resultDataArray = array();//создаём массив для данных

        //Если запрос пуст - Вводим параметры "ПО УМОЛЧАНИЮ"
        if ($incoming_request == 'empty') {
            Core::app()->setLog(__METHOD__."[".__LINE__."] пустой запрос, загружаю параметры \"по умолчанию\"...");
            $resultDataArray = array(
                'incoming_request' => 'empty',//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
            );
            $this->result = $resultDataArray;//присваиваем свойству resul результат работы парсера (массив данных)
            return 'массив данных для пустого запроса присвоен в свойство $result (MsParserGet->result)'; 
        }
        
        //ОБРАБОТКА ЗАПРОСА
        Core::app()->setLog(__METHOD__."[".__LINE__."] обрабатываю GET запрос (".$incoming_request.") ...");
        $this->incoming_request = Core::app()->checkStr($incoming_request); //входящий запрос ОБРАБОТАТЬ ФИЛЬТРОМ!!!
        //var_dump( $this->incoming_request);die;

        //получаем массив параметров из входящего запроса и используем его как параметры
        $params = explode('/',$this->incoming_request);//Внимание! Здесь пишем массив параметров в свойство. Это свойство будет часто использоваться!!! ОТФИЛЬТРОВАТЬ!!!
/**
 *         ВНИМАНИЕ - правило: не в зависимости от кол-ва параметров - параметры со следующими ключами:
 *         [0]-транслитерация (например заголовок страницы), как правило нигде не используется в коде (присутствует в запросе исключительно для ЧПУ)
 *         [1]-всегда ИМЯ МОДЕЛИ(и часть имени контроллера типа ИмяController)
 *         [2]-всегда ДЕЙСТВИЕ(action)
 *         [3]-в зависимости от действия(action): может быть номером страницы (если Index) или id строки таблицы БД (если View)
 *          *если необходимо получать больше\меньше параметров - необходимо дописать дополнительный "case" в конструкцию "switch"
 *           при этом настоятельно рекомедуется придерживаться данного правила!!!
 */
        $count = count($params); //var_dump($count); die;
        Core::app()->setLog(__METHOD__."[".__LINE__."] получено ".$count." параметра из запроса...");

        switch($count) {
 /**
 * если принят 1 параметра
 */
        case 1:
            //var_dump($params[0]);die;
            if ($params[0] == 'home') {
                $resultDataArray = array(
                    'incoming_request' => 'empty',//содержимое запроса
                    'translit' => 'Home',//транслит
                    'controller' => 'SiteController',//контроллер по умолчанию
                    'action' => 'View', //действие по умолчанию
                    'action_atribute' => 'v', //атрибут действия по умолчанию
                    'model' => 'Site', //имя модели по умолчанию
                    'table_name' => 'site', //имя таблицы данных по умолчанию
                    'id' => 1, //id по умолчанию
                    'page' => false,//номер страницы по умочанию
                );

            //если отправлено письмо подтверждения регистрации выводим исп контроллер
            // Site, action View (главную страницу) для вывода системного сообщения
            } elseif ($params[0] == 'checkYourMail') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
            );

            //если ошибка регистрации (не совпал хеш)
            } elseif ($params[0] == 'regError') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
            );

            //если регистрация и автоматическая авторизация прошли успешно:
            } elseif ($params[0] == 'congratulations') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
            );

            //если нажали кнопку Вход
            } elseif($params[0] == 'Login') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Login',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Login', //действие по умолчанию
                'action_atribute' => 'l', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 6, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения
            );

            //если нажали регистрация
            } elseif ($params[0] == 'Registration') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Registration',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Registration', //действие по умолчанию
                'action_atribute' => 'r', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 7, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
            );

            //если произвели вход
            } elseif ($params[0] == 'Welcome') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Welcome',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
            );

            //если недостаточно прав
            } elseif ($params[0] == 'AccessDenied') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'AccessDenied',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения AccessDenied
            );

            //нажали "Забыли пароль"
            } elseif ($params[0] == 'PassRestore') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'PassRestore',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'PassRestore', //действие по умолчанию
                'action_atribute' => false, //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 8, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
                'email_not_find' => $_SESSION['email_not_find'],//добавляем в массив возможную ошибку дублирования email
            );
            unset($_SESSION['email_not_find']);

            //если пароль успешно сменён
            } elseif ($params[0] == 'PassRestored') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'PassRestored',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
                'userMailToMsg' => $_SESSION['userEmail'],//
            );
            unset($_SESSION['userEmail']); //чистим сессию с имейлом пользователя  PassRestoreError

            //если пароль восстановить/сменить не удалось
            } elseif ($params[0] == 'PassRestoreError') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'PassRestoreError',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
            );

            //если нажали регистрация
            } elseif ($params[0] == 'settings') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Settings',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Settings', //действие по умолчанию
                'action_atribute' => 'set', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'user', //имя таблицы данных по умолчанию
                'id' => Core::$userData['id'], //
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
            );

            //если нажали контакты
            } elseif ($params[0] == 'contacts') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'contacts',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Сontacts', //действие по умолчанию
                'action_atribute' => '', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 4, //
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
            );

            //если нажали контакты
            } elseif ($params[0] == 'about') {
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'about',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'About', //действие по умолчанию
                'action_atribute' => '', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 5, //
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
            );

            //если нажали кнопку выход restore
            } elseif ($params[0] == 'Exit') {
                new Logout;
                exit();
            //если такой запрос не предусмотрен - отправляем на стр 404
            } else {
                //die("404 dddd");
                include_once(Core::app()->config->error_404); //подключаем файл отображения ошибки 404
            }

        break;
 /**
 * если принято 2 параметра
 */
            case 2:
            //если пришел AJAX:
            if ($params[0] == 'ajax') {
                switch ($params[1]) {
                    //получить форму заказа программы тренировок
                    case "GetFormTrainOrder":
                        require_once("app/views/site/_user_info_1.php");
                        exit();
                    break;
                    
                    case "gBookPictureRefresh":
                        //echo "hello world";
                        $Gbook = new Gbook();
                        $Gbook->getSekretImg();
                        echo $Gbook->sec_img_name;
                        //echo "<img src=\"/assets/images/ms_gbook/cap_pic.gif\"/>";
                        exit();
                    break;

                    case "gBookPictureDelete":
                        //echo "hello world";
                        $Gbook = new Gbook();
                        $result = $Gbook->secPicDelete();
                        if ($result === true) {
                            echo "Gbook: Картинка успешно удалена";
                        } elseif ($result === false) {
                            echo "Gbook: Ошибка удаления картинки";
                        } else {
                            echo $result;
                        }
                        exit();
                        break;
                    
                    case "captchaPictureRefresh":
                        $Captcha = new Captcha();
                        $Captcha->getSekretImg();
                        echo $Captcha->captcha_img_name;
                        exit();
                        break;

                    case "captchaPictureDelete":
                        $Captcha = new Captcha();
                        $result = $Captcha->secPicDelete();
                        if ($result === true) {
                            //echo "Captcha: Картинка успешно удалена";
                        } elseif ($result === false) {
                            echo "Captcha: Ошибка удаления картинки";
                        } else {
                            echo $result;
                        }
                        exit();
                        break;

                    default:
                        Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        <span id='debugErrMsg'>Не получен параметр в запросе AJAX</span>");
                        die("Нет такой страницы");
                }
            }

            //если перешли по ссылке из письма регистрации...
            if ($params[0] == 'activate') {
                $hash = $params[1];
                new Registr($hash);
                exit();
            } elseif ($params[0] == 'СhangePass') {
                $getHash = $params[1]; //полученный из письма хеш
                $cookieHash = $_COOKIE['changePassHash']; //хеш сохранённый перед отправкой письма
                $cookieId = $_COOKIE['changePassId']; //id пользователя у которого меняем пароль
                $changePass = new PassRestore;
                $changePass->chengePass($getHash,$cookieHash,$cookieId);

            } elseif ($params[0] == 'setSearchWord') {
                $searchWordEncoded = $params[1];
                $MsSearchKWord = new SearchKWord();
                $MsSearchKWord->setSearchWord($searchWordEncoded);//декодируем и пишем в сессию слово
                header('location:'.$_SESSION['searchGetBackURI'].'');
                exit();

            } elseif ($params[0] == 'dropSearchWord') {
                $MsSearchKWord = new SearchKWord();
                $MsSearchKWord->dropSearchWord();//чистим сессию
                header('location:'.$_SESSION['searchGetBackURI'].'');
                exit();

            } elseif ($params[0] == 'addToBasket') {
                $MsBasket = new Basket;
                $MsBasket->addToBasket($params[1]);
                header('location:'.$MsBasket->url_back.'');
                exit();

            } elseif ($params[0] == 'dellFromBasket') {
                $MsBasket = new Basket;
                $MsBasket->dellFromBasket($params[1]);
                header('location:'.$MsBasket->url_back.'');
                exit();
            }
        break;
 /**
 * если принято 3 параметра
 */
            case 3:
                //если пришел AJAX:
                if ($params[0] == 'ajax') {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] обрабатываю AJAX GET запрос...");

                    switch ($params[1]) {

                        //получить страницу без доп.параметров (ajax/GetPage/trening)
                        case "GetPage":
                            if ($params[2] ==  "mainPage") {
                                new SiteController(array("translit" => "home", "model" => "site", "table_name" => "site",
                                    "action" => "View", "action_atribute" => "v", "id" => 1, "ajaxRequest" => true));
                                exit();
                            }

                            if ($params[2] ==  "trening") {
                                new SiteController(array(
                                    "translit" => "trening", "model" => "site",
                                    "table_name" => "site", "action" => "View",
                                    "action_atribute" => "v", "id" => 2, "ajaxRequest" => true));
                                exit();
                            }

                            if ($params[2] ==  "diet") {
                                new SiteController(array("translit" => "diet", "model" => "site", "table_name" => "site",
                                    "action" => "View", "action_atribute" => "v", "id" => 3, "ajaxRequest" => true));
                                exit();
                            }

                            if ($params[2] ==  "contacts") {
                                new SiteController(array("translit" => "contacts", "model" => "Site", "table_name" => "site",
                                    "action" => "Сontacts", "action_atribute" => "", "id" => 4, "ajaxRequest" => true));
                                exit();
                            }

                            if ($params[2] ==  "Login") {
                                new SiteController(array("translit" => "login", "model" => "Site", "table_name" => "site",
                                    "action" => "Login", "action_atribute" => "l", "id" => 6, "ajaxRequest" => true));
                                exit();
                            }

                            if ($params[2] ==  "Registration") {
                                new SiteController(array("translit" => "registration", "model" => "Site", "table_name" => "site",
                                    "action" => "Registration", "action_atribute" => "r", "id" => 7, "ajaxRequest" => true));
                                exit();
                            }

                            if ($params[2] ==  "AddExPartForm") {
                                require_once "app/views/exercise/_add_ex_part.php";
                                exit();
                            }
                            
                            if ($params[2] ==  "AddExMainForm") {
                                require_once "app/views/exercise/_add_ex_main.php";
                                exit();
                            }

                            if ($params[2] ==  "AddExercise") {
                                $data['ex_main_data'] = Core::app()->DBProcess->selectDataOnID($_SESSION['ex_main_id'],"ex_main");
                                require_once "app/views/exercise/_add_exercise.php";
                                unset($_SESSION['ex_main_id']);
                                exit();
                            }
                            

                        break;

                        default:
                            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                                <span id='debugErrMsg'>Не получен параметр в запросе AJAX</span>");
                            die("Нет такой страницы");
                    }
                    exit();
                }

            $this->translit = $params[0];
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            if(!$this->controller_name){
                //echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";
            }
            
            $this->action = Core::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            if (!$this->action) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>определить действие не удалось!</span>");
            }
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            if (!$this->action) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>не смог определить атрибут действия!</span>");
            }
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            if (!$this->model_name) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>не смог определить имя модели!</span>");
            }
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД (не всегда совпадает например: при добавлении категории..)
            if(!$this->table_name){
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>не смог определить имя таблицы!</span>");
            }
            
            //добавить
            if ($this->action == 'Add') {
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => false,//не определяется для данного действия
                    'page' => false,//не определяется для данного действия
                );  
            
            } elseif($this->action == 'AddCategory') { //добавление категории (полученный параметр действия сравнивается с соотв. атрибутом из файла конфигурации)
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => false,//не определяется для данного действия
                    'page' => false,//не определяется для данного действия
                );  
            
            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>действие не определить не удалось</span>");
            }
            break;
 /**
 * если принято 4 параметра
 */
            case 4:

            //redirect 301
            if ( $params[0] =='ibp-vybor-ibp-raznovidnosti-ibp') {
                header('location:/vybiraem-istochnik-bespereboynogo-pitaniya-ibp-raznovidnosti-ibp/article/v/559', true, 301);
                exit();
            }

             //redirect 301
            if ( $params[0] =='www2') {
                header('location:/ustanovka-moduley-php-v-ubuntu-1604/article/v/44', true, 301);
                exit();
            }

             //redirect 301
            if ( $params[0] =='nastroyka-veb-servera-apache2-na-ubuntu-1604') {
                header('location:/nastroyka-veb-servera-apache2-v-ubuntu-1604/article/v/39', true, 301);
                exit();
            }
            
            $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = Core::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}

            //если действие Index то параметр с ключом [3] означает номер страницы
            if ($this->action == 'Index') {
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'id' => false, //не нужен для данного действия
                );
            } elseif ($this->action == 'View') { //если действие View то параметр с ключом [3] означает id строки таблицы БД
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => $params[3],
                    'page' => false,//не определяется для данного действия
                );
            } elseif ($this->action == 'Edit') {
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => $params[3],
                    'page' => false,//не определяется для данного действия
                );
                
            } elseif ($this->action == 'All') {
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'id' => false, //не нужен для данного действия
                );
             
            } elseif ($this->action == 'EditCategory') {
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => $params[3],
                    'page' => false,//не определяется для данного действия
            );

            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>сценарий для запрашиваемого действия ".$this->action." отсутствует</span>");
            }
            break;   
 /**
 * если принято 5 параметра
 */
            case 5:

            //redirect 301
            if( $params[0] =='proba223w'){
                header('location:/zhelezo/article/i/1/9', true, 301); // на новый: пример:  hypercuts/production/v/4
                exit();
            }


            //var_dump($params);die;
             $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = Core::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //var_dump($this->action);die;
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}
            
            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'category_id' => $params[4], //id категории (например статьи)
                );
                break;
                
            }/**
 * elseif($this->action == 'Admin'){
 *                 $resultDataArray = array(
 *                     'incoming_request' => $this->incoming_request,
 *                     'translit' => $this->translit,
 *                     'controller' => $this->controller_name,
 *                     'action' => $this->action,
 *                     'action_atribute' => $this->action_atribute,
 *                     'model' => $this->model_name,
 *                     'table_name' => $this->table_name,
 *                     'page' => $params[3],
 *                     'object' => $params[4], //например orders (заказы)
 *                 );
 *                 break;
 *             }
 */
            
 /**
 * если принято 6 параметра
 */
            case 6:
             $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = Core::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}
            
            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'category_id' => $params[4], //id категории (например статьи)
                    'category_key' => $params[5], //всё что угодно, например ключ категории (например: category или brands) с помощью этого в модели можно настроить товары какой именно суперкатегории выводить'
                );
            }
            
            break;
            
            case 7:
             $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = Core::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}
            
            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'category_id' => $params[4], //id категории (например статьи)
                    'category_key' => $params[5], //всё что угодно, например ключ категории (например: category или brands) с помощью этого в модели можно настроить товары какой именно суперкатегории выводить'
                    'sub_category_id' => $params[6],
                );
            }
            
            break;
            
            //если неудовлетворительное кол-во параметров - выводим сообщение об ошибке
            default:
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>Неверное количество параметров в запросе или неизвестное действие! (404!)</span>");
            include_once(Core::app()->config->error_404);
        } //switch END
        
            //после проверки в switch получаем массив данных и пишем его
            // в свойство resul
            $count = count($resultDataArray);
            if ($count > 0) {
                $this->result = $resultDataArray;//присваиваем свойству resul результат работы парсера (массив данных) 
                //var_dump($this->result); die;
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span style='color:green'>получено ".$count." параметров для action:<span> <b>".$this->result['action']."</b>");
            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>Ключевые параметры для исполнения действия:
                 ".$this->result['action']." НЕ получены ! error_404</span>");

                include_once(Core::app()->config->error_404);
                return false;
            }
        //return $count;
        //var_dump($action);
    }
}
?>