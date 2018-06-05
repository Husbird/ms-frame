<?php
session_start();
header("Content-Type: text/html; charset=utf-8");//устанавливаем кодировку

ini_set("display_errors",'1');
ini_set("track_errors",'1');
ini_set("html_errors",'1');
error_reporting(E_ERROR | E_PARSE);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);// Выводить все PHP ошибки

require_once("framework/classes/Core.class.php"); //класс приложения
require_once("framework/__autoload.php"); //функция автозагрузки
require_once("framework/__debug.php"); //функции для отладки

new Controller;//включаем Front Controller!
?>