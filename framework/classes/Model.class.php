<?php
//переработано
class Model {

    public $actionData = false;//массив данных для сонтроллера (actionView)

    
    function __construct() {
        //подключаем виджет хлебные крошки:
        //$breadcumb = new Breadcrumb();
    }
    
    //выборка ключевых слов к данному разделу (исп в видео add...)
    public function keepMarkerData() {
        //готовим ключевые слова
        $tableNames[] = $this->table_name;
        $fieldNames[] = $this->table_name.'_keywords';
        $data = Core::app()->DBProcess->keepMarkerData($tableNames, $fieldNames);
        return $data;
    }
    
    //получаем системное сообщение из сообветсвующего фаила
    public function getSysMassage($massageName) {
        //var_dump($massageName);
        if ($massageName) {
            $massageName = strtolower( trim($massageName) );
            if (file_exists("framework/components/massages/_$massageName.php")) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    подключаю фаил с системным сообщением. Имя файла: (_".$massageName."php)");
                return include_once("framework/components/massages/_$massageName.php");
            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    фаил с системным сообщением не найден. Имя файла: (_".$massageName."php)");
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    Ошибка подключения файла с системным сообщением см. журнал ошибок");
            }   
        }
    }
}




//извлечение всех данных из указанной таблицы и генерирование пагинации
/*public function AllData($DBNumber = false,$num = 5){
    $dbObj = new MsDBProcess($DBNumber);
    $mixedDataArray = $dbObj->MsAllSelect($this->translit,$this->table_name,$this->action_atribute,$this->page,$num,'id','DESC');
    //var_dump($mixedDataArray);
    return $mixedDataArray;
}*/

//извлечение всех данных определённой категории из указанной таблицы и генерирование пагинации
/**
 * public function AllDataByCategory(){
 *         $dbObj = new MsDBProcess;
 *         $mixedDataArray = $dbObj->MsAllSelect($this->translit,$this->table_name,$this->action_atribute,$this->page,3,'id','DESC',
 *                                                                                                 $this->category_id, $this->table_name2);
 *         //var_dump($mixedDataArray);
 *         return $mixedDataArray;
 *         //$data = $mixedDataArray['data'];
 *         //$pagesNav = $mixedDataArray['pagesNav']
 *         //var_dump($data['data']);
 *     }
 */

//выборка всех данных указанной таблицы
/*
public function getTableInfo($tableName = false){
    $dbObj = new MsDBProcess;
    //массив с ключевыми словами (входящие параметры - обязательно массивы!)
    $mixedDataArray = $dbObj->getTableInfo($tableName);
    //var_dump($mixedDataArray);
    return $mixedDataArray;
}*/
?>