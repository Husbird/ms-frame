<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.04.2017
 * Time: 18:35
 */
if (Core::app()->config->debug === true) {
    $_SESSION["debug_mode"] = true; //включаем режим отладки

    $_SESSION["start_time"] = microtime(true); //время начала работы скрипта
    $_SESSION["memory_usage"] = memory_get_usage(); //исп.память на текущий момент
    $_SESSION['db_conn_num'] = 0; //устанавливаем счётчик соединений с БД
    $_SESSION['db_query_num'] = 0; //устанавливаем счётчик запросов к БД
    //$_SESSION['db_query_from'] = array(); //места запросов к БД
    //$_SESSION['db_query_errors'] = array(); //сборщик ошибок запросов к БД
    //$_SESSION['app_log'] = array(); //логи приложения


    /** функция register_shutdown_function(),
     * которая позволит вам выполнить какой-то код
     * перед завершением работы скрипта
     */
    register_shutdown_function( function() {

        //выводим кол-во запросов к БД
        echo "<hr>";
        //выводим информацию запросов к БД
        echo "<div style='display: inline-table; font-size: 12px;'><ol>";
        echo "<p style='color: green'>Queries info:  | DBConnections: ".$_SESSION['db_conn_num']." | DBQueries: ".$_SESSION['db_query_num'].
            " | Time: ". (microtime(true) - $_SESSION['start_time'])." sec.</p>";

        if (is_array($_SESSION['db_query_from'])) {
            foreach ($_SESSION['db_query_from'] as $value) {
                echo "<li>$value</li>";
            }
        } else {
            echo "<li>Запросы к базе данных не выполнялись...</li>";
        }

        if (is_array($_SESSION['db_query_errors'])) {
            foreach ($_SESSION['db_query_errors'] as $value) {
                echo "<li><span style='color: red'>PDOException message: </span>$value</li>";
            }
        } else {
            echo "<li>Ошибок запросов не выявлено ... <span style='color: green'>Оk!</span></li>";
        }
        echo "</ol></div>";

        //выводим информацию журнала логов
        echo "<div style='display: inline-table; font-size: 12px;'><ol>";
        echo "<p style='color: green'>Application log info:  | MemoryMin: ".($_SESSION["memory_usage"] / 1024 / 1024)." MB
         | MemoryMax: ".(memory_get_peak_usage() / 1024 / 1024)." MB | MemoryDelta: "
            . ((memory_get_peak_usage() / 1024 / 1024) - ($_SESSION["memory_usage"] / 1024 / 1024))." MB</p>";
//var_dump($_SESSION['app_log']);
        if (is_array($_SESSION['app_log'])) {
            foreach ($_SESSION['app_log'] as $value2) {
                echo "<li>$value2</li>";
            }
        }
        echo "</ol></div>";

        unset($_SESSION["start_time"]); //чистим сессию от microtime
        unset($_SESSION["memory_usage"]); //чистим сессию от memory_get_usage();
        unset($_SESSION["debug_mode"]); //отключаем режим отладки
        unset($_SESSION['db_conn_num']); //обнуляем счётчик соединений с БД
        unset($_SESSION["db_query_num"]); //обнуляем счётчик запросов к БД
        unset($_SESSION['db_query_from']);
        unset($_SESSION['db_query_errors']); //чистим массив ошибок запросов к БД
        unset($_SESSION['app_log']); // Для очистки раскомментировать и перезагрузить страницу
    });

}

/**
 * //echo "<pre>";
//print_r($data);
//echo "</pre>";
//var_dump($data);
 */

/** измерение времени работы скрипта 
public static function getWorkTime() {
    // Начиная с PHP 5.4.0 в суперглобальном массиве $_SERVER доступно значение REQUEST_TIME_FLOAT.
    // Оно содержит временную метку начала запроса с точностью до микросекунд.
    return round( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 4 )." sec.";
}
*/
?>