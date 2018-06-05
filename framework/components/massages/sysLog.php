<?php
if((MSS::app()->config->debug === true) AND (MSS::$user_role == 'Admin')){
    echo '<div class="row" style="margin-bottom:3%;">';
        //выводим зарегистрированных пользователей
        echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">';
            echo '<h2>Зарегистрированные пользователи</h2>';
            $MsDBProcess = new MsDBProcess;
            $MsTimeProcess = new MsTimeProcess;
            //$mixedDataArray = $MsDBProcess->MsAllSelect('translit','user',1,0,3,'id','ASC');
            $MsDBProcess->listTableInfo('user');

            /**
 * foreach ($mixedDataArray as $key => $value) {
 *                 if($value[0]['id']){
 *                     $date_reg = $MsTimeProcess->dateFromTimestamp($value[0]['date_reg']);
 *                     $date_last = $MsTimeProcess->dateFromTimestamp($value[0]['date_last']);
 *                     echo 'ID: '.$value[0]['id'].'<br>';
 *                     echo 'Имя: '.$value[0]['name'].'<br>';
 *                     echo 'Отчество: '.$value[0]['patronymic'].'<br>';
 *                     echo 'E-mail: '.$value[0]['email'].'<br>';
 *                     echo 'Дата регистрации: '.$date_reg.'<br>';
 *                     echo 'Дата посл.авторизации: '.$date_last.'<br>';
 *                     echo 'ip: '.$value[0]['ip'].'<br>';
 *                     echo 'Права: '.$value[0]['adm_mss'].'<br>';
 *                     echo 'Активность: '.$value[0]['activity'].'<hr>';
 *                 }
 *             }
 */
        echo '</div>';
        
        echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">';
            //выводим содержимое журнала работы функции записи в лог файлы
            echo '<h3>Журнал работы "MsLogWrite":</h3>';
            echo '<pre>';
            print_r($_SESSION['mss_log_monitor']);
            echo '</pre>';
            unset($_SESSION['mss_log_monitor']);
        echo '</div>';
        
        echo '<div class="clearfix"></div>';
        
        echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
            //выводим содержимое журнала авторизации
            echo '<h3>"auth_log" <small>журнал идентификации</small></h3>';
            $filename = 'framework/log/auth_log.mss';
            if(is_file($filename)){
                $handle = fopen($filename, "rb");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                echo '<p><small><ul>'.$contents.'</ul></small></p>';
            }
        echo '</div>';
        
        echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
            //выводим содержимое журнала изменений
            echo '<h3>"changes_log"<small> журнал изменения данных</small></h3>';
            $filename = 'framework/log/changes_log.mss';
            if(is_file($filename)){
                $handle = fopen($filename, "rb");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                echo '<p><small><ul>'.$contents.'</ul></small></p>';
            }
        echo '</div>';
        
        echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
            //выводим содержимое журнала изменений
            echo '<h3>"error_log"<small> журнал ошибок</small></h3>';
            $filename = 'framework/log/error_log.mss';
            if(is_file($filename)){
                $handle = fopen($filename, "rb");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                echo '<p><small><ul>'.$contents.'</ul></small></p>';
            }
        echo '</div>';
        
        echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
            //выводим содержимое журнала изменений
            echo '<h3>"all_log"<small> журнал общий</small></h3>';
            $filename = 'framework/log/all_log.mss';
            if(is_file($filename)){
                $handle = fopen($filename, "rb");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                echo '<p><small><ul>'.$contents.'</ul></small></p>';
            }
        echo '</div>';
    echo '</div> <!--row--> ';
}


if((MSS::app()->config->debug === true) AND (MSS::$user_role == 'Admin')){
    echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin: 5%; font-size:13px">';
    echo '<h4><strong>Режим отладки - включён!</strong></h4>';
    echo '<h4><strong>Журнал работы приложения $GLOBALS["mss_monitor"]:</strong></h4>';
    foreach($GLOBALS['mss_monitor'] as $value){
        echo "$value <br/>";
    }
    //unset($_COOKIE);
    echo '<hr>';
    echo '<h4><strong>Содержимое глобального массива $_COOKIE:</strong></h4>';
    var_dump($_COOKIE);
    echo '<h4><strong>Содержимое глобального массива $_SESSION:</strong></h4>';
    
    echo '<b>Результат работы ход приложения: (сессия) mss_monitor</b>';
    echo '<pre>';
    print_r($_SESSION['mss_monitor']);
    echo '</pre>';
    
    echo '<b>Текущий ip определённый в MsAuthoriz::GetRealIp (сессия)</b>';
    echo '<pre>';
    var_dump($_SESSION['ip_current']);
    echo '</pre>';
    
    echo '<b>Данные об авторизации (сессия)</b>';
    echo '<pre>';
    var_dump($_SESSION['auth']);
    echo '</pre>';
    
    echo '<b>Ошибки ввода при авторизации (сессия)</b>';
    echo '<pre>';
    var_dump($_SESSION['authErr']);
    echo '</pre>';
    
    echo '<b>Ошибки ввода при регистрации (сессия)</b>';
    echo '<pre>';
    var_dump($_SESSION['registration_error']);
    echo '</pre>';
    
    echo '<hr> Объект данных сгенерированный в модели:<br>';
    var_dump($this->data);
    echo '<hr>';
    echo '</div>';
    
    //unset($_SESSION['mss_monitor']);
}
?>