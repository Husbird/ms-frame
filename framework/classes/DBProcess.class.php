<?php
/**
 * DBProcess
 * 
 * Работа с базой данных
 * 
 * Created by PhpStorm.
 * Author: Moskaleny
 * Date: 08.04.2017
 * Time: 11:20
 */
class DBProcess {

    //метка соединения с БД
    private $pdo = null;

    function __construct() {
        Core::app()->setLog(__METHOD__."[".__LINE__."] получаю метку соединения ...");

        $this->pdo = Core::app()->DBase;
    }

    /** Возвращает в массиве название всех полей указанной таблицы
     * (ИСПОЛЬЗУЕТСЯ в универсальных методах insert,update)
     */
    public function tableColnames($tableName) {
    // var_dump($tableName);
        try {

            $sql = "SHOW COLUMNS FROM $tableName";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();
            //echo "bible ";var_dump($data);

            $count = count($data);
            $x = 0;

            while ($x < $count) {
                $array[] = $data[$x]['Field'];
                $x++;
            }

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

            return $array;

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }
    }

    //возвращает массив данных из таблиы
    public function getTableInfo($tableName) {

        Core::app()->setLog(__METHOD__."[".__LINE__."] получаю данные таблицы($tableName)");
        $sql = "SELECT * FROM $tableName";

        try {

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $selectedData = $stmt->fetchAll();

            //если нужна одна запись то: $selectedData= $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

            return $selectedData;

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
            //die("Нет доступа к базе данных ...");
        }
    }

    //получаем массив данных по id
    public function selectDataOnID($id, $tableName) {
        $id = Core::app()->clearInt($id);
        
        $sql = "SELECT * FROM `$tableName` WHERE id = :id";
        Core::app()->setLog(__METHOD__."[".__LINE__."] выполняю SQL: ".$sql);

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); //PDO::PARAM_STR
            $stmt->execute();
            //$stmt->execute([':id' => $id]); # No need to escape it!
            $selectedData = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

            return $selectedData[0];

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }
    }

    //добавляем посещение и дату посещения
    public function addActivityToDB($tableName = "user", $field_name_activity = "activity",
                                    $field_name_date = "date_last") {
        $id = Core::$userData['id'];
        $numAdd = ++Core::$userData['activity'];
        $date = time();

        $sql = "UPDATE `$tableName` SET $field_name_activity='$numAdd', $field_name_date='$date' WHERE id = '$id'";
        Core::app()->setLog(__METHOD__."[".__LINE__."] выполняю SQL: ".$sql);

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";
            return true;

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }
    }

    //добавляем просмотр
    public function addViewToDB($id, $tableName) {
        //если админ - не добавляем посещение (завершаем выполнение скрипта)
        if ( Core::app()->accessCheck('Admin,Суперчеловек ;),Moderator') ) {
            return true;
        }

        //получаем имеющиееся кол-во просмотров
        $sql = "SELECT views FROM `$tableName` WHERE id = '$id'";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); //получили только значение view (не асоциативн массив)
            //var_dump($data);die;
            $newView = ++$data[0]; //увеличиваем на 1

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }

        try {
            $sql = "UPDATE `$tableName` SET views='$newView' WHERE id = '$id'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";

            return true;
        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }
    }


    //универсальная функция для получения данных из БД (С учётом ЧПУ)
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
    public function аllSelect($translit, $tableName, $action_atribute, $page, $num,
                                $order_by = false, $asc_desc = false) {
        if (!$translit) {die('не передан атрибут "partNameTranslit"');}
        if (!$tableName) {die('не передан атрибут "tableName"');}
        if (!$action_atribute) {die('не передан атрибут "action"');}
        if (!$num) {die('не передан атрибут "num"');}
        if ($page === false) {die('не передаётся страница!');}
        //var_dump($page);

        //считаем кол-во всех записей (строк)
        $sql = "SELECT id FROM `$tableName`";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $positions = $stmt->rowCount();

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

        $sql = "SELECT * FROM `$tableName` ORDER by $order_by $asc_desc LIMIT $start, $num ";
        //$sql = "SELECT * FROM `$tableName` WHERE category_id = $category_id ORDER by $order_by $asc_desc LIMIT $start, $num ";
        //var_dump($sql);
        Core::app()->setLog(__METHOD__."[".__LINE__."] выполняю SQL: ".$sql);

        try {
            $stmt = $this->pdo->prepare($sql);
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT); //PDO::PARAM_STR
            $stmt->execute();
            //$stmt->execute([':id' => $id]); # No need to escape it!
            $selectedData = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";
            //return $selectedData[0];

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }
        //предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;
        $page1left = false;$page1right = false;$page2right = false;$page3right = false;$page4right = false;
        $page5right = false;$nextpage = false;

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
            'data' => $selectedData,
            'pagesNav' => $pages,
        );
        return $dataArray; //возвращаем массив 
    }

    //добавление данных в БД
    public function universalInsertDB($tableName = false, $dataArray = false) {
        //var_dump($tableName);
        //выбираем названия всех полей таблицы
        $colNames = self::tableColnames($tableName);
        //var_dump($colNames);

        $fields = "";
        $values = "";

        foreach ($colNames as $key => $value) {
            //если элемент массива POST ключ которого соответствуюет текущему(в цикле) названию поля
            // не пуст - берём его значение и добавляем в строку запроса
            if ( isset($dataArray[$value]) ) {
                $fields = $fields.$value.", ";
                $finishedValue = trim(addslashes($dataArray[$value]));//обрабатываем
                $values = $values."'".$finishedValue."', ";
                //$values = $values."'".strip_tags(trim($dataArray[$value]))."', ";
            } else {
                $fields = $fields.$value.", ";
                $values = $values."'0', ";
            }
        }
        //var_dump($fields);
        $fields = substr($fields, 0, -2); //убираем запятую и пробел в конце строки
        $values = substr($values, 0, -2); //убираем запятую и пробел в конце строки
        $values = Core::app()->checkStr($values); //проверка полученной строки
        // var_dump($fields);

        try {

            $sql = "INSERT INTO $tableName ($fields) VALUES ($values)";
            //echo "<hr>".$sql; die;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql."
             | затронуто: ".$stmt->rowCount()." строк | возвращаю id новой записи: 
             <b>".$this->pdo->lastInsertId()."</b>";

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            die; 
            return false;
        }
    }

    //получаем все записи из указанной таблицы
    //$tableName - обязательный аргумент
    public function universalSelect($tableName, $fieldName = false, $arg = false) {

        if (($fieldName !== false) AND ($arg !== false)) {

            $sql = "SELECT * FROM $tableName WHERE $fieldName = '$arg'";
            $query = $this->mysqli->query($sql);//true

            $i = 0;
            do {
                $sub_rez[$i] = mysqli_fetch_assoc($query);
                //проверяем результат mysqli_fetch_assoc
                if ( is_null($sub_rez[$i]) ) {
                    break;
                } else {
                    $result[$i] = $sub_rez[$i];
                }
                $i++;
            } while(true);

            return $result;
        }

        $sql = "SELECT * FROM $tableName";
        $query = $this->mysqli->query($sql);//true

        $i = 0;
        do {
            $sub_rez[$i] = mysqli_fetch_assoc($query);
            //проверяем результат mysqli_fetch_assoc
            if (is_null($sub_rez[$i])) {
                break;
            } else {
                $result[$i] = $sub_rez[$i];
            }
            $i++;
        } while(true);

        return $result;

    }

    //обновление данных в БД
    public function universalUpdateDB($tableName = false, $id = false, $dataArray = false){

        if( (!$tableName) or (!$id) or (!$dataArray) ) {
            Core::app()->LogWriter->setLog(__METHOD__ ."[".__LINE__."] Ошибка: не указано имя таблицы или id!", "e");
            die("Ошибка обработки данных...");
        }

        //var_dump($tableName);
        $colNames = self::tableColnames($tableName); //выбираем названия всех полей таблицы
        foreach ($colNames as $key=>$value) {
            //если элемент массива POST ключ которого соответствуюет текущему(в цикле) названию поля
            // не пуст - берём его значение и добавляем в строку запроса
            if (isset($dataArray[$value])) {
                //если ключ равен id то удаляем этот элемент массива, т.к. обновлять поле id нет смысла
                if ($value == 'id') {
                    unset($dataArray[$value]);
                } else {
                    //if(($value = 'file_adress') AND ($tableName == 'video')){
                    // echo addslashes($dataArray[$value]) ; die;
                    //}
                    //$finishedValue = mysqli_real_escape_string($this->mysqli,trim($dataArray[$value]));//экранируем символы в текущем значении
                    $finishedValue = addslashes($dataArray[$value]);

                    $set = $set.$value."='".$finishedValue."', ";
                    //mysqli_real_escape_string($link,strip_tags(trim($_POST[$value])));
                    //$set = $set.$value."='".mysqli_real_escape_string($this->mysqli,trim($dataArray[$value]))."', ";
                }
            }
        }
        $set = substr($set, 0, -2); //убираем запятую и пробел в конце строки

        try {

            $sql = "UPDATE $tableName SET $set WHERE id=$id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql."
             | затронуто: ".$stmt->rowCount()." строк";

            return $stmt->rowCount();

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            //die; //чтобы увидеть ошибку - раскомментировать и включить режим "debug" (true) 
            return false;
        }
    }

    //удаление данных по id исп. (ООП)
    //$files_path и $directories_path - обязательно массивы!
    public function dropDataToID($id = false, $tableName = false, $files_path = false, $directories_path = false) {
        if ( (!$tableName) || (!$id) ) {
            echo 'Ошибка: не указано имя таблицы или id! (in dropDataToID())';
        }

        try {

            $sql = "DELETE FROM $tableName WHERE id='$id'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql."
             | затронуто: ".$stmt->rowCount()." строк | возвращаю id новой записи: 
             <b>".$this->pdo->lastInsertId()."</b>";
            $result = true;
            //return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }

        //$query = $this->mysqli->query($sql);//true
        //если данные успешно удалены из БД - проверяем нужно ли удалять файлы и директории
        if ($result === true) {
            $delFileErrors = 0;
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: данные из таблицы: '.$tableName.' id: '.$id.' - удалены успешно!';
            //если переданы пути удаляемых файлов - удаляем их:
            if ($files_path) {
                //выбираем переданные пути удаления файлов, и удаляем файлы
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пробую удалить '.count($files_path).' файла ...';
                foreach ($files_path as $key => $path) {
                    if (is_file($path)) {
                        $delFile = unlink($path);
                        if (!$delFile) {
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка: файл по адресу: '.$path.' удалить не удалось!';
                            $delFileErrors++;
                        } else {
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл по адресу: '.$path.' удалён успешно!';
                        }
                    } else {
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл по адресу: '.$path.' НЕ найден!';
                    }
                }
            }

            if ($directories_path) {
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пробую удалить '.count($directories_path).' директорий(ю) ...';
                foreach ($directories_path as $key => $path) {
                    if (is_dir($path)) {
                        if (!$this->removeDirectory($path)) {
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка: директорию по адресу: '.$path.' удалить не удалось!';
                            $delFileErrors++;
                        } else {
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: директория по адресу: '.$path.' удалена успешно!';
                        }
                    } else {
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: директория по адресу: '.$path.' НЕ найдена!';
                    }
                }
            }
            if ($delFileErrors > 0) {
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: в процессе удаления допущено:
                 <span style="color:red;">'.$delFileErrors.' ошибок</span>!';
            } else {
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: в процессе удаления ошибок не допущено!';
            }
            return true;
        } else {
            return false;
        }
    }

    //удаление директории и вложенных директорий и файлов (рекурсия)
    public function removeDirectory ($dir) {
        if ($objs = glob($dir."/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
        return true;
    }

    //функция для выборки ключевых слов (меток) из указанных таблиц (массив $tableNames) и соответсвующих таблицам полей
    //(массив $fieldNames)
    //возвращает строку со всеми найденными ключевыми словами через запятую
    public function keepMarkerData ($tableNames, $fieldNames) {
        $x = count($tableNames);

        //выбираем метки из всез таблиц указанных в массиве
        for ($i=1; $i <= $x; $i++) {
            $key = $i - 1; //получаем ключ для элемента массива $tableNames с которым будем работать в текущей итерации
            $tableName = $tableNames[$key]; //имя таблицы
            $fieldName = $fieldNames[$key]; //имя поля

            $sql = "SELECT $fieldName FROM `$tableName`";
            
            /*$query = mysqli_query($this->mysqli,$sql);//true
            while ($data[] = mysqli_fetch_assoc($query)); //получаем ассоциативный массив (последний элемент - пустой =( )
            array_pop($data); //удаляем последний элемент массива (пустой)*/

            try {
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll();

                $_SESSION['db_query_num']++;
                $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";
                //return $selectedData[0];

            } catch (PDOException $e) {
                $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
                return false;
            }

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
        if (is_array($keyWordsUnsorted)) {
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
        } else {
            return 'ключевых слов пока нет';
        }
    }
}
?>