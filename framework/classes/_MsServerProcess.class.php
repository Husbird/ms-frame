<?php
//набор функций обработки изображений
class MsServerProcess
{

    
 /**
 *    function __construct(){
 *         
 *     }
 */
    
    //Готовая PHP-функция текущего URL страницы (Не тестировано!!!)
    public function request_url(){
    $result = ''; // Пока результат пуст
    $default_port = 80; // Порт по-умолчанию
    
    // А не в защищенном-ли мы соединении?
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) {
    // В защищенном! Добавим протокол...
    $result .= 'https://';
    // ...и переназначим значение порта по-умолчанию
    $default_port = 443;
    } else {
    // Обычное соединение, обычный протокол
    $result .= 'http://';
    }
    // Имя сервера, напр. site.com или www.site.com
    $result .= $_SERVER['SERVER_NAME'];
    
    // А порт у нас по-умолчанию?
    if ($_SERVER['SERVER_PORT'] != $default_port) {
    // Если нет, то добавим порт в URL
    $result .= ':'.$_SERVER['SERVER_PORT'];
    }
    // Последняя часть запроса (путь и GET-параметры).
    $result .= $_SERVER['REQUEST_URI'];
    // Уфф, вроде получилось!
    return $result;
}
    
}
?>