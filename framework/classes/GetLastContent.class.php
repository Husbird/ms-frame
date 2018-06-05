<?php
//ООП +++
class GetLastContent {

    protected $data = false;//массив полученных данных
    public $mysqli = false; //метка соединения
    public $print_content = false; //содержимое html
    protected $input_array = array(
                'article' => array('date_add', 4, 'DESC'),
                'plot' => array('date_add', 4, 'DESC'),
                'user' => array('date_reg', 4, 'DESC'),
                'thesis' => array('date_add', 4, 'DESC'),
                'audio' => array('date_add', 4, 'DESC'),
                'gbook_ms' => array('datetime', 4, 'DESC'),
                'photo' => array('date_add', 4, 'DESC'),
                'author' => array('date_add', 4, 'DESC'),
                'video' => array('date_add', 4, 'DESC'),
            );
    
    //принимаем массив с именем таблицы, количеством выбираемых строк, и сортировкой (ASC или DESC)
    function __construct(){
        //var_dump($array); die;
        //если входящий параметр не является массивом
        if(!is_array($this->input_array)){
            return __METHOD__." массив с начальными параметрами не принят";
        }

        $this->mysqli = Core::app()->DBase; //получаем метку соединения
        
        foreach ($this->input_array as $tableName => $value) {
            //echo $tableName.': <br>';
           // echo 'поле: '.$value[0].' <br>';
            //echo 'кол-во строк: '.$value[1].' <br>';
            //echo 'сортировка: '.$value[2].' <br>';
            //echo 'Заголовок: '.$value[3].' <br>';
            $sql = "SELECT * FROM $tableName ORDER by {$value[0]} {$value[2]} LIMIT 0, {$value[1]}";
            $query = $this->mysqli->query($sql);//true
            //$data[$tableName]['main_title'] = $value[3];
            // В цикле переносим результаты запроса в массив $authorsData[]
    		while ($data[$tableName][] = mysqli_fetch_assoc($query));
            array_pop($data[$tableName]);//удаляем последний (пустой)элемент массива"
        }
        $this->data = $data;
        //echo '<pre>';
            //print_r($data);
        //echo '</pre>';
        //var_dump($data);
    }
    
    public function printLastContent(){
        if(!is_array($this->data)){
            echo 'Новый контент отсутствует или заданы неверные параметры';
            return false;
        }
        
        if (!file_exists("framework/components/widgets/last_content/print.php")){
            die('отсутствует файл отображения!');
            return false;
        }
        
        //$this->data = (object)$this->data;
        
        include_once("framework/components/widgets/last_content/print.php");
        
        //пишем в свойство $this->content всё содержимое html файла контента
        //ob_start();
        //include_once("framework/components/widgets/last_content/print.php");
        //$html = ob_get_clean();
        //$this->print_content = $html;//полный путь к файлу контента
        
        //var_dump($this->data);
        //require_once("framework/components/widgets/last_content/print.php");
        //var_dump($this->print_content); die;
    } 
    
    
   
 
}
?>