<?php
class Site extends Model
{
    protected $translit; //(применяется для формирования GET запросов)
    protected $table_name = false; //имя таблицы
    protected $model_name; //имя модели
    protected $action = 'Index'; //действие по умолчанию
    protected $action_atribute = 'i'; //атрибут действия по умолчанию (применяется для формирования GET запросов)
    protected $page = false; //по умолчанию всегда первая страница
    protected $id = false; //по умолчанию
    protected $system_massage = false; //полученное системное сообщение
    protected $paginationNum = 3; //по умолчанию выводим 3 строки таблицы на странице
    protected $params = false;
    protected $ajaxRequest = false; //AJAX запрос true/false

    
    public $actionData = false;//массив данных используется контроллером (actionView)
    
    function __construct($params = array()) {
        
        $this->translit = $params['translit'];//определяем 'translit' (применяется для формирования GET запросов)
        $this->model_name = $params['model']; //переданное имя модели
        $this->table_name = $params['table_name'];
        $this->action = $params['action'];//определяем action !!!
        $this->action_atribute = $params['action_atribute'];//определяем атрибут action. (применяется для формирования GET запросов)
        $this->page = $params['page'];//определяем номер страницы
        $this->id = $params['id'];//определяем id
        $this->ajaxRequest = $params['ajaxRequest'];//определяем AJAX запрос
        $this->system_massage = $params['system_massage_file']; //передаётся парсером затем извлекается их файла функцией getSysMassage

        Core::app()->setLog(__METHOD__."[".__LINE__."] включаю действие (action ".$this->action.") ...");
        
        //готовим данные модели к запрашиваему действию:
        switch ($this->action) {

            case 'View':
            //получаем данные из БД:
            $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);
            //определяем title для отображения в метатеге шаблона
            $data['pageTitle'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);
            $data['h1'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);
            $data['meta_description'] = htmlspecialchars_decode($data['meta_description'], ENT_QUOTES);
            
            //настройка кнопки редактирования
            $HtmlDetails = new HtmlDetails;
            $HtmlDetails->btn_title = "Редактировать содержание";
            $HtmlDetails->btn_href = "/admin/site/e/{$this->id}";
            $HtmlDetails->button_get("edit");
            $data['button_edit'] = $HtmlDetails->button_html_code;

            // последний контент:
            $LastContent = new LastContent;
            $LastContent->table_name = "article";
            $data['last_content_article'] = $LastContent->get_data();

            //парсим картинки в текст
            $StringProcess = new StringProcess;
            $dir = "/assets/media/images/site/{$this->id}/";
            $data['text'] = $StringProcess->textImgParser(nl2br($data['text']), 
                $dir, $data['meta_keywords']);

            // convert the tags of the form "[]" into tags "<>"
            $data['text'] = $StringProcess->textBracketsDecode($data['text']);

            // отдаём данные контроллеру:
            $this->actionData = (object)$data;

            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'Index':
            $data = Core::app()->DBProcess->аllSelect($this->translit,
                $this->table_name,$this->action_atribute,$this->page, 5,'id','DESC');//получаем массив данных для сонтроллера (actionView)
            $data['pageTitle'] = $data['title']; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;

            case 'Edit':
            $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);//получаем массив данных
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            $data['edit_info'] = "id: ".Core::$userData['id']." | Имя: ".Core::$userData['name']." | IP:".Core::$userData['ip']; //добавляем данные редактора
            $data['date_edit'] = time(); //добавляем дату редактирования
            $data['pageTitle'] = 'Редактирование страницы';

            //смотрим файлы в каталоге статьи:
            $dir = './assets/media/images/'.$this->table_name.'/'.$this->id;
            $pictures = scandir($dir, 1);
            $countFiles = count($pictures);
            //убираем 2 последних элемента (точки) и возвращаем остальные (до начала массива)
            $pictures = array_slice($pictures, 0, ($countFiles-2)); //Array ( [0] => IMG_20170724_164341.jpg [1] => GukG8-6kgYA.jpg )
            $data['pictures'] = $pictures;
            // отдаём данные контроллеру:
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            case 'Login':
                $data = Core::app()->DBProcess->selectDataOnID($this->id,$this->table_name);
                $data['h1'] = "Авторизация";
                //если возникла ошибка - передаём её из сессии в системное сообщение
                if($_SESSION['authErr']){
                    //добавляем к массиву системное сообщение
                    $data['system_massage'] = $_SESSION['authErr'];
                    unset($_SESSION['authErr']);
                }
                // последний контент:
                $LastContent = new LastContent;
                $LastContent->table_name = "article";
                $data['last_content_article'] = $LastContent->get_data();

                $this->actionData = (object)$data;
                //подключаем виджет хлебные крошки:
                $breadcumb = new Breadcrumb();
                //инициализация ссылок в виджете хлебные крошки
                $breadcumb->setLink($data['title'],3);
            break;
            
            case 'Registration':
            $data = Core::app()->DBProcess->selectDataOnID($this->id,$this->table_name);
            $data['h1'] = "Регистрация";
            if($_SESSION['registration_error']){
                //добавляем к массиву системное сообщение
                $data['system_massage'] = $_SESSION['registration_error'];
                unset($_SESSION['registration_error']);
            }
            // последний контент:
            $LastContent = new LastContent;
            $LastContent->table_name = "article";
            $data['last_content_article'] = $LastContent->get_data();
                
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'PassRestore':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);//получаем массив данных для kонтроллера
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'Settings':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);//получаем массив данных для kонтроллера
            
            $data['pageTitle'] = 'Настройка профиля';
            $data['active'.$this->model_name] = 'active'; //для активации пункта меню навигации
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки pageTitle
            $breadcumb->setLink("{$data['pageTitle']}",2);
            break;
            
            case 'Сontacts':
            //получаем массив данных для kонтроллера
            $data = $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);
            //настройка кнопки редактирования
            $HtmlDetails = new HtmlDetails;
            $HtmlDetails->btn_title = "Редактировать содержание";
            $HtmlDetails->btn_href = "/admin/site/e/{$this->id}";
            $HtmlDetails->button_get("edit");
            $data['button_edit'] = $HtmlDetails->button_html_code;

            //определяем title для отображения в метатеге шаблона
            $data['pageTitle'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);
            $data['h1'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);
            $data['meta_description'] = htmlspecialchars_decode($data['meta_description'], ENT_QUOTES);
            //$data['activeContacts'] = 'active'; //для активации пункта меню навигации

            // последний контент:
            $LastContent = new LastContent;
            $LastContent->table_name = "article";
            $data['last_content_article'] = $LastContent->get_data();

            //парсим картинки в текст
            $StringProcess = new StringProcess;
            $dir = "/assets/media/images/site/{$this->id}/";
            $data['text'] = $StringProcess->textImgParser(nl2br($data['text']), 
                $dir, $data['meta_keywords']);

            // convert the tags of the form "[]" into tags "<>"
            $data['text'] = $StringProcess->textBracketsDecode($data['text']);

            // отдаём данные контроллеру:
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки pageTitle
            $breadcumb->setLink("{$data['pageTitle']}",2);
            break;

            case 'About':
            //получаем массив данных для kонтроллера
            $data = $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);
            //определяем title для отображения в метатеге шаблона
            $data['pageTitle'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);
            $data['h1'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);
            $data['meta_description'] = htmlspecialchars_decode($data['meta_description'], ENT_QUOTES);

            // последний контент:
            $LastContent = new LastContent;
            $LastContent->table_name = "article";
            $data['last_content_article'] = $LastContent->get_data();

            //настройка кнопки редактирования
            $HtmlDetails = new HtmlDetails;
            $HtmlDetails->btn_title = "Редактировать содержание";
            $HtmlDetails->btn_href = "/admin/site/e/{$this->id}";
            $HtmlDetails->button_get("edit");
            $data['button_edit'] = $HtmlDetails->button_html_code;

            //$data['activeContacts'] = 'active'; //для активации пункта меню навигации
            //парсим картинки в текст
            $StringProcess = new StringProcess;
            $dir = "/assets/media/images/site/{$this->id}/";
            $data['text'] = $StringProcess->textImgParser(nl2br($data['text']), 
                $dir, $data['meta_keywords']);

            // convert the tags of the form "[]" into tags "<>"
            $data['text'] = $StringProcess->textBracketsDecode($data['text']);
            
            // отдаём данные контроллеру:
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки pageTitle
            $breadcumb->setLink("{$data['pageTitle']}",2);
            break;
            
            //если действие не определено
            default:
                Core::app()->setLog(__METHOD__."[".__LINE__."] Определить действие (action ".$this->action.") 
                переданное в модель ".$this->model_name." НЕ удалось! Работа модели прервана ...", "e");
            die("Ошибка обработки данных, сообщите Вашему системному администратору.");
        }
    }
}
?>