<?php
//ООП +++
//Обработка заказов пользователей
class OrderProcess extends DBProcess {
    
    /**
 * public $model_name;//имя модели
 *     public $action;//вызываемое действие
 *     public $params = array();
 */
    public $mysqli = null;
    public $url_back = false;
    
    function __construct($cookie = false){
        $this->url_back = $_SERVER['HTTP_REFERER'];
        $this->mysqli = Core::app()->DBase; //получаем метку соединения
    }
    
    //запись заказа в базу данных и отправка электронных сообщений
    //$params - массив данных переданный из формы оформления заказа
    public function orderInsertToDB($params){
        $params['order_date'] = time(); //$date_today = date("d.m.y / H:i:s"); //дата и время заказа
        //получаем код заказа
        $params['order_code'] = strtoupper('X-'.substr(md5($this->params['order_date'].$this->params['telephone_num']),1,8));
        $params['products_id'] = $_SESSION['id_ms_product'];

        $add = $this->universalInsertDB('orders',$params);//универсальный метод добавления данных в БД
        
        //обрабатываем результат внесения заказа в БД
        if($add){
            unset($_SESSION['id_ms_product']); //чистим корзину
            $_SESSION['order_confirm'] = 'successful'; //ставим метку "успех"
            
            //отправляем сообщеня на электронную почту. 
            $SendMail = new SendMail();
            $array = array("client" => $params['email'], "developer" => "ms-projects@mail.ru", "admin" => "prosportpit@mail.ru"); //кому отправляем
            $timeProcess = new TimeProcess;
            $order_date = $timeProcess->dateFromTimestamp($params['order_date']);
            $sitePath = Core::app()->config->site_path;
            $subject = "Заказ на сайте ".$sitePath." от ".$order_date.""; //тема сообщения
            $text = "
                <html>
            		<head>
                        <meta charset='windows-1251' />
                        <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
            		</head>
                    <body>
                		<table>
                		<center>
                		<h4>Здравствуйте ".$params['client_name']." !</h4>
                		</center>
                		<tr>
                			<td>
                                <p>Вы осуществили заказ на сайте".$sitePath.", <br>код Вашего заказа: <b>".$params['order_code']."</b></p>
                                <p>Рекомендуем сохранить код заказа до его получения.</p>
                                <span style='color:#333'><a href='".$sitePath."'>Перейти на сайт</a></span>
                			</td>
                		 </tr>
                		 <tr>
                			<td>
                				<i><span style='color:green'>Телефоны для справок:</span></i><br>
                				<i>+380 66 357-99-57 (МТС)</i><br>
                				<i>+380 73 450-87-82 (МТС)</i><br>
                                
                			</td>
                		</tr>
                		 <tr>
                			<td>
                                <i><span style='margin-left:300px'>Спасибо за покупку!</span></i><br>
                                <i><span style='margin-left:300px'>С уважением, администрация $sitePath</span></i>
                                <i><span style='margin-left:300px'>г.Донецк</span></i>
                            </td>
                		 </tr>
                		 <tr>
                			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
                		 </tr>
                		</table>
            		</body>
          		</html>
            "; //полный текст сообщения (с тегами)
            $from = $sitePath; //от кого
            $SendMail->sendMail($array,$from,$subject,$text);//отправка письма
            
            return true;
        }else{
            
            //отправляем сообщение об ошибке на электронную почту. 
            $SendMail = new SendMail();
            $array = array("admin" => "ms-projects@mail.ru"); //кому отправляем
            $timeProcess = new TimeProcess;
            $order_date = $timeProcess->dateFromTimestamp($params['order_date']);
            $sitePath = Core::app()->config->site_path;
            $subject = "Ошибка на сайте ".$sitePath." время ".$order_date.""; //тема сообщения
            $text = "
                <html>
            		<head>
                        <meta charset='windows-1251' />
                        <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
            		</head>
                    <body>
                		<table>
                		<center>
                		<h4>Здравствуйте Админ !</h4>
                		</center>
                		<tr>
                			<td>
                                <p><b>На сайте ".$sitePath.", <br> при оформлении заказа произошла ошибка</b></p>
                                <p>id товаров: ".$params['products_id']."</p>
                                <p>Отправлено: ".__METHOD__."</p>
                                <span style='color:#333'><a href='".$sitePath."'>Перейти на сайт</a></span>
                			</td>
                		 </tr>
                		 <tr>
                			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
                		 </tr>
                		</table>
            		</body>
          		</html>
            "; //полный текст сообщения (с тегами)
            $from = $sitePath; //от кого
            $SendMail->sendMail($array,$from,$subject,$text);//отправка письма
            
             // оставляем товары в корзине (не чистим сессию с id товаров)
            $_SESSION['order_confirm'] = 'abortively';//ставим метку "неудача"
            return false;
        }
    }
    
    //вывод сообщения пользователю о результате обработки его заказа
    public function orderConfirmMassage(){
        if($_SESSION['order_confirm'] == 'successful'){
            echo "
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center;'>
                <br/>
                <div class='alert alert-success' role='alert'>
                    <p><span class='glyphicon glyphicon-saved'><b> Поздравляем!</b> Ваш заказ успешно принят!</p>
                    <p>Код заказа и другие данные о заказе высланы на указанный Вами e-mail.</p>
                    <p>Рекомендуем сохранить эти данные до получения заказа!</p>
                    <p>Спасибо за покупку!</p>
                </div>
                <br/>
            </div>
            ";
            
        }elseif($_SESSION['order_confirm'] == 'abortively'){
            echo '
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <br/>
                <div class="alert alert-danger" role="alert">
                    К сожалению при обработке заказа возникла ошибка, оформить заказ не удалось =(<br />
                    Мы уже знаем об этом, в ближайшее время ошибка будет устранена! <br />
                    Приносим свои извенения. Администрация.
                </div>
            </div>
            ';
        }elseif($_SESSION['order_update'] == 'abort'){
            echo "
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center;'>
                <br/>
                <div class='alert alert-success' role='alert'>
                    <p>Операция успешно выполнена!</p>
                    <p>Заказ был успешно отмечен как <b>отменённый</b>!</p>
                </div>
                <br/>
            </div>
            ";
        }elseif($_SESSION['order_update'] == 'sold'){
            echo "
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center;'>
                <br/>
                <div class='alert alert-success' role='alert'>
                    <p>Операция успешно выполнена!</p>
                    <p>Заказ был успешно отмечен как <b>реализованный</b>!</p>
                </div>
                <br/>
            </div>
            ";
        }elseif($_SESSION['order_update'] == 'failure'){
            echo "
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center;'>
                <br/>
                <div class='alert alert-danger' role='alert'>
                    <p><b>Ощибка</b>! Операция завершилась неудачей =(</p>
                    <p>Сообщите системному администратору!</p>
                </div>
                <br/>
            </div>
            ";
        }
        unset($_SESSION['order_confirm']); //чистим сессию
        unset($_SESSION['order_update']); //чистим сессию
    }
    
    //выборка данных заказов по статусу (например "$statusString = 'current'")
    //$page - текущая страница
    public function SelectOrders($translit,$statusString,$page){
        if($statusString == 'current'){
            $status = 0;
        }elseif($statusString == 'sold'){
             $status = 1;
        }elseif($statusString == 'aborted'){
             $status = 2;
        }
        
        $result = $this->MsAllSelectWhere($translit,'orders','i',$page,3, "status = '$status'", $statusString, 'id', 'DESC');
        return $result;
    }
    //$operationUpdateResult - результат выполнения обновления данных заказа (true или false)
    public function orderInitMassage($status, $operationUpdateResult) {
        //var_dump($this->params['status']); var_dump($operationUpdateResult); die;
        if(($status == 1) AND ($operationUpdateResult)){
            $_SESSION['order_update'] = 'sold';
        }elseif(($status == 2) AND ($operationUpdateResult)){
            $_SESSION['order_update'] = 'abort';
        }else{
            $_SESSION['order_update'] = 'failure';
        }
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
}
?>