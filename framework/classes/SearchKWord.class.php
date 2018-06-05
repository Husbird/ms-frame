<?php
//Поиск по ключевому слову
class SearchKWord
{
    public $sitePath = false; //текущий адрес сайта
    public $keyWordsMassiv = false; //массив с ключевыми словами
    public $tableName = false; //имена таблиц в которых ищем (массив)
    public $fieldName = false; //соответственно поля таблиц в которых ищем (массив)
    public $currentModel = false; //текущяя используемая модель (основная)
    public $currentAction = false; //текущее действие
    //public $getBackURI = false;

    private $pdo = null;
    
    //ВНИМАНИЕ Необходимо чтобы поле с ключевыми словами состояло из названия таблицы, нижнего дефиса и слова keywords (video_keywords)
    //$tableNames - принимаем название таблиц в виде массива типа $tableNames[] = 'video';
	function __construct($tableName = false, $currentModel = false,
                         $currentAction = 'i', $translit = false, $DBNumber = false) {
        $this->pdo = Core::app()->DBase;

       
	   if (!$currentModel) {
	       $this->currentModel = 'текущя модель не определена!';
	   } else {
	       $this->currentModel = $currentModel;//пишем в свойство имя основной используемой модели (исп для формирования URI)
	   }
	   $this->currentAction = $currentAction;//пишем в свойство текущее действие (исп для формирования URI)
	   //$_SESSION['mss_monitor'][] = '<b>Запускаю MsPassRestore !</b><br>';
	   //die("Let's go!");
        if ($tableName) {
            $this->tableName = $tableName;
            $this->fieldName = $tableName.'_keywords'; //формируем и пишем в свойство полученное имя поля с ключевыми словами 
            $this->keepMarkerData_mss(); //получаем ключевые слова и пишем их в свойство - $keyWordsMassiv
        }
        if ($currentModel) {
            $_SESSION['searchGetBackURI'] = "/$translit/$this->currentModel/$currentAction/1"; //ссылка для возврата    
        }
	}
    
    
    //функция для выборки ключевых слов (меток) из указанных таблиц (массив $tableNames) и соответсвующих таблицам полей (массив $fieldNames)
    //возвращает массив со всеми найденными ключевыми словами через запятую
    public function keepMarkerData_mss() {
            //$x = count($this->tableNames);
            
            //выбираем все метки из таблицы
            $tableName = $this->tableName; //имя таблицы
            $fieldName = $this->fieldName; //имя поля
            //в зависимости от прав определяем значение для сравнения с полем access_level
            if ( (Core::$user_role == 'Admin') OR (Core::$user_role == 'Суперчеловек ;)') OR (Core::$user_role == 'SuperUser') ) {
                $sql = "SELECT $fieldName FROM `$tableName`";
            } else {
                $sql = "SELECT $fieldName FROM `$tableName` WHERE access_level = ''";
            }

            try {

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll();

                $_SESSION['db_query_num']++;
                $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

            } catch (PDOException $e) {
                $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
                return false;
            }
            //$access_level = '';
            
            //$sql = "SELECT $fieldName FROM `$tableName` WHERE access_level = '$access_level'";
            //echo  $sql."<br>"; //die;

            /*$query = mysqli_query($this->mysqli,$sql);//true
            if(!$query){
                echo '<b>'.__METHOD__.'</b> Ошибка: вероятно отсутсвует необходимое поле в таблице <b>'.$tableName.'</b>';
                return;
            }
            while ($data[] = mysqli_fetch_assoc($query)); //получаем ассоциативный массив (последний элемент - пустой =( )
            array_pop($data); //удаляем последний элемент массива (пустой)*/
            
            foreach ($data as $key => $value) {
                //echo  "ключ: - ". $key . " значение: - ". $value[$fieldName]."<br>";
                $keyWordsUnsorted[] = $value[$fieldName];
                //$string = $string.",".$value;
                //echo  $value."<br>";
            }
            unset($data); //обнуляем массив для следующей итерации
            //var_dump($keyWordsUnsorted);
            //var_dump($data); die();
        
        $stringKeywordsUnsorted = implode(',', $keyWordsUnsorted);
        //избавляемся от пробелов после запятых, другие (между словами) оставляем)
        $stringKeywordsUnsorted = str_replace(', ', ',', $stringKeywordsUnsorted);
         //привеодим к нижнему регистру
        $stringKeywordsUnsorted = mb_strtolower($stringKeywordsUnsorted, 'UTF-8');
        $keyWordsUnsorted = explode(',', $stringKeywordsUnsorted);
        //Меняем местами ключи с их значениями в массиве (удаляются повторяющиеся значения)
        $keyWordsSorted = array_flip($keyWordsUnsorted);
        $keyWordsMassiv = array_keys($keyWordsSorted);
        //var_dump($keyWordsMassiv); die();
        $this->keyWordsMassiv = $keyWordsMassiv; //пишем в свойство массив с данными (ключевыми словами)
    }
    
    //вывод ключевых слов (меток)
    //$this->keyWordsMassiv - подготовленный массив с ключевыми словами
    public function keyWordsPrint() {
        if( is_array($this->keyWordsMassiv) ) {
            echo "<div style='max-width:90%; margin: 0 auto; text-align:center;'>";
            echo "<span style=''>МЕТКИ: </span>";
            foreach ($this->keyWordsMassiv as $key => $value) {
                $value = trim(mb_strtoupper($value, 'UTF-8'));
                $valueForUrl = $this->ruStringConverter_mss($value,'encode'); //кодируем для URI

                if ($value == $_SESSION['searchWord']) {
                    $backgroundColor = 'background-color:red'; 
                    $href = "/dropSearchWord/$valueForUrl'";
                } else {
                    $backgroundColor = 'background-color:#000'; 
                    $href = "/setSearchWord/$valueForUrl";
                }
                echo "<div style='padding:3px; $backgroundColor; display:inline-block; margin:3px;'>
                    <big><a href='$href' style='color:#fff;
                     text-decoration: none;'>$value</a></big></div>";
            }
            //var_dump($_SESSION['searchWord']);
            echo "</div>";  
        } else {
            echo 'ключевых меток не найдено...';
        }
    }
    
    //кодирование и декодирование кирилической строки (например для передачи GETом при ЧПУ)
    //значение  $action или encode или decode
    protected function ruStringConverter_mss($string,$action) {
        switch ($action) {
        case "encode":
            $string = base64_encode ($string); //Кодирует данные алгоритмом MIME base64 (пример: 0KjQldCd0JrQntCS0JjQpw==)
            //проход 1
            $vowels = array("="); //заменяем знаки которые не принимает .htacces (данный массив можно расщирять через запятую: array("-", "\", "^"))
            $string = str_replace($vowels, "-", $string);
            //проход 2
            $vowels = array("/");
            $string = str_replace($vowels, "mss", $string);
            return $string;
            
        case "decode":
            //проход 1
            $vowels = array("-"); //заменяем знаки обратно (данный массив можно расщирять через запятую: array("-", "\", "^"))
            $string = str_replace($vowels, "=", $string);
            //проход 2
            $vowels = array("mss"); //заменяем знаки обратно (данный массив можно расщирять через запятую: array("-", "\", "^"))
            $string = str_replace($vowels, "/", $string);
            
            $string = base64_decode ($string); //Кодирует данные алгоритмом MIME base64 (пример: 0KjQldCd0JrQntCS0JjQpw==)
            return $string;
        }
    }
    //получаем массив с айдишниками соответствующих маркеру строк таблицы
    public function searchEngine_mss($keyWordMarker) {
    $keyWordMarker = mb_strtolower($keyWordMarker,'UTF-8');
    //получаем массив данных из таблицы по id ИСП.
        //$tableName = $this->tableName;
        //$tableName = 'plot';
        //$fieldName = 'id, plot_keywords';
        $sql = "SELECT id, $this->fieldName FROM `$this->tableName`";

        try {

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }

        /*//echo $sql."<br>";
        $query = mysqli_query($this->mysqli,$sql);//true
        //var_dump($sql);
        while($selectedData[] = mysqli_fetch_assoc($query)); //var_dump($selectedData);
        array_pop($selectedData); //удаляем последний элемент массива (пустой)
        //var_dump($selectedData);echo "<br>";*/

        foreach ($data as $key => $value) {
            //echo  "ключ: - ". $key . " значение: - ". $value[$this->fieldName]."<br>";
            $string = $value[$this->fieldName]; //var_dump($keyWordMarker);
            $string = mb_strtolower($string,'UTF-8'); //к нижнему регистру! var_dump($string);
            $search = strpos($string, $keyWordMarker);//сравниваем
            //если совпало:
            if ($search !== false) {
                //echo "нашел!!!<br>";
                $idMassiv[] = $value['id']; //найденные id строк с совпадениями
            }//else{
                //echo "ищу - ".$keyWordMarker."<br>";
                //echo "смотрю на - ".$string."<br>";
                //echo "результат: <br>";
                //var_dump($search);
            //}    
        }
         //если строки не найдены (перешли в другой раздел) - чистим сессию и обновляем страницу
         $x = count($idMassiv);
         if (!$x) {
            $this->dropSearchWord();
            header('location:'.$_SERVER['REQUEST_URI'].'');
            exit();
         } else {
            //var_dump($idMassiv);die;
            return $idMassiv;   
         }
    }
    
    //выбираем по id из массива - данные из БД --> в массив + навигация для вывода постранично
    //$idMassiv - массив с id
    //$marker - метка для отправки GETом (для .htacces для ЧПУ)
    //$partNameTranslit - фраза транслитом для отправки GETом (для .htacces для ЧПУ)
    //$num - кол-во элементов выводимых на одной странице
    //$page - номер текущей страницы
    //$tableName - номер текущей страницы
    //$select - перечень полей таблицы для запроса select
    //$link - метка соединения с БД
    function idToData_pageNav_mss($idMassiv, $translit, $tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false) {
        //в цикле выбираем данные с помощью массива с idшниками
        foreach ($idMassiv as $key =>$value) {
            //var_dump($value);
            $sql = "SELECT * FROM `$tableName` WHERE id = '$value' ORDER BY id";
            //$x = $db->universalSelect_mss($partNameTranslit,$page,$marker, 2, $select, 'thesis', 'ORDER BY thesis_text', 'ASC', $value, 'id',$idMassivCount);
            //var_dump($page);
            try {

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $data[] = $stmt->fetchAll();

                $_SESSION['db_query_num']++;
                $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

            } catch (PDOException $e) {
                $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
                return false;
            }

            /*$query = mysqli_query($this->mysqli,$sql);//true
    		$data[] = mysqli_fetch_assoc($query);*/
        }

        $positions = count($data); //кол-во всех записей
        //var_dump($data); die('HI !!!');
        
        // Находим общее число страниц 
    	$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
    	$page = intval($page); 
    	// Если значение $page меньше единицы или отрицательно 
    	// переходим на первую страницу 
    	// А если слишком большое, то переходим на последнюю 
    	if (empty($page) or $page < 0) $page = 1; 
    	  if ($page > $total) $page = $total;
          
        // Вычисляем начиная к какого номера 
    	// следует выводить записи (строки) 
    	$start = $page * $num - $num;
        //вырезаем элементы из массива в зависимости от номера страницы и кол-ва выводимых элементов на одной странице
        $data = array_slice($data, $start, $num);
        //var_dump($data); die('HI !!!');
        //навигация страниц
    	//предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;$page1left = false;
        $page1right = false;$page2right = false;$page3right = false;$page4right = false;$page5right = false;$nextpage = false;
        
        //навигация страниц
		// Проверяем нужны ли стрелки назад 
		if ($page != 1) $pervpage = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/1>Начало</a></li> 
									   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'>Назад</a></li> '; 
		// Проверяем нужны ли стрелки вперед 
        //var_dump($page);
		if ($page != $total) $nextpage = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'>Вперёд</a></li> 
										   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.$total.'>Последняя</a></li>'; 
		
		// Находим две ближайшие станицы с обоих краев, если они есть
        if($page - 5 > 0) $page5left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 5).'>'. ($page - 5) .'</a></li>';
        if($page - 4 > 0) $page4left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 4).'>'. ($page - 4) .'</a></li>';
        if($page - 3 > 0) $page3left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 3).'>'. ($page - 3) .'</a></li>';
		if($page - 2 > 0) $page2left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 2).'>'. ($page - 2) .'</a></li>'; 
		if($page - 1 > 0) $page1left = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'>'. ($page - 1) .'</a></li>';
        if($page + 1 <= $total) $page1right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'>'. ($page + 1) .'</a></li>';
		if($page + 2 <= $total) $page2right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 2).'>'. ($page + 2) .'</a></li>';
        if($page + 3 <= $total) $page3right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 3).'>'. ($page + 3) .'</a></li>';
        if($page + 4 <= $total) $page4right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 4).'>'. ($page + 4) .'</a></li>';
        if($page + 5 <= $total) $page5right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 5).'>'. ($page + 5) .'</a></li>';
        $page = '<li class="active"><span>'.$page.'</span></li>';
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right, 
            $page4right,$page5right,$nextpage);

        //приводим массив к стандартному виду
        $arr = [];
        foreach ($data as $key=>$value) {
            $arr[] = $value[0];
        }
        $data = $arr;

        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray; //возвращаем массив 
    }
    
    //запись слова в сессию
    public function setSearchWord($encodedWord) {
        $searchWord = $this->ruStringConverter_mss($encodedWord, 'decode');
        $_SESSION['searchWord'] = $searchWord; //пишем декодированное слово в сессию
        
    }
    
    //чистим сессию от поискового слова
    public function dropSearchWord() {
        //$searchWord = $this->ruStringConverter_mss($encodedWord, 'decode');
        unset($_SESSION['searchWord']); //чистим сессию
        
    }
}
?>