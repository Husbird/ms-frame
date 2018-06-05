<?php
//набор функций обработки даты и времени
class TimeProcess
{

    
 /**
 *    function __construct(){
 *         
 *     }
 */
    
    //форматируем timestamp в дату формата: 7 Февраля 2016 г.
    public function dateFromTimestamp($timestamp) {
        if (!$timestamp or $timestamp == '') {
            return 'нет данных'; die('Ошибка: dateFromTimestamp()');
        }
        $prevDate = date("j, n, Y, H:i:s",$timestamp);
        $dateMassiv = explode(",", $prevDate);
        $date = $dateMassiv[0]; //дата
        $prevMonth = (int)$dateMassiv[1]; //порядковый номер месяца
        $month = array ("нулевой","Января", "Февраля", "Марта", "Апреля", "Мая", "Июня",
                        "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
        $prevYear = $dateMassiv[2];
        $time = $dateMassiv[3];
        $normalDate = $date." ".$month[$prevMonth]." ".$prevYear." г. ".$time;
        
        return $normalDate;
    }
    
}
?>