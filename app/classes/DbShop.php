<?php
/**
 *
 *
 * Created by PhpStorm.
 * User: moskaleny
 * Date: 09.04.2017
 * Time: 20:47
 */
class DbShop
{
    public $mysqli;
    /**
     *    function __construct(){
     *
     *     }
     */
    function __construct() {
        //parent:: __construct();
        //получаем метку соединения
        Core::app()->setLog(__METHOD__."[".__LINE__."] получаю метку соединения ...", "n");
        $this->mysqli = Core::app()->DBase; //получаем метку соединения
    }

    //выборка всех связанных с продуктом данных ajax.js
    public function productSingleSelect($product_id) {

        if (!$product_id) {
            Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] не получен обязательный параметр ...", "e");
            die('<b>Ошибка</b>: не получен обязательный параметр!');
        }
        //выборка данных из таблицы продукта, таблицы соответствующей категории, соответствующего бренда
        $sql = "SELECT t1.id, t1.prod_name, t1.brand_id, t1.v, t1.category_id, t1.txt_full, t1.price, t1.old_price, t1.sklad,
                 t1.production_keywords, t1.views, t2.brand_name, t2.country, t2.brandsite, t2.txt_full AS brand_text, t2.views, t3.category_name, t3.description, t3.views
                FROM `production` t1 
                LEFT JOIN `prod_brand` t2 ON t2.id = t1.brand_id 
                LEFT JOIN `prod_cat` t3 ON t3.id = t1.category_id
                WHERE t1.id = '$product_id' ";

        $query = $this->mysqli->query($sql); // ООП запрос
        echo mysqli_error ($this->mysqli);
        //var_dump($query);die;

        //$query = mysqli_query($this->link,$sql);//true процедурный подход
        $selectedData[] = mysqli_fetch_assoc($query);
        //echo $positions;exit();
        //var_dump($sql); die;
        //результат работы метода - инициализация нижеуказанных свойств
        //$this->selectedDataOnID = $selectedData; //массив
        return $selectedData[0];
    }
}
?>