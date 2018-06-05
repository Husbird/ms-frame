<?php
//Расширения Блога ТехПомощи
class TechHelpSite
{
    private $pdo;


    function __construct() {
        //parent:: __construct();
        //получаем метку соединения
        Core::app()->setLog(__METHOD__ . "[" . __LINE__ . "] получаю метку соединения ...", "n");
        $this->pdo = Core::app()->DBase;
    }

     //метод для получения данных для вывода страниц с перечнием тезисов 
    public function MsAllThesises($translit,$tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false){
        if(!$translit){die('не передан атрибут "partNameTranslit"');}
        if(!$tableName){die('не передан атрибут "tableName"');}
        if(!$action_atribute){die('не передан атрибут "action"');}
        if(!$num){die('не передан атрибут "num"');}
        if($page === false){die('не передаётся страница!');}

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

        $sql = "SELECT t1.id, t1.thesis_text, t1.description, t1.thesis_link, t1.date_add, t1.admin_info, t1.edit_info, t1.date_edit,
         t1.thesis_keywords, t1.views, t2.aunhor_name, t2.author_surname, t2.author_nik, t2.id AS author_id     
                FROM `$tableName` t1 LEFT JOIN author t2 ON t2.id = t1.thesis_source_id ORDER by $order_by $asc_desc LIMIT $start, $num ";

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
            'data' => $selectedData,
            'pagesNav' => $pages,
        );
        return $dataArray;
    }


    //выборка статей
    public function AllArticlesData($translit,$tableName, $action_atribute, $page, $num, $order_by = false, $asc_desc = false, 
                                    $category_id = false) {
        if (!$translit) { die('не передан атрибут "partNameTranslit"'); }
        if (!$tableName) { die('не передан атрибут "tableName"'); }
        if (!$action_atribute) { die('не передан атрибут "action"'); }
        if (!$num) { die('не передан атрибут "num"'); }
        if ($page === false) { die('не передаётся страница!'); }

        //считаем кол-во всех записей (строк)
        if ($category_id) {
            $sql = "SELECT id FROM `$tableName` WHERE category_id = $category_id";
        } else {
            $sql = "SELECT id FROM `$tableName`";
        }

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

        if ($category_id) { //выборка статей по указанной категории
            $sql = "SELECT t1.id, t1.article_title, t1.article_text, t1.date_add, t1.date_edit, t1.author_source, t1.source_link, t1.article_keywords,
                 t1.views, t2.title, t2.description, t2.admin_info, t2.id AS cat_id     
                FROM `$tableName` t1 LEFT JOIN `article_cat` t2 ON t2.id = t1.category_id WHERE t1.category_id = $category_id ORDER 
                    by $order_by $asc_desc LIMIT $start, $num ";

            //увеличиваем на 1 кол-во просмотров данной категории
            Core::app()->DBProcess->addViewToDB($category_id,'article_cat');

        } else { //выборка всех статей и их категорий
            $sql = "SELECT t1.id, t1.article_title, t1.article_text, t1.date_add, t1.date_edit, t1.author_source, t1.source_link, t1.article_keywords,
                 t1.views, t2.title, t2.description, t2.admin_info, t2.id AS cat_id     
                FROM `$tableName` t1 LEFT JOIN `article_cat` t2 ON t2.id = t1.category_id ORDER 
                    by $order_by $asc_desc LIMIT $start, $num ";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT); //PDO::PARAM_STR
            $stmt->execute();
            //$stmt->execute([':id' => $id]); # No need to escape it!
            $data = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";
            //return $selectedData[0];

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }

       /* $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
        while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"*/

        //var_dump($data);
        //предопределяем ссылки (чтобы избежать ошибки E_NOTICE)
        $pervpage = false;$page5left = false;$page4left = false;$page3left = false;$page2left = false;$page1left = false;
        $page1right = false;$page2right = false;$page3right = false;$page4right = false;$page5right = false;$nextpage = false;
        
        if ($category_id) { //указываем в ссылке id категории
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
            
      } else {
        
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
        $pages = array($pervpage,$page5left,$page4left,$page3left,$page2left,$page1left,$page,$page1right,$page2right,$page3right,
                        $page4right,$page5right,$nextpage);
        
        $dataArray = array(
            'data' => $data,
            'pagesNav' => $pages,
        );
        return $dataArray;
    }



}
?>