<?php
//переработано ++
//Front Contriller
class Controller
{
    public $request_method;//метод запроса +
    public $incoming_request = false;//входящий GET запрос
    public $view_file_path = false; //полный путь к файлу представления
    public $layout = 'app/views/layouts/main'; //путь к шаблону
    public $layout_left_sidebar = 'app/views/layouts/l_side_bar.php'; //путь к шаблону левого сайдбара
    public $layout_right_sidebar = 'app/views/layouts/r_side_bar.php'; //путь к шаблону правого сайдбара
    public $content = '<p><h3 style="color:red">Контент отсутствует!!!</h3>
                                вероятно, что фаил отображения контента ещё не создан...
                    </p>';
    public $leftSideBarContent = '<p><h3 style="color:red">Контент отсутствует!!!</h3>
                                вероятно, что фаил отображения контента левой боковой панели ещё не создан...<br>
                                или добавьте в метод контроллера render второй параметр true... 
                            </p>';
    public $rightSideBarContent = '<p><h3 style="color:red">Контент отсутствует!!!</h3>
                                вероятно, что фаил отображения контента правой боковой панели ещё не создан...<br>
                                или добавьте в метод контроллера render второй параметр true... 
                            </p>';
     
    function __construct() {
        //проверяем включены ли сессии если нет то включаем
        //$this->session_status = $this->is_session_started();
        
        //определяем метод запроса
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        Core::app()->setLog(__METHOD__."[".__LINE__."] метод запроса определён как: ".$this->request_method);
        
        //предаём в ядро метку соединения с БД и другие расширения
        Core::app()->setLog(__METHOD__."[".__LINE__."] 
        инициализация метки соединения с БД: Core::app()->DBase и другие расширения");
        
        Core::app()->__setExtension("DBase", DBConnect::getInstance()->getPDO()); //устанавливаем метку соединения
        Core::app()->__setExtension("DBProcess", new DBProcess()); //уст.базовый класс работы с БД
        
        //Core::app()->__setExtension("DbExercise", new DbExercise()); //уст. класс работы с БД для раздела упражнения
        //Core::app()->__setExtension("DbShop", new DbShop()); //уст. класс работы с БД для магазина
        Core::app()->__setExtension("DbVideo", new DbVideo()); //уст. класс работы с БД для раздела видео
        //Core::app()->__setExtension("ThesisSite", new ThesisSite()); //уст. класс работы с БД для сайта тезисов
        Core::app()->__setExtension("TechHelpSite", new ThesisSite()); //уст. класс работы с БД для сайта TechHelp
        
        //определяем права.....
        Core::app()->setLog(__METHOD__."[".__LINE__."] идентификация пользователя ...");
        new UserInit(); //идентификация пользователя
        
        //если права определены и пользователь НЕ гость - инициализация данных пользователя
        if ($_SESSION['auth'] == true) {
        //добавляем посещение
        Core::app()->DBProcess->addActivityToDB();
        }
        
        //если метод запроса POST:
        if ($this->request_method == 'POST') {
        new ParserPost($_POST);
        Core::app()->setLog(__METHOD__."[".__LINE__."] парсер POST отработал безрезультатно! ...");
        exit('Ошибка в запросе!');
        
        //если метод запроса GET: 
        } elseif ($this->request_method == 'GET') {
        //$this->incoming_request = $_GET['route'];//получаем строку запроса
        //var_dump($this->incoming_request);
        Core::app()->setLog(__METHOD__."[".__LINE__."] получена строка запроса: ".$_GET['route']);
        //var_dump($this->incoming_request);die;
        //если пустой запрос ($_GET['route'] == NULL)
        if ($_GET['route'] == NULL) {
            $_GET['route'] = 'empty';
            Core::app()->setLog(__METHOD__."[".__LINE__."] GET запрос - пуст: ".$_GET['route']);
        }
            //передаём содержимое запроса в парсер
            $ParserGet = new ParserGet($_GET['route']);//передаём содержимое запроса в парсер
            
            $params = $ParserGet->result;//получаем параметры из парсера
            Core::$requestArgs = $params; //пишем их в ядро
        
                //Пробуем подключить фаил соответствующего запросу контроллера
                if ( !file_exists(Core::app()->config->controller_path.$params['controller'].".php") ) {
                    //если фаил контроллера отсутствует - формируем текст об ошибке
                    Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        не смог открыть соответствующий запросу контроллер (".$params['controller'].")
                        Отсутствует фаил контроллера (".$params['controller'].".php) или ошибка в 'GET' запросе:
                        (".$params['incoming_request'].")");

                    include_once(Core::app()->config->error_404); //подключаем файл отображения ошибки 404

                } else {
                    //Передаём управление и необходимые параметры соответствующему контроллеру,
                    //создаём (и проверяем) класс нужного контроллера,передаём массив параметров полученных из парсера GET запроса
                    if ( is_object(new $params['controller']($params)) ) { //попытка создать экземпляр класса соответствующего контроллера
                        Core::app()->setLog(__METHOD__."[".__LINE__."] 
                            Объект контроллера (".$params['controller'].") успешно создан!");
                        //echo 'Объект создан';
                    } else {
                        Core::app()->setLog(__METHOD__."[".__LINE__."] 
                            Создать объект контроллера (".$params['controller'].") не удалось!");
                        include_once(Core::app()->config->error_404); //подключаем файл отображения ошибки 404
                        //die("Ошибка контроллера!");
                    }
                }
        }
    }
    //проверка перед выводом отображения, возвращает полный путь к фаилу отображения
    public function getContent($view, $leftSideBar = false, $rightSideBar = false) {
        //var_dump(Core::app()->config->views_path.strtolower($this->params['model']).'/'.$view.".php");
        //var_dump($this->layout); die(__METHOD__);
        //var_dump($leftSideBar);die;
        //проверяем существует ли файл с контентом
        $contentFilePath = Core::app()->config->views_path.strtolower($this->params['model']).'/'.$view.".php";
        //var_dump($contentFilePath);
         //include('/app/views/layouts/site/view.php');
        if (!file_exists($contentFilePath)) {
            //var_dump($fileFullPath);
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                отсутствует файл отображения!");
            //если фаил отображения отсутствует - возвращаем контент по умолчанию (сообщение об отсутствии контента)
            return $this->content;
            //$this->view_file_path = $this->layout.'.php'; //путь к шаблону
            //var_dump($this->view_file_path);
        } else {
            if ($leftSideBar == true) {
                $this->leftSideBarContent = $this->sideBarRun($this->layout_left_sidebar);
            } else {
                $this->leftSideBarContent = "";
            }
    
            if ($rightSideBar == true) {
                $this->rightSideBarContent = $this->sideBarRun($this->layout_right_sidebar);
            } else {
                $this->rightSideBarContent = "";
            }
            //var_dump($this->layout); die(__METHOD__);
            //пишем в свойство $this->content всё содержимое html файла контента
            ob_start();
            require_once ($contentFilePath);//полный путь к файлу контента
            $html = ob_get_clean();
            //помещаем содержимое html файла отображения в свойство в виде объекта, для дальнейшего ввода в шаблоне
            $this->content = $html;
            //var_dump($this->layout); die(__METHOD__);
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                фаил отображения (".$contentFilePath.") передан в свойство как объект!");
        }
    }

    /**$leftSideBar = true - подключить левую боковую панель
     * $rightSideBar = true - подключить правую боковую панель
     * $view - имя файла представления (без расширения ".php")
     */
    public function render($view, $leftSideBar = false, $rightSideBar = false) {
        //получаем в буфер контенет файла представления и боковых панелей
        $this->getContent($view, $leftSideBar, $rightSideBar);
        //Вывод основного шаблона
        require_once($this->layout.'.php');
	}

    protected function ajaxRender($view, $leftSideBar = false, $rightSideBar = false) {
        $this->getContent($view, $leftSideBar, $rightSideBar);
        //Вывод файла отображения
        echo $this->content;
    }

    public function loadModel() {
        //Пробуем подключить фаил соответствующего запросу контроллера
        //var_dump($this->model_name);
        try {
            $this->model_name = ucfirst($this->model_name);
            
            if (!file_exists(Core::app()->config->models_path.$this->model_name.".php")) {
                //если фаил модели отсутствует - формируем текст об ошибке
                die("<br><b>Error: Контроллер не смог открыть нужную модель во время выполнения 'action$this->action' !</b><br>
                                        Вероятно отсутствует фаил модели ($this->model_name.php) или ошибка в 'GET' запросе (<b>$this->model_name</b>)...
                                        ");
                throw new Exception("<br><b>Error: Контроллер не смог открыть нужную модель во время выполнения 'action$this->action' !</b><br>
                                        Вероятно отсутствует фаил модели ($this->model_name.php) или ошибка в 'GET' запросе (<b>$this->model_name</b>)...
                                        ");
            } else {
                ///создаём экземпляр класса необходимой модели (и проверяем)
                if ( is_object($model = new $this->model_name($this->params)) ) {
                    //возвращаем экземпляр класса модели
                    return $model;
                } else {
                    echo "<br><b>Error: Контроллер не смог открыть нужную модель во время выполнения 'loadModel()' !</b><br>";
                }
            }
        } catch (Exception $e){
            Core::app()->setLog(__METHOD__."[".__LINE__."] Ошибка: (".$e->getMessage().")");
        }
    }

    protected function sideBarRun($filePath) {
        if (!file_exists($filePath)) {
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        отсутствует файл отображения левой панели! (".$filePath.")");

            //возвращаем сообщение об отсутствии файла отображения
            return $this->leftSideBarContent;
        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        буферизация содержимого файла боковой панели (".$filePath.")");
            ob_start();
            include_once($filePath);//полный путь к файлу контента
            $html = ob_get_clean();
            return $html;
        }
    }
    
    //проверка включены ли сессии
    /* public function is_session_started() {

        if (is_array($_SESSION)) {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] сессия уже создана!");
        } else {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] сессия не создана, создаю...");
            $sessionStart = session_start();
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] session_start(): ".$sessionStart);
        }
    }*/
}
?>