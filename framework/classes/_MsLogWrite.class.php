<?php
class MsLogWrite{
    
/**
* public $model_name;//имя модели
*     public $action;//вызываемое действие
*     public $params = array();
*/

    //$action - действие (если задано - должно совпасть с кейсом)
    //$saveAll - если  false - режим записи всех логов выключен, если true - включён. Значение задаётся в файле конфигурации приложения
    //$work - включает\отключает работу класса MsLogWrite, true - работает, false - не работает;
    //$adminComment - комментарий администратора, по умолчанию отсутствует;
    //$method имя класса и метода из которого вызван MsLogWrite
    //пример вызова: new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    //'ошибка при подготовке SQL запроса SELECT ('.$sql.') ',__METHOD__);
    function __construct($action = false, $saveAll = false, $work = true, $adminComment = false, $method = false){
        $MsTimeProcess = new MsTimeProcess;
        $runTime = $MsTimeProcess->dateFromTimestamp(time());
        //запись собственных параметров в сессию
        if(!$action){$actionToMonitor = '<b>false</b> не определено...';}
        else{$actionToMonitor = '<b>'.$action.'</b>';}
        
        if(!$saveAll){$saveAllToMonitor = '<b>false</b> (возможно не задано в config/main.php)';}
        else{$saveAllToMonitor = '<b>true</b> (вкл/откл. в config/main.php)';}
        
        if(!$work){$workToMonitor = '<b>false</b> <span style="color: blue">отключена полностью</span> (проверьте в config/main.php)';}
        else{$workToMonitor = '<b>true</b> <span style="color: red"><b>включена</b></span>';}
        
        if(!$adminComment){$commentToMonitor = '<b>false</b> комментарий админа отсутствует';}
        else{$commentToMonitor = 'комментарий админа: <i>'.$adminComment.'</i>';}
        
        if(!$method){$methodToMonitor = '<b>false</b> не определено...';}
        else{$methodToMonitor = '<b>'.$method.'</b>';}
        
        $_SESSION['mss_log_monitor'][] = '<hr><b>'.__METHOD__.'</b>: <b>'.$runTime.'</b> начинаю работу ...';
        $_SESSION['mss_log_monitor'][] = '<b>'.__METHOD__.'</b>: запись в журнал производится в:  '.$methodToMonitor.'';
        $_SESSION['mss_log_monitor'][] = '<b>'.__METHOD__.'</b>: функция записи логов "MsLogWrite" : '.$workToMonitor.'';
        $_SESSION['mss_log_monitor'][] = '<b>'.__METHOD__.'</b>: функция записи логов с неопределённым действием (action) '.$saveAllToMonitor.'';
        $_SESSION['mss_log_monitor'][] = '<b>'.__METHOD__.'</b>: действие '.$actionToMonitor.'';
        $_SESSION['mss_log_monitor'][] = '<b>'.__METHOD__.'</b>: '.$commentToMonitor.'';     
        
        ###################################### запись в лог фаил (журнал посещений) #############################################
        //MSS::$userData['patronymic'];
        //var_dump($x);die();
       if($work == false){
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: режим записи отключен в настройках (отключаюсь)!'; 
            return;
        
       }        //почему срабатывает обновил??????
       
        switch ($action){ 
        	case 'update':
                $action = 'обновление';
                $file_log = 'framework/log/changes_log.mss';
        	break;
        
        	case 'add':
                $action = 'добавление';
                $file_log = 'framework/log/changes_log.mss';
        	break;
        
        	case 'del':
                $action = 'удаление';
                $file_log = 'framework/log/changes_log.mss';
        	break;
            
            case 'gbook_add_comment':
                $action = 'комментирование';
                $file_log = 'framework/log/changes_log.mss';
        	break;
            
            case 'pass_restore':
                $action = 'попытка сменить пароль';
                $file_log = 'framework/log/auth_log.mss';
        	break;
            
            case 'log_in':
                $action = 'авторизация';
                $file_log = 'framework/log/auth_log.mss';
        	break;
            
            case 'registration':
                $action = 'регистрация';
                $file_log = 'framework/log/auth_log.mss';
        	break;
            
            case 'logout':
                $action = 'выход';
                $file_log = 'framework/log/auth_log.mss';
        	break;
            
            case 'error':
                $action = 'Ошибка!';
                $file_log = 'framework/log/error_log.mss';
        	break;
            
        
        	default :
            //если задано писать ВСЁ
            if($saveAll == true){ //сработает только если НЕ указано действие ИЛИ указано не правильно (не попадёт в кейс)
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:red"><b>режим ПОЛНОЙ записи включён!</b></span>';
                if(!$action){$action = ' не установлено ';}
                $file_log = 'framework/log/all_log.mss';
            }else{
                 $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:blue"><b>режим ПОЛНОЙ записи откючён (отключаюсь)</b></span>';
                return true;
            }
        }
        

        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пишу в журнал: '.$file_log.' ...';
        $page_log = $_SERVER['REQUEST_URI'];//текущая страница
        $ref_log = $_SERVER['HTTP_REFERER'];//откуда пришел
        //$ref_log = pathinfo($ref_log, PATHINFO_BASENAME); //убираем http//my-site.com/
        $log_title = '<p><b>'.$runTime.'</b></p>
        
        <p>класс: '.$methodToMonitor.'</p>
            <li>
            '.$adminComment.'<br>
            Действие: <b>'.$action.'</b><br>
            посетил: '.$page_log.'<br>
            пришел с: '.$ref_log.'
            </li>'. "\n";//символ \n писать только в двойных кавычках !!! 
        $file_put = file_put_contents($file_log,$log_title,FILE_APPEND);
        //var_dump($file_put);
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: результат работы file_put_contents: действие - <b>'.$action.'</b> ('.$file_put.')';
        ###################################################################################  
    }
}
?>