<?php
class MsDBProcess /**extends MsDBConnect*/{
    
    public $tableName; //имя таблицы
    public $pages;//массив с сылками страниц для постраничной навигации
    
    public $selectedDataOnID; //массив данных полученный методом selectDataOnID()
    //из универсальной функции:
    public $universalData;
    public $universalCall;
    
    //метка соединения с БД
    public $mysqli = null;

    function __construct($DBNumber = false){
        //parent:: __construct();
        //ПОЛУЧАЕМ МЕТКУ СОЕДИНЕНИЯ С БД
        //если номер базы данных не указан - получаем метку первой (основной)
        if(!$DBNumber){
            $this->mysqli = DBConnect::getInstance()->getMysqli(); //получаем метку соединения
        }else{
            //если номер базы данных указан - генерируем имя метода, вызываем его и получаем метку соответствующей БД
            $dinamicMethodName = 'getMysqli_'.$DBNumber;
            $this->mysqli = MsDBConnect::getInstance()->$dinamicMethodName(); //получаем метку соединения
        }
        
    }
    //получаем в массиве название всех полей указанной таблицы (ИСПОЛЬЗУЕТСЯ в универсальных методах insert,update)
    public function tableColnames($tableName){
        if(!$tableName){
            echo 'Ошибка: не указано имя таблицы! (in tableColnames()';
        }
        $query = mysqli_query($this->mysqli,"SHOW COLUMNS FROM $tableName");// or die("".__METHOD__.": ошибка в запросе!");
        echo mysqli_error ($this->mysqli);
        $count = mysqli_num_rows($query);
        $x = 0; 
        while ($x < $count) 
        { 
            $colname = mysqli_fetch_row($query); 
             //var_dump($colname);
            $massiv[] = $colname[0];
            $x++; 
        } 
        return $massiv;
    }

    //получаем в массиве название всех полей указанной таблицы (ИСПОЛЬЗУЕТСЯ в универсальных методах insert,update)
    public function tableCountTitles($tableName){
        if(!$tableName){
            echo 'Ошибка: не указано имя таблицы! (in tableCountTitles())';
        }
        $query = mysqli_query($this->link,"SELECT COUNT(*) FROM $tableName");
        $row = mysqli_fetch_row($query);
        $total = $row[0]; // всего записей
        //var_dump($row);die;
        return $total;
    }

	//возвращает массив данных из таблиы 
	public function getTableInfo($tableName){
	   if(!$tableName){
            echo 'Ошибка: не указано имя таблицы! (in getTableInfo())';
        }
		$query = "SELECT * FROM $tableName" or die("Error in the getTableInfo.." . mysqli_error($this->mysqli));
		//execute the query.
		$result = $this->mysqli->query($query);
		  //echo 'SELECT вернул '.$result->num_rows." строк";
		
		//получаем массив
		while($row = mysqli_fetch_assoc($result)){
			//данные каждой строки таблицы попадают в отдельный массив
			$massiv[] = $row;
		}
		return $massiv;
	}#getTableInfo
	
	//вывод на экран содержимого таблицы
	public function listTableInfo($tableName){
		try{
			$massive = self::getTableInfo($tableName);
			if(!$tableName){
                echo 'Ошибка: не указано имя таблицы! (in listTableInfo())';
            }
			if(!is_array($massive)){
					throw new Exception("<p style='color:red; font-weight:bold;'>Ошибка: Не ввели имя таблицы!</p>");
			}
			echo "<h1>Таблица \"$tableName\"</h1>";
			$i = 1;
			foreach ($massive as $key_ => $value_) {
				echo "<h3>$i Строка</h3>";
				$i++;
				foreach($value_ as $key => $value){
					echo $key." = ".$value."<br>";
				}
				echo "<hr>";
			}
		}catch(Exception $e){
			//$e->getMessage();
			echo $e;
		}
	}

//получаем массив данных из ЛЮБОЙ таблицы по id ИСП.
    public function selectDataOnID($id,$tableName){
        //var_dump(debug_backtrace());
        $id = $this->clearInt($id);
        $GLOBALS['mss_monitor'][] = 'selectDataOnID: извлекаю данные по id...'.$id.' из таблицы '.$tableName;
        if((!$tableName) or (!$id)){
            $GLOBALS['mss_monitor'][] = '<span style="color:red;">ошибка</span>: не указано имя таблицы или id!';
        }
        //считаем кол-во всех авторов
        $sql = "SELECT * FROM `$tableName` WHERE id = '$id'"; //echo '<br>';
        //var_dump($this->mysqli);die;
        $query = $this->mysqli->query($sql); // ООП запрос
        //echo mysqli_error ($this->mysqli);
        //if(!$query){var_dump(debug_backtrace());}
        
        //$query = mysqli_query($this->link,$sql);//true процедурный подход
        $selectedData[] = mysqli_fetch_assoc($query);
        //echo $positions;exit();
        //var_dump($sql); die;
        //результат работы метода - инициализация нижеуказанных свойств
        //$this->selectedDataOnID = $selectedData; //массив
        return $selectedData[0];
    }
    
    //добавляем просмотр ИСП. ООП++
    public function addViewToDB($id,$tableName){
        if((!$tableName) or (!$id)){
            echo '<span style="color:red;">ошибка</span>: не указано имя таблицы или id! (in addViewToDB())';
        }
        //получаем имеющиееся кол-во просмотров
        $sql = "SELECT views FROM `$tableName` WHERE id = '$id'";
        $query = $this->mysqli->query($sql) or die("ERROR: ".mysqli_error($this->mysqli));//true
        $selectedData[] = mysqli_fetch_assoc($query);
        $newView = (int)++$selectedData[0]['views']; //увеличиваем на 1
        //пишем в бд
        $sql = "UPDATE `$tableName`
        SET views='$newView' WHERE id = '$id'" ;
        $query = $this->mysqli->query($sql) or die("ERROR: ".mysqli_error($this->mysqli));//true
        //echo $positions;exit();
        //var_dump($query); die;
        //результат работы метода - увеличение кол-ва просмотров на 1
    }
    
    //добавляем посещение ИСП. ООП++ и дату посещения
    public function addActivityToDB($tableName = "user", $field_name_activity = "activity", $field_name_date = "date_last"){
        $id = MSS::$userData['id'];
        if(!$id){
            echo '<span style="color:red;"><b>'.__METHOD__.'</b> ошибка</span>: не указан id!';
        }
        $numberInc = (int)++MSS::$userData['activity']; //увеличиваем на 1
        $date = time();
        //пишем в бд
        $sql = "UPDATE `$tableName`
        SET $field_name_activity='$numberInc', $field_name_date='$date' WHERE id = '$id'" ;
        $query = $this->mysqli->query($sql) or die("ERROR: ".__METHOD__.": ". mysqli_error($this->mysqli));//true
    }                   

    //добавление данных в БД
    public function universalInsertDB($tableName = false,$dataArray = false){

    if($tableName == false){die('<b>'.__METHOD__.'</b>: ошибка! Не указано имя таблицы!');}//проверка имени обновляемой таблицы
          $colNames = self::tableColnames($tableName); //выбираем названия всех полей таблицы
          foreach ($colNames as $key=>$value){
              //если элемент массива POST ключ которого соответствуюет текущему(в цикле) названию поля
              // не пуст - берём его значение и добавляем в строку запроса
              if(isset($dataArray[$value])){
                  $fields = $fields.$value.", ";
                  $finishedValue = trim($dataArray[$value]);//обрабатываем
                  $values = $values."'".$finishedValue."', ";
                  //$values = $values."'".strip_tags(trim($dataArray[$value]))."', ";
              }
          }
          $fields = substr($fields, 0, -2); //убираем запятую и пробел в конце строки
          $values = substr($values, 0, -2); //убираем запятую и пробел в конце строки
          //$set = substr($set, 0, -2); //убираем запятую и пробел в конце строки
          
          $sql = "INSERT INTO $tableName ($fields) VALUES ($values)";

        //var_dump($sql);
        $query = mysqli_query($this->mysqli,$sql) or die("ERROR: ".mysqli_error($this->mysqli));//true//true
        $id_last = mysqli_insert_id($this->mysqli);//возвращает ID сгенерированный при последней операции
        if($query){
            return $id_last;
        }else{
            die("Ошибка записи данных в БД 126");
            return false;
        }
    }
    
    //получаем все записи из указанной таблицы
    public function universalSelect($tableName, $fieldName = false, $arg = false){
        
        if(($fieldName !== false) AND ($arg !== false)){
           
            $sql = "SELECT * FROM $tableName WHERE $fieldName = '$arg'";
            $query = $this->mysqli->query($sql);//true
        
            $i = 0;
            do {
                $sub_rez[$i] = mysqli_fetch_assoc($query);
                //проверяем результат mysqli_fetch_assoc
                if(is_null($sub_rez[$i])){
                    
                    break;
                    
                }else{
                    
                    $result[$i] = $sub_rez[$i];
                    
                }
                
                $i++;
                
            }while(true);
            
            return $result;
        }
        
        $sql = "SELECT * FROM $tableName";
        $query = $this->mysqli->query($sql);//true
        
        $i = 0;
        do {
            $sub_rez[$i] = mysqli_fetch_assoc($query);
            //проверяем результат mysqli_fetch_assoc
            if(is_null($sub_rez[$i])){
                
                break;
                
            }else{
                
                $result[$i] = $sub_rez[$i];
                
            }
            
            $i++;
            
            }while(true);

        return $result;
        
    }
    
//обновление данных в БД (исп. ООП)
    public function universalUpdateDB($tableName = false, $id = false, $dataArray = false){
        
        if((!$tableName) or (!$id) or (!$dataArray)){
            echo 'Ошибка: не указано имя таблицы или id! (in universalUpdateDB())';
        }
        $colNames = self::tableColnames($tableName); //выбираем названия всех полей таблицы
        foreach ($colNames as $key=>$value){
            //если элемент массива POST ключ которого соответствуюет текущему(в цикле) названию поля
            // не пуст - берём его значение и добавляем в строку запроса
            if(isset($dataArray[$value])){
                //если ключ равен id то удаляем этот элемент массива, т.к. обновлять поле id нет смысла
                if($value == 'id'){
                    unset($dataArray[$value]);
                }else{
                    //if(($value = 'file_adress') AND ($tableName == 'video')){
                       // echo addslashes($dataArray[$value]) ; die;
                    //}
                    //$finishedValue = mysqli_real_escape_string($this->mysqli,trim($dataArray[$value]));//экранируем символы в текущем значении
                    $finishedValue = $dataArray[$value];
                    
                    $set = $set.$value."='".$finishedValue."', ";
                     //mysqli_real_escape_string($link,strip_tags(trim($_POST[$value])));
                    //$set = $set.$value."='".mysqli_real_escape_string($this->mysqli,trim($dataArray[$value]))."', ";
                }
            }
        }
        $set = substr($set, 0, -2); //убираем запятую и пробел в конце строки
        //var_dump($colNames);die;
        $sql = "UPDATE $tableName SET $set WHERE id=$id";
        //var_dump($sql);die;
        $query = $this->mysqli->query($sql);//true
        if($query){
            return true;
        }else{
            return false;
           die("Ошибка обновления данных в БД 190");
        }
    }

    //удаление данных по id исп. (ООП)
    //$files_path и $directories_path - обязательно массивы!
    public function dropDataToID($id = false, $tableName = false, $files_path = false, $directories_path = false){
        if((!$tableName) or (!$id)){
            echo 'Ошибка: не указано имя таблицы или id! (in dropDataToID())';
        }
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: удаляю данные из таблицы: '.$tableName.' id: '.$id.' ...';
        $sql = "DELETE FROM $tableName WHERE id='$id'";
        $query = $this->mysqli->query($sql);//true
        //если данные успешно удалены из БД - проверяем нужно ли удалять файлы и директории
        if($query){
            $delFileErrors = 0;
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: данные из таблицы: '.$tableName.' id: '.$id.' - удалены успешно!';
            //если переданы пути удаляемых файлов - удаляем их:
            if($files_path){
                //выбираем переданные пути удаления файлов, и удаляем файлы
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пробую удалить '.count($files_path).' файла ...';
                foreach($files_path as $key => $path){
                        if(is_file($path)){
                            $delFile = unlink($path);
                            if(!$delFile){
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка: файл по адресу: '.$path.' удалить не удалось!';
                                $delFileErrors++;
                            }else{
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл по адресу: '.$path.' удалён успешно!';
                            }
                        }else{
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл по адресу: '.$path.' НЕ найден!';
                        }
                }
            }
            
            if($directories_path){
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пробую удалить '.count($directories_path).' директорий(ю) ...';
                foreach($directories_path as $key => $path){
                    if(is_dir($path)){
                        if(!$this->removeDirectory($path)){
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка: директорию по адресу: '.$path.' удалить не удалось!';
                            $delFileErrors++;
                        }else{
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: директория по адресу: '.$path.' удалена успешно!';
                        }
                    }else{
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: директория по адресу: '.$path.' НЕ найдена!';
                    }
                }
            }
            if($delFileErrors > 0){
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: в процессе удаления допущено:
                 <span style="color:red;">'.$delFileErrors.' ошибок</span>!';
            }else{
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: в процессе удаления ошибок не допущено!';
            }
            return true;
        }else{
            return false;
        }
    }
    
    //удаление директории и вложенных директорий и файлов (рекурсия)
    public function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
           foreach($objs as $obj) {
             is_dir($obj) ? removeDirectory($obj) : unlink($obj);
           }
        }
        rmdir($dir);
        return true;
    }
    
    //универсальная функция для получения данных из БД (С учётом ЧПУ) ООП++
    //$translit - фраза транслитом для ЧПУ например название раздела или статьи
    //$page - текущая страница (int)
    //$action_atribute - атрибут действия(action) для формирования ссылок постраничной навигации
    //$num - число статей (строк) на одной странице
    //$tableName наименование таблицы в БД
    //$order_by - пример: author_surname
    //$asc_desc - сортировка: ASC - по возрастающей или DESC - по убывающей
    // доп. ДЛЯ ПОИСКА: (не используется)
    //$searchedData - содержимое поля по которому вытягиваем строку (например значение id)
    //$searchField - наименование поля в которых смотрим
    public function MsAllSelect($translit,$tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}
        //var_dump($page);
        //считаем кол-во всех записей (строк)

        $sql = "SELECT id FROM `$tableName`";
        
        //var_dump(MsDBConnect::$link);die;
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        // Находим общее число страниц 
		$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
		$page = intval($page); 
		// Если значение $page меньше единицы или отрицательно 
		// переходим на первую страницу 
		// А если слишком большое, то переходим на последнюю 
		if(empty($page) or $page < 0) $page = 1; 
		  if($page > $total) $page = $total;
        // Вычисляем начиная к какого номера 
		// следует выводить записи (строки) 
		$start = $page * $num - $num;
        
        $sql = "SELECT * FROM `$tableName` ORDER by $order_by $asc_desc LIMIT $start, $num ";
            //$sql = "SELECT * FROM `$tableName` WHERE category_id = $category_id ORDER by $order_by $asc_desc LIMIT $start, $num ";
            //var_dump($sql);

        $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
		while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //var_dump($data);
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
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,$page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray; //возвращаем массив 
    }
    
    //выборка всех данных таблицы по заданному условию "WHERE"
    //$category_id - может быть передан для формирования url
    public function MsAllSelectWhere($translit,$tableName, $action_atribute, $page, $num, $whereStr, $category_id = false, $order_by = false, $asc_desc = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}
        if(!$whereStr){die('не передан обязательный атрибут "whereStr"');}
        //var_dump($page);
        //считаем кол-во всех записей (строк)

        $sql = "SELECT id FROM `$tableName` WHERE $whereStr";
        
        //var_dump(MsDBConnect::$link);die;
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        // Находим общее число страниц 
		$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
		$page = intval($page); 
		// Если значение $page меньше единицы или отрицательно 
		// переходим на первую страницу 
		// А если слишком большое, то переходим на последнюю 
		if(empty($page) or $page < 0) $page = 1; 
		  if($page > $total) $page = $total;
        // Вычисляем начиная к какого номера 
		// следует выводить записи (строки) 
		$start = $page * $num - $num;
        
        $sql = "SELECT * FROM `$tableName` WHERE $whereStr ORDER by $order_by $asc_desc LIMIT $start, $num ";
            //$sql = "SELECT * FROM `$tableName` WHERE category_id = $category_id ORDER by $order_by $asc_desc LIMIT $start, $num ";
            //var_dump($sql);

        $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
		while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //var_dump($data);
        //предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;$page1left = false;
        $page1right = false;$page2right = false;$page3right = false;$page4right = false;$page5right = false;$nextpage = false;
        
        
        if(!$category_id){
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
        }else{
            //навигация страниц
    		// Проверяем нужны ли стрелки назад 
    		if ($page != 1) $pervpage = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/1/'.$category_id.'>Начало</a></li> 
    									   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$category_id.'>Назад</a></li> '; 
    		// Проверяем нужны ли стрелки вперед 
            //var_dump($page);
    		if ($page != $total) $nextpage = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$category_id.'>Вперёд</a></li> 
    										   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.$total.'/'.$category_id.'>Последняя</a></li>'; 
    		
    		// Находим две ближайшие станицы с обоих краев, если они есть
            if($page - 5 > 0) $page5left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 5).'/'.$category_id.'>'. ($page - 5) .'</a></li>';
            if($page - 4 > 0) $page4left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 4).'/'.$category_id.'>'. ($page - 4) .'</a></li>';
            if($page - 3 > 0) $page3left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 3).'/'.$category_id.'>'. ($page - 3) .'</a></li>';
    		if($page - 2 > 0) $page2left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 2).'/'.$category_id.'>'. ($page - 2) .'</a></li>'; 
    		if($page - 1 > 0) $page1left = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$category_id.'>'. ($page - 1) .'</a></li>';
            if($page + 1 <= $total) $page1right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$category_id.'>'. ($page + 1) .'</a></li>';
    		if($page + 2 <= $total) $page2right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 2).'/'.$category_id.'>'. ($page + 2) .'</a></li>';
            if($page + 3 <= $total) $page3right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 3).'/'.$category_id.'>'. ($page + 3) .'</a></li>';
            if($page + 4 <= $total) $page4right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 4).'/'.$category_id.'>'. ($page + 4) .'</a></li>';
            if($page + 5 <= $total) $page5right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 5).'/'.$category_id.'>'. ($page + 5) .'</a></li>';
        }
        
        $page = '<li class="active"><span>'.$page.'</span></li>';
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,$page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray; //возвращаем массив 
    }
    
    //выборка статей
    public function AllArticlesData($translit,$tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false, $category_id = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}
        //var_dump($page);
        //считаем кол-во всех записей (строк)
        if($category_id){
            $sql = "SELECT id FROM `$tableName` WHERE category_id = $category_id";
        }else{
            $sql = "SELECT id FROM `$tableName`";
        }
        //var_dump(MsDBConnect::$link);die;
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        // Находим общее число страниц 
		$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
		$page = intval($page); 
		// Если значение $page меньше единицы или отрицательно 
		// переходим на первую страницу 
		// А если слишком большое, то переходим на последнюю 
		if(empty($page) or $page < 0) $page = 1; 
		  if($page > $total) $page = $total;
        // Вычисляем начиная к какого номера 
		// следует выводить записи (строки) 
		$start = $page * $num - $num;
        if ($category_id) { //выборка статей по указанной категории
            $sql = "SELECT t1.id, t1.article_title, t1.article_text, t1.date_add, t1.date_edit, t1.author_source, t1.source_link, t1.article_keywords,
                 t1.views, t2.title, t2.description, t2.admin_info, t2.id AS cat_id     
                FROM `$tableName` t1 LEFT JOIN `article_cat` t2 ON t2.id = t1.category_id WHERE t1.category_id = $category_id ORDER 
                    by $order_by $asc_desc LIMIT $start, $num ";
            //увеличиваем на 1 кол-во просмотров данной категории
            $this->addViewToDB($category_id,'article_cat');
        }else{ //выборка всех статей и их категорий
            $sql = "SELECT t1.id, t1.article_title, t1.article_text, t1.date_add, t1.date_edit, t1.author_source, t1.source_link, t1.article_keywords,
                 t1.views, t2.title, t2.description, t2.admin_info, t2.id AS cat_id     
                FROM `$tableName` t1 LEFT JOIN `article_cat` t2 ON t2.id = t1.category_id ORDER 
                    by $order_by $asc_desc LIMIT $start, $num ";
        }

        $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
		while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //var_dump($data);
        //предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;$page1left = false;
        $page1right = false;$page2right = false;$page3right = false;$page4right = false;$page5right = false;$nextpage = false;
        
        if($category_id){ //указываем в ссылке id категории
            //навигация страниц
    		// Проверяем нужны ли стрелки назад 
    		if ($page != 1) $pervpage = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/1/'.$category_id.'>Начало</a></li> 
    									   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$category_id.'>Назад</a></li> '; 
    		// Проверяем нужны ли стрелки вперед 
            //var_dump($page);
    		if ($page != $total) $nextpage = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$category_id.'>Вперёд</a></li> 
    										   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.$total.'/'.$category_id.'>Последняя</a></li>'; 
    		
    		// Находим две ближайшие станицы с обоих краев, если они есть
            if($page - 5 > 0) $page5left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 5).'/'.$category_id.'>'. ($page - 5) .'</a></li>';
            if($page - 4 > 0) $page4left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 4).'/'.$category_id.'>'. ($page - 4) .'</a></li>';
            if($page - 3 > 0) $page3left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 3).'/'.$category_id.'>'. ($page - 3) .'</a></li>';
    		if($page - 2 > 0) $page2left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 2).'/'.$category_id.'>'. ($page - 2) .'</a></li>'; 
    		if($page - 1 > 0) $page1left = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$category_id.'>'. ($page - 1) .'</a></li>';
            if($page + 1 <= $total) $page1right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$category_id.'>'. ($page + 1) .'</a></li>';
    		if($page + 2 <= $total) $page2right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 2).'/'.$category_id.'>'. ($page + 2) .'</a></li>';
            if($page + 3 <= $total) $page3right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 3).'/'.$category_id.'>'. ($page + 3) .'</a></li>';
            if($page + 4 <= $total) $page4right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 4).'/'.$category_id.'>'. ($page + 4) .'</a></li>';
            if($page + 5 <= $total) $page5right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 5).'/'.$category_id.'>'. ($page + 5) .'</a></li>';
            
      }else{
        
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
      }
        
        $page = '<li class="active"><span>'.$page.'</span></li>';
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,$page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray;
    }
    
    //выборка видео
    public function AllVideoData($translit,$tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false, $category_id = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}
        //var_dump($page);
        //считаем кол-во всех записей (строк)
        if($category_id){
            $sql = "SELECT id FROM `$tableName` WHERE category_id = $category_id";
        }else{
            //if((MSS::$user_role == 'Admin') OR (MSS::$user_role == 'Суперчеловек ;)') OR (MSS::$user_role == 'SuperUser')){
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser')){
                $sql = "SELECT id FROM `$tableName`";
            }else{
                $sql = "SELECT id FROM `$tableName` WHERE access_level != 'closed'";
            }
        }
        //var_dump(MsDBConnect::$link);die;
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        // Находим общее число страниц 
		$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
		$page = intval($page); 
		// Если значение $page меньше единицы или отрицательно 
		// переходим на первую страницу 
		// А если слишком большое, то переходим на последнюю 
		if(empty($page) or $page < 0) $page = 1; 
		  if($page > $total) $page = $total;
        // Вычисляем начиная к какого номера 
		// следует выводить записи (строки) 
		$start = $page * $num - $num;
        if($category_id){ //выборка статей по указанной категории
            //проверяем уровень доступа категории
            $categoryTableName = 'video_cat';
            $sql_2 = "SELECT access_level FROM `$categoryTableName` WHERE id = $category_id";
            //echo $sql_2;
            $query_2 = $this->mysqli->query($sql_2);//true
            $data_2 = mysqli_fetch_assoc($query_2);
            //var_dump($data_2['access_level']);die;
        
            //проверяем СКРЫТЫЕ разделы
            if($data_2['access_level'] != 'closed'){ //если разделы НЕ скрыты то извлекаем данные
                $sql = "SELECT t1.id, t1.video_name, t1.video_comment, t1.date_add, t1.admin_info, t1.video_keywords, t1.edit_info, t1.date_edit,
                     t1.file_adress, t1.views, t2.title, t2.description, t2.admin_info, t2.date_add, t2.id AS cat_id     
                    FROM `$tableName` t1 LEFT JOIN `video_cat` t2 ON t2.id = t1.category_id WHERE t1.category_id = $category_id ORDER 
                        by t1.$order_by $asc_desc LIMIT $start, $num ";
                //увеличиваем на 1 кол-во просмотров данной категории
                $this->addViewToDB($category_id,$categoryTableName);
            }else{ //если скрыты - проверяем права!!!
                if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser')){
                    header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                    exit();
                }
                $sql = "SELECT t1.id, t1.video_name, t1.video_comment, t1.date_add, t1.admin_info, t1.video_keywords, t1.edit_info, t1.date_edit,
                     t1.file_adress, t1.views, t2.title, t2.description, t2.admin_info, t2.date_add, t2.id AS cat_id     
                    FROM `$tableName` t1 LEFT JOIN `video_cat` t2 ON t2.id = t1.category_id WHERE t1.category_id = $category_id ORDER 
                        by t1.$order_by $asc_desc LIMIT $start, $num ";
                //увеличиваем на 1 кол-во просмотров данной категории
                $this->addViewToDB($category_id,$categoryTableName);
            }
        }else{ //выборка ВСЕХ видеозаписей и их категорий (КРОМЕ скрытых)
            //MSS::accessCheck('Admin,Суперчеловек ;),SuperUser,User,Guest');
            //if((MSS::$user_role == 'Admin') OR (MSS::$user_role == 'Суперчеловек ;)') OR (MSS::$user_role == 'SuperUser')){
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser')){
                $sql = "SELECT t1.id, t1.video_name, t1.video_comment, t1.date_add, t1.admin_info, t1.video_keywords, t1.edit_info, t1.date_edit,
                     t1.file_adress, t1.views, t1.access_level, t2.title, t2.description, t2.admin_info, t2.date_add, t2.id AS cat_id     
                    FROM `$tableName` t1 LEFT JOIN `video_cat` t2 ON t2.id = t1.category_id ORDER by t1.$order_by $asc_desc LIMIT $start, $num ";
            }else{
                $sql = "SELECT t1.id, t1.video_name, t1.video_comment, t1.date_add, t1.admin_info, t1.video_keywords, t1.edit_info, t1.date_edit,
                     t1.file_adress, t1.views, t1.access_level, t2.title, t2.description, t2.admin_info, t2.date_add, t2.id AS cat_id     
                    FROM `$tableName` t1 LEFT JOIN `video_cat` t2 ON t2.id = t1.category_id 
                     WHERE t1.access_level != 'closed' ORDER by t1.$order_by $asc_desc LIMIT $start, $num ";
            }
                    // ДЛЯ ОБРАЗЦА WHERE t1.access_level != 1 AND t1.category_id != 2 AND t1.category_id != 3  - работает! WHERE t1.access_level != `closed`
                    //die($sql);
                    
                    #$sql = "SELECT t1.id, t1.video_name, t1.video_comment, t1.date_add, t1.admin_info, t1.video_keywords, t1.edit_info, t1.date_edit,
#                 t1.file_adress, t1.views, t1.access_level, t2.title, t2.description, t2.admin_info, t2.date_add, t2.id AS cat_id     
#                FROM `$tableName` t1 LEFT JOIN `video_cat` t2 ON t2.id = t1.category_id 
#                 WHERE t1.access_level != 'closed' ORDER by t1.$order_by $asc_desc LIMIT $start, $num ";
        }

        $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
		while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //var_dump($data);die;
        //предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;$page1left = false;
        $page1right = false;$page2right = false;$page3right = false;$page4right = false;$page5right = false;$nextpage = false;
        
        if($category_id){ //указываем в ссылке id категории
            //навигация страниц
    		// Проверяем нужны ли стрелки назад 
    		if ($page != 1) $pervpage = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/1/'.$category_id.'>Начало</a></li> 
    									   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$category_id.'>Назад</a></li> '; 
    		// Проверяем нужны ли стрелки вперед 
            //var_dump($page);
    		if ($page != $total) $nextpage = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$category_id.'>Вперёд</a></li> 
    										   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.$total.'/'.$category_id.'>Последняя</a></li>'; 
    		
    		// Находим две ближайшие станицы с обоих краев, если они есть
            if($page - 5 > 0) $page5left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 5).'/'.$category_id.'>'. ($page - 5) .'</a></li>';
            if($page - 4 > 0) $page4left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 4).'/'.$category_id.'>'. ($page - 4) .'</a></li>';
            if($page - 3 > 0) $page3left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 3).'/'.$category_id.'>'. ($page - 3) .'</a></li>';
    		if($page - 2 > 0) $page2left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 2).'/'.$category_id.'>'. ($page - 2) .'</a></li>'; 
    		if($page - 1 > 0) $page1left = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$category_id.'>'. ($page - 1) .'</a></li>';
            if($page + 1 <= $total) $page1right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$category_id.'>'. ($page + 1) .'</a></li>';
    		if($page + 2 <= $total) $page2right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 2).'/'.$category_id.'>'. ($page + 2) .'</a></li>';
            if($page + 3 <= $total) $page3right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 3).'/'.$category_id.'>'. ($page + 3) .'</a></li>';
            if($page + 4 <= $total) $page4right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 4).'/'.$category_id.'>'. ($page + 4) .'</a></li>';
            if($page + 5 <= $total) $page5right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 5).'/'.$category_id.'>'. ($page + 5) .'</a></li>';
            
      }else{
        
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
      }
        
        $page = '<li class="active"><span>'.$page.'</span></li>';
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,$page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray;
    }

    //метод для получения данных для вывода страниц с перечнием тезисов 
    public function MsAllThesises($translit,$tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}
        //var_dump($page);
            //считаем кол-во всех записей (строк)
        $sql = "SELECT id FROM `$tableName`";
        //var_dump(MsDBConnect::$link);die;
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        // Находим общее число страниц 
		$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
		$page = intval($page); 
		// Если значение $page меньше единицы или отрицательно 
		// переходим на первую страницу 
		// А если слишком большое, то переходим на последнюю 
		if(empty($page) or $page < 0) $page = 1; 
		  if($page > $total) $page = $total;
        // Вычисляем начиная к какого номера 
		// следует выводить записи (строки) 
		$start = $page * $num - $num;

        $sql = "SELECT t1.id, t1.thesis_text, t1.thesis_born, t1.thesis_link, t1.date_add, t1.admin_info, t1.edit_info, t1.date_edit, t1.thesis_keywords,
                 t1.views, t2.aunhor_name, t2.author_surname, t2.author_nik, t2.id AS author_id     
                FROM `$tableName` t1 LEFT JOIN author t2 ON t2.id = t1.thesis_source_id ORDER by $order_by $asc_desc LIMIT $start, $num ";
        //echo $sql;
        $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
        //$i=0;
		while ($data[] = mysqli_fetch_assoc($query));//{
            
            /**
 * $source = $data[$i]['thesis_source_id'];
 *             $sql2 = "SELECT u.id, u.aunhor_name, u.author_patronymic, u.author_surname, u.author_nik 
 *             FROM author u
 *             INNER JOIN thesis d ON u.id = '$source'";
 *             $query2 = $this->mysqli->query($sql2);//true OOП
 *             if(!$query2){
 *                 echo mysqli_error($this->link);    
 *             }
 *             //var_dump($query2); 
 *            	$data[$i]['getSource'] = mysqli_fetch_assoc($query2);
 *             $i++;
 *         		  echo $sql2;
 */
		//};
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //echo '<pre>';
        //var_dump($data);
        //echo '</pre>';

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
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,$page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray;
    }
    
    
    //функция для выборки ключевых слов (меток) из указанных таблиц (массив $tableNames) и соответсвующих таблицам полей
    //(массив $fieldNames)
    //возвращает строку со всеми найденными ключевыми словами через запятую
   	public function keepMarkerData($tableNames, $fieldNames) {
        $x = count($tableNames);
        
        //выбираем метки из всез таблиц указанных в массиве
        for ($i=1; $i <= $x; $i++) {
            $key = $i - 1; //получаем ключ для элемента массива $tableNames с которым будем работать в текущей итерации
            $tableName = $tableNames[$key]; //имя таблицы
            $fieldName = $fieldNames[$key]; //имя поля
            
            $sql = "SELECT $fieldName FROM `$tableName`";
            //echo  $sql."<br>";
            $query = mysqli_query($this->mysqli,$sql);//true
            while ($data[] = mysqli_fetch_assoc($query)); //получаем ассоциативный массив (последний элемент - пустой =( )
            array_pop($data); //удаляем последний элемент массива (пустой)
            
            foreach ($data as $key => $value){
                //echo  "ключ: - ". $key . " значение: - ". $value[$fieldName]."<br>";
                $keyWordsUnsorted[] = $value[$fieldName];
                //$string = $string.",".$value;
                //echo  $value."<br>";
            }
            unset($data); //обнуляем массив для следующей итерации
            //var_dump($keyWordsUnsorted);
            //var_dump($data); die();
        }
        if(is_array($keyWordsUnsorted)){
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
            return $keyWordsMassiv; //возвращаем массив с данными
        }else{
            return 'ключевых слов пока нет';
        }
    }

    //выборка продукции
    public function productDataSelect($translit,$tableName, $action_atribute, $page, $num, $order_by = 'id', 
                                                                $asc_desc = 'ASC', $main_category_id = false, $category_key, $sub_category_id = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}
        //в зависимости от поступившего ключа - получаем название нужного поля с id (например: category_id или brand_id)
        //$category_id_field_name = $category_key.'_id';
        
        if($category_key == 'category'){
            $main_category_id_field_name = 'category_id';
            $sub_category_id_field_name = 'brand_id';
            $table_2 = 'prod_cat';
            $table_3 = 'prod_brand';
        }elseif($category_key == 'brand'){
            $main_category_id_field_name = 'brand_id';
            $sub_category_id_field_name = 'category_id';
            $table_2 = 'prod_brand';
            $table_3 = 'prod_cat';
        }
        
        //var_dump($sub_category_id);die;
        //считаем кол-во всех записей (строк)
        if(($main_category_id) AND ($sub_category_id)){
            $sql = "SELECT id FROM `$tableName` WHERE $main_category_id_field_name = $main_category_id AND $sub_category_id_field_name = $sub_category_id";
            //die($sql);
        }elseif($main_category_id){
            $sql = "SELECT id FROM `$tableName` WHERE $main_category_id_field_name = $main_category_id";
        }else{
            $sql = "SELECT id FROM `$tableName`";
        }
        //var_dump(MsDBConnect::$link);die;
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        // Находим общее число страниц 
		$total = intval(($positions - 1) / $num) + 1;
        // Определяем начало сообщений для текущей страницы 
		$page = intval($page); 
		// Если значение $page меньше единицы или отрицательно 
		// переходим на первую страницу 
		// А если слишком большое, то переходим на последнюю 
		if(empty($page) or $page < 0) $page = 1; 
		  if($page > $total) $page = $total;
        // Вычисляем начиная к какого номера 
		// следует выводить записи (строки) 
		$start = $page * $num - $num;
        
        
        if(($main_category_id) AND ($sub_category_id)){ //выборка по указанной категории и подкатегории
            /**
 * $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.country, t1.brаnd_name, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
 *                  t1.production_keywords, t1.views, t2.category_name, t2.description      
 *                 FROM `$tableName` t1 LEFT JOIN `prod_cat` t2 ON t2.id = t1.category_id WHERE t1.category_id = $category_id ORDER 
 *                     by $order_by $asc_desc LIMIT $start, $num ";
 */
                    
            $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
                 t1.production_keywords, t1.views, t2.category_name, t2.description, t3.brand_name, t3.brandsite, t3.country
                FROM `$tableName` t1 
                LEFT JOIN `$table_2` t2 ON t2.id = t1.$main_category_id_field_name 
                LEFT JOIN `$table_3` t3 ON t3.id = t1.$sub_category_id_field_name
                WHERE t1.$main_category_id_field_name = $main_category_id AND $sub_category_id_field_name = $sub_category_id ORDER
                    by $order_by $asc_desc LIMIT $start, $num ";
                    
                    //echo $sql.'<br>';
            //увеличиваем на 1 кол-во просмотров данной категории
            //$this->addViewToDB($category_id,'prod_cat');
        }elseif($main_category_id){ //выборка по указанной категории
        //die('fdfdf');
            /**
 * $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.country, t1.brаnd_name, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
 *                  t1.production_keywords, t1.views, t2.category_name, t2.description      
 *                 FROM `$tableName` t1 LEFT JOIN `prod_cat` t2 ON t2.id = t1.category_id WHERE t1.category_id = $category_id ORDER 
 *                     by $order_by $asc_desc LIMIT $start, $num ";
 */
            //если выбираем по брендам
            if($category_key == 'brand'){
                $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
                 t1.production_keywords, t1.views, t2.brand_name, t2.country, t2.brandsite, t2.txt_full, t2.views, t3.category_name, t3.description, t3.views
                FROM `$tableName` t1 
                LEFT JOIN `$table_2` t2 ON t2.id = t1.$main_category_id_field_name 
                LEFT JOIN `$table_3` t3 ON t3.id = t1.$sub_category_id_field_name
                WHERE t1.$main_category_id_field_name = $main_category_id ORDER
                    by $order_by $asc_desc LIMIT $start, $num ";
            //если выбираем по категориям (t2 и t3 меняются местами, соответственно меняем названия полей этих таблиц в запросе)
            }elseif($category_key == 'category'){
                $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
                 t1.production_keywords, t1.views, t2.category_name, t2.description, t3.brand_name, t3.brandsite, t3.country
                FROM `$tableName` t1 
                LEFT JOIN `$table_2` t2 ON t2.id = t1.$main_category_id_field_name 
                LEFT JOIN `$table_3` t3 ON t3.id = t1.$sub_category_id_field_name
                WHERE t1.$main_category_id_field_name = $main_category_id ORDER
                    by $order_by $asc_desc LIMIT $start, $num ";
            }
                    //echo $sql.'<br>';
            //увеличиваем на 1 кол-во просмотров данной категории
            //$this->addViewToDB($category_id,'prod_cat');
        }else{ //выборка всех продуктов и их категорий
            $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
                 t1.production_keywords, t1.views, t2.category_name, t2.description, t3.brand_name, t3.brandsite, t3.country
                FROM `$tableName` t1
                LEFT JOIN `prod_cat` t2 ON t2.id = t1.category_id 
                LEFT JOIN `prod_brand` t3 ON t3.id = t1.brand_id  
                ORDER by $order_by $asc_desc LIMIT $start, $num ";
        }

        $query = $this->mysqli->query($sql);//true
        echo mysqli_error ($this->mysqli);
        //var_dump($query);
        // В цикле переносим результаты запроса в массив $authorsData[]
		while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //var_dump($data);
        //предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;$page1left = false;
        $page1right = false;$page2right = false;$page3right = false;$page4right = false;$page5right = false;$nextpage = false;
        
         if(($main_category_id) AND ($sub_category_id)){
            //навигация страниц
    		// Проверяем нужны ли стрелки назад 
    		if ($page != 1) $pervpage = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/1/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>Начало</a></li> 
    									   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>Назад</a></li> '; 
    		// Проверяем нужны ли стрелки вперед 
            //var_dump($page);
    		if ($page != $total) $nextpage = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>Вперёд</a></li> 
    										   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.$total.'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>Последняя</a></li>'; 
    		
    		// Находим две ближайшие станицы с обоих краев, если они есть
            if($page - 5 > 0) $page5left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 5).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page - 5) .'</a></li>';
            if($page - 4 > 0) $page4left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 4).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page - 4) .'</a></li>';
            if($page - 3 > 0) $page3left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 3).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page - 3) .'</a></li>';
    		if($page - 2 > 0) $page2left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 2).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page - 2) .'</a></li>'; 
    		if($page - 1 > 0) $page1left = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page - 1) .'</a></li>';
            if($page + 1 <= $total) $page1right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page + 1) .'</a></li>';
    		if($page + 2 <= $total) $page2right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 2).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page + 2) .'</a></li>';
            if($page + 3 <= $total) $page3right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 3).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page + 3) .'</a></li>';
            if($page + 4 <= $total) $page4right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 4).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page + 4) .'</a></li>';
            if($page + 5 <= $total) $page5right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 5).'/'.$main_category_id.'/'.$category_key.'/'.$sub_category_id.'>'. ($page + 5) .'</a></li>';
            
        }elseif($main_category_id){ //указываем в ссылке id категории
            //навигация страниц
    		// Проверяем нужны ли стрелки назад 
    		if ($page != 1) $pervpage = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/1/'.$main_category_id.'/'.$category_key.'>Начало</a></li> 
    									   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$main_category_id.'/'.$category_key.'>Назад</a></li> '; 
    		// Проверяем нужны ли стрелки вперед 
            //var_dump($page);
    		if ($page != $total) $nextpage = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$main_category_id.'/'.$category_key.'>Вперёд</a></li> 
    										   <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.$total.'/'.$main_category_id.'/'.$category_key.'>Последняя</a></li>'; 
    		
    		// Находим две ближайшие станицы с обоих краев, если они есть
            if($page - 5 > 0) $page5left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 5).'/'.$main_category_id.'/'.$category_key.'>'. ($page - 5) .'</a></li>';
            if($page - 4 > 0) $page4left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 4).'/'.$main_category_id.'/'.$category_key.'>'. ($page - 4) .'</a></li>';
            if($page - 3 > 0) $page3left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 3).'/'.$main_category_id.'/'.$category_key.'>'. ($page - 3) .'</a></li>';
    		if($page - 2 > 0) $page2left = ' <li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 2).'/'.$main_category_id.'/'.$category_key.'>'. ($page - 2) .'</a></li>'; 
    		if($page - 1 > 0) $page1left = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page - 1).'/'.$main_category_id.'/'.$category_key.'>'. ($page - 1) .'</a></li>';
            if($page + 1 <= $total) $page1right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 1).'/'.$main_category_id.'/'.$category_key.'>'. ($page + 1) .'</a></li>';
    		if($page + 2 <= $total) $page2right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 2).'/'.$main_category_id.'/'.$category_key.'>'. ($page + 2) .'</a></li>';
            if($page + 3 <= $total) $page3right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 3).'/'.$main_category_id.'/'.$category_key.'>'. ($page + 3) .'</a></li>';
            if($page + 4 <= $total) $page4right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 4).'/'.$main_category_id.'/'.$category_key.'>'. ($page + 4) .'</a></li>';
            if($page + 5 <= $total) $page5right = '<li><a href=/'.$translit.'/'.$tableName.'/'.$action_atribute.'/'.($page + 5).'/'.$main_category_id.'/'.$category_key.'>'. ($page + 5) .'</a></li>';
            
      }else{
        
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
      }
        
        $page = '<li class="active"><span>'.$page.'</span></li>';
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,$page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray;
    }
    
    //обработка числа перед записью в БД
	public function clearInt($data){
        $_SESSION['mss_monitor'][] = 'MsDBProcess clearInt(): привожу к числу - '.$data;
		return abs((int)$data);
	}
    
    //получаем кол-во записей в указанной таблице, в соответствии с указанным аргументом и полем
    public function countTableRecords($tableName,$fieldName,$arg){
        $id = $this->clearInt($id);
        if((!$tableName) or (!$id) or (!$arg)){
            $GLOBALS['mss_monitor'][] = __METHOD__.'<span style="color:red;">ошибка</span>: аргументы получены не в полном объёме';
        }
        //считаем кол-во всех авторов
        $sql = "SELECT * FROM `$tableName` WHERE $fieldName = '$arg'";
        //var_dump($this->mysqli);die;
        $query = $this->mysqli->query($sql); // ООП запрос
        $count = mysqli_num_rows($query);
        return $count;
    }
    
    public function selectUserServiceObj($id_user) {

        $sql = "SELECT t1.id, t1.serv_obj_name, t1.id_serv_list, t1.id_city, t1.area_id, t2.service_name, t3.city_name, 
                  t4.area_name, t1.serv_street, t1.serv_building, t1.serv_office, t1.serv_note, t1.tel_num     
                  FROM `service_obj` t1
                  LEFT JOIN `service_list` t2 ON t2.id = t1.id_serv_list 
                  LEFT JOIN `city_list` t3 ON t3.id = t1.id_city
                  LEFT JOIN `area_list` t4 ON t4.id = t1.area_id
                  WHERE t1.id_user = '$id_user'"; 

        $query = $this->mysqli->query($sql);//true
        echo mysqli_error ($this->mysqli);
        
        while ($result[] = mysqli_fetch_assoc($query));
        array_pop($result);//удаляем последний (пустой)элемент массива"
        
        return $result;
    }
}
?>