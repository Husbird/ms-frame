<?php
/**
 * Created by PhpStorm.
 * User: MS-projects
 * Date: 01.04.2017
 * Time: 0:25
 */

//функция автозагрузки классов
function __autoload($class) {
    if (file_exists(Core::app()->config->framework_classes.$class.".class.php"))
        require_once(Core::app()->config->framework_classes.$class.".class.php");//файлы классы фреймворка

    if (file_exists(Core::app()->config->app_classes_path.$class.".php"))
        require_once(Core::app()->config->app_classes_path.$class.".php");//файлы классы приложения (пользовательские)

    if (file_exists(Core::app()->config->controller_path.$class.".php"))
        require_once(Core::app()->config->controller_path.$class.".php");//файлы сонтроллеров приложения

    if (file_exists(Core::app()->config->models_path.$class.".php"))
        require_once(Core::app()->config->models_path.$class.".php");//файлы моделей приложения

    if (file_exists(Core::app()->config->framework_widgets.$class.".php"))
        require_once(Core::app()->config->framework_widgets.$class.".php");//файлы виджетов
    //var_dump(Core::app()->config->framework_classes.$class.".class.php"); //framework/classes/MsController.class.php framework_widgets
}
?>