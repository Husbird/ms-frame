<?php
class Article extends Model
{
    protected $translit; //(применяется для формирования GET запросов)
    protected $table_name = false; //имя таблицы
    protected $model_name; //имя модели
    protected $action = 'Index'; //действие по умолчанию
    protected $action_atribute = 'i'; //атрибут действия по умолчанию (применяется для формирования GET запросов)
    protected $page = false; //по умолчанию всегда первая страница
    protected $id = false; //по умолчанию
    protected $category_id = false; //id категории
    protected $system_massage = false; //полученное системное сообщение
    protected $paginationNum = 3; //по умолчанию выводим 3 строки таблицы на странице
    protected $params = false;
    
    //public $actionIndexData = array();//массив данных для контроллера (actionIndex)
    public $actionData = false;//массив данных для сонтроллера (actionView)
    //public $actionViewData2 = 0;   
    
    function __construct($params = array()) {
        $this->translit = $params['translit'];//определяем 'translit' (применяется для формирования GET запросов)
        $this->model_name = $params['model']; //переданное имя модели
        $this->table_name = $params['table_name'];
        $this->table_name2 = 'article_cat'; //ставим вручную для каждого конкретного случая
        $this->action = $params['action'];//определяем action !!!
        $this->action_atribute = $params['action_atribute'];//определяем атрибут action. (применяется для формирования GET запросов)
        $this->page = intval($params['page']);//определяем номер страницы
        $this->id = intval($params['id']);//определяем id
        $this->category_id = intval($params['category_id']);//определяем id категории
        $this->system_massage = $params['system_massage_file']; //передаётся парсером затем извлекается их файла функцией getSysMassage

        //готовим данные модели к запрашиваему действию:
        switch ($this->action) {

            case 'View':
            $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);//получаем массив данных
            //в зависимости от прав включаем кнопки
            if ( Core::app()->accessCheck('Admin,Суперчеловек ;),Moderator') ) {
                $data['delBtn'] = true;
                //вкл вывод кол-ва просмотров:
                $data['show_views'] = true;
            }
            //настройка кнопки редактирования
            $HtmlDetails = new HtmlDetails;
            $HtmlDetails->btn_title = "Редактировать"; //надпись на кнопке;
            $HtmlDetails->btn_href = "/admin/article/e/$this->id"; //ссылка (действие);
            $HtmlDetails->button_get("edit"); //функция кнопки;
            $data['button_edit'] = $HtmlDetails->button_html_code; //код кнопки

            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = htmlspecialchars_decode($data["article_title"], ENT_QUOTES);
            $data['meta_description'] = htmlspecialchars_decode($data["article_description"], ENT_QUOTES);
            $data['meta_keywords'] = htmlspecialchars_decode($data["article_keywords"], ENT_QUOTES);
            $data['article_text'] = nl2br(htmlspecialchars_decode($data["article_text"], ENT_QUOTES));

            // готовим данные для микроразметки:
            $data['datePublished'] = date(DATE_ISO8601, $data["date_add"]);
            $data['dateModified'] = date(DATE_ISO8601, $data["date_edit"]);
            $Hfu = new Hfu;
            // постоянный адрес страницы
            $data['mainEntityOfPageUrl'] = Core::app()->config->site_path.'/'.$Hfu->hfu_gen($data['pageTitle']).'/article/v/'.$data['id'];
            $data['logoImgUrl'] = Core::app()->config->site_path.'/assets/images/mss_logo.png'; // адрес логотипа

            // последний контент:
            $LastContent = new LastContent;
            $LastContent->table_name = "article";
            $data['last_content_article'] = $LastContent->get_data();

            //парсим картинки в текст
            $StringProcess = new StringProcess;
            $dir = "/assets/media/images/article/{$this->id}/";
            $data['article_text'] = $StringProcess->textImgParser($data['article_text'], 
                $dir, $data['meta_keywords']);
            // convert the tags of the form "[]" into tags "<>"
            $data['article_text'] = $StringProcess->textBracketsDecode($data['article_text']);

            // отдаём данные контроллеру:
            $this->actionData = (object)$data;
            
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            //добавляем +1 к количеству просмотров
            Core::app()->DBProcess->addViewToDB($this->id,$this->table_name);
            break;
            
            case 'Index':
            //если нажали по метке (поиск)
            if ($_SESSION['searchWord']) {
                 // search on keyword
                $MsSearchKWord = new SearchKWord($this->table_name, 
                    $this->model_name,$this->action);
                $idMassiv = $MsSearchKWord->searchEngine_mss($_SESSION['searchWord']);// получаем массив id соответствующих искомому слову
                $data = $MsSearchKWord->idToData_pageNav_mss($idMassiv,'search', 
                    $this->table_name,$this->action_atribute, $this->page, 3, 'id', 'ASC');
            } else {
                //массив данных с учётом категории
                $data = $this->AllArticlesData('by category id');//получаем массив данных    
            }

            //в зависимости от прав включаем кнопки
            if ( Core::app()->accessCheck('Admin,Суперчеловек ;),Moderator') ) {
                $data['addNewBtn'] = true;
                $data['delBtn'] = true; //кнопка удаления раздела
                $data['show_views'] = true; //вкл вывод кол-ва просмотров:
            }
            //настройка кнопки редактирования
            $HtmlDetails = new HtmlDetails;
            $HtmlDetails->btn_title = "Редактировать раздел"; // надпись на кнопке;
            $HtmlDetails->btn_href = "/admin/article/e_cat/$this->category_id"; // ссылка (действие);
            $HtmlDetails->button_get("edit"); //функция кнопки;
            $data['button_edit'] = $HtmlDetails->button_html_code; //код кнопки
            //var_dump($this->category_id);die;
            //var_dump($data['data'][0]['title']);
            //$data['pageTitle'] = 'Статьи'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $data['pageTitle'] = htmlspecialchars_decode($data['data'][0]['title'], ENT_QUOTES);
            $data['meta_description'] = nl2br(htmlspecialchars_decode($data['data'][0]["description"], ENT_QUOTES));
            $data['cat_id'] = $this->category_id; //id текущей категории (для кнопки удаления)

            // последний контент:
            $LastContent = new LastContent;
            $LastContent->table_name = "article";
            $data['last_content_article'] = $LastContent->get_data();

            // отдаём данные контроллеру:
            $this->actionData = (object)$data;
            //инициализация ссылок в виджете хлебные крошки
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            $breadcumb->setLink($data['pageTitle'],2);
            break;
            
            case 'Edit':
            $data = Core::app()->DBProcess->selectDataOnID($this->id, $this->table_name);//получаем массив данных
            //готовим ключевые слова
            $keyWords = $this->keepMarkerData(); //массив с ключевыми словами
            $data['allKeyWords'] = $keyWords; //добавляем полученный массив к основному массиву $data
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            $data['edit_info'] = "id: ".Core::$userData['id']." | Имя: ".Core::$userData['name']." | IP:".Core::$userData['ip']; //добавляем данные редактора
            $data['date_edit'] = time(); //добавляем дату редактирования
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            //готовим данные имеющихся категорий статей для выпадающего списка формы
            //массив с категориями(добавляем полученный массив к основному массиву $data)
            $data['categoryData'] = Core::app()->DBProcess->getTableInfo('article_cat'); 
            $data['pageTitle'] = 'Редактирование статьи';

            //смотрим файлы в каталоге статьи:
            $dir = './assets/media/images/'.$this->table_name.'/'.$this->id;
            $pictures = scandir($dir, 1);
            $countFiles = count($pictures);
            //убираем 2 последних элемента (точки) и возвращаем остальные (до начала массива)
            $pictures = array_slice($pictures, 0, ($countFiles-2)); //Array ( [0] => IMG_20170724_164341.jpg [1] => GukG8-6kgYA.jpg )
            $data['pictures'] = $pictures;
            //print_r($pictures); die("sdfada");
            // отдаём данные контроллеру:
            $this->actionData = (object)$data;

            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            case 'All':
            //если нажали по метке (поиск)
            if ($_SESSION['searchWord']) {
                // ------------------------------------------------ поиск по ключевому слову -----------------------------------
                $MsSearchKWord = new SearchKWord($this->table_name,$this->model_name,$this->action);
                $idMassiv = $MsSearchKWord->searchEngine_mss($_SESSION['searchWord']);// получаем массив id соответствующих искомому слову
                $data = $MsSearchKWord->idToData_pageNav_mss($idMassiv,'search',$this->table_name,$this->action_atribute, $this->page,3, 'id', 'ASC');
            } else {
                $data = $this->AllArticlesData();//получаем массив данных 
            }
            //в зависимости от прав включаем кнопки
            if ( Core::app()->accessCheck('Admin,Суперчеловек ;),Moderator') ) {
                $data['addNewBtn'] = true;//включаем кнопку "добавить"
            }
            $data['pageTitle'] = htmlspecialchars_decode($data['data'][0]['title'], ENT_QUOTES);
            $data['meta_description'] = nl2br(htmlspecialchars_decode($data['data'][0]["description"], ENT_QUOTES));
            //$data['pageTitle'] = 'Все статьи'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $this->actionData = (object)$data;
            //инициализация ссылок в виджете хлебные крошки
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            $breadcumb->setLink($data['pageTitle'],2);
            break;
            
            case 'Add':
            //готовим ключевые слова
            $keyWords = $this->keepMarkerData(); //массив с ключевыми словами
            $data['allKeyWords'] = $keyWords; //добавляем полученный массив к основному массиву $data
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            //добавляем данные o редакторe
            $data['admin_info'] = "id: ".Core::$userData['id']." | Имя: ".Core::$userData['name']." | IP:".Core::$userData['ip']; 
            $data['date_add'] = time(); //добавляем дату редактирования

            //готовим данные имеющихся категорий
            //массив с категориями статей (обавляем полученный массив к основному массиву $data)
            $data['article_catData'] = Core::app()->DBProcess->getTableInfo('article_cat');

            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Добавляем новую статью!';
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            case 'AddCategory':
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            //добавляем данные o редакторe
            $data['admin_info'] = "id: ".Core::$userData['id']." | Имя: ".Core::$userData['name']." | IP:".Core::$userData['ip'];
            $data['date_add'] = time(); //добавляем дату редактирования

            //готовим данные из БД:
            $data['article_catData'] = Core::app()->DBProcess->getTableInfo('article_cat'); //массив с категориями статей (обавляем полученный массив к основному массиву $data)
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Добавляем новую категорию статей!';
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;

            case 'EditCategory':
                //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
                //добавляем данные o редакторe
                $data['admin_info'] = "id: ".Core::$userData['id']." | Имя: ".Core::$userData['name']." | IP:".Core::$userData['ip'];
                $data['date_add'] = time(); //добавляем дату редактирования
                //готовим данные из БД:
                $data = Core::app()->DBProcess->selectDataOnID($this->id, "article_cat");//получаем массив данных
                //var_dump($data );die;
                $data['pageTitle'] = "Редактирование раздела ".$data['title'];

                //смотрим файлы в каталоге статьи:
                $dir = './assets/media/images/article_cat/'.$this->id;
                $pictures = scandir($dir, 1);
                $countFiles = count($pictures);
                //убираем 2 последних элемента (точки) и возвращаем остальные (до начала массива)
                $pictures = array_slice($pictures, 0, ($countFiles-2)); //Array ( [0] => IMG_20170724_164341.jpg [1] => GukG8-6kgYA.jpg )
                $data['pictures'] = $pictures;

                $this->actionData = (object)$data;
                //подключаем виджет хлебные крошки:
                $breadcumb = new Breadcrumb();
                //инициализация ссылок в виджете хлебные крошки
                $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            //если действие не определено
            default:
            die("<br>Модель: <b>$this->model_name</b>: Ошибка: Определить действие (action '$this->action') переданное в модель '$this->model_name' НЕ удалось!<br> Работа модели прервана.");
        }
    }
    
    public function AllArticlesData($options = false) {
        if (!$options) {
            $mixedDataArray = Core::app()->TechHelpSite->AllArticlesData($this->translit,$this->table_name,$this->action_atribute,$this->page,5,'id','DESC',false);    
        } elseif ($options == 'by category id') {
            $mixedDataArray = Core::app()->TechHelpSite->AllArticlesData($this->translit,$this->table_name,$this->action_atribute,$this->page,5,'id','DESC',$this->category_id); 
        }
        //var_dump($mixedDataArray);
        return $mixedDataArray;
    }
}
?>