<?php
//Упражнения
class MyExercises
{
    public $mysqli;

    //разделы упражнений
    public $partsData = array();

    //группы упражнений
    public $mainData = array();


 /**
 *    function __construct(){
 *         
 *     }
 */
    function __construct() {
        //parent:: __construct();
        //получаем метку соединения 
        Core::app()->setLog(__METHOD__ . "[" . __LINE__ . "] получаю метку соединения ...");
        $this->mysqli = Core::app()->DBase; //получаем метку соединения

        $this->_getParts();//получаем все разделы
        $this->_getMain(); //получаем группы упражнений
    }

    //вывести кнопку "Добавить раздел"
    public function showAddPartButton() {
        if (Core::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator')) {
            echo '<button type="button" class="btn btn-primary btn-sm" onclick="clickAddExPart()">Добавить раздел</button>';
        }
    }

    //вывести кнопку "Добавить группу"
    public function showAddMainButton() {
        if (Core::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator')) {
            echo '<button type="button" class="btn btn-primary btn-sm" onclick="clickAddExMain()">
                        Добавить группу</button>';
        }
    }

    //вывести кнопку "Добавить группу"
    public function showAddExerciseButton() {
        if (Core::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator')) {
            echo '<button type="button" class="btn btn-primary btn-sm" onclick="clickAddExercise()">
                        Добавить упражнение</button>';
        }
    }

    //получить в массиве данные разделов
    protected function _getParts() {
        $this->partsData = Core::app()->DBProcess->getTableInfo("ex_part");
    }

    //получить в массиве данные групп упражнений
    protected function _getMain() {
        $this->mainData = Core::app()->DBProcess->getTableInfo("ex_main");
    }

    //получить в массиве данные указанного числа ресторанов
    public function showExerciseList() {
        $hfu = new Hfu();
        
        echo "<div class='exerciseList'>";
        echo "<div id='miniHeader'>";
        echo "<h4 id='h_exercise'>Список упражнений</h4>";
        echo "</div>";
        foreach ($this->partsData as $key => $parts) {
            echo "<ul id='ul_sidebar_1'>";
            echo "<li>";
            $partTranslit = $hfu->hfu_gen($parts['ex_part_name']);
            //раздел
            echo "<a href='/$partTranslit/exercise/i/{$parts['id']}' class='ex_part_name''>{$parts['ex_part_name']}</a>";

            foreach ($this->mainData as $key2 => $main) {
                if ($main['ex_part_id'] == $parts['id']) {
                    $mainTranslit = $hfu->hfu_gen($main['ex_main_name']);
                    //echo "<ul>";
                    echo "<li>";
                    //группа упражнений
                    echo "<a href='/$mainTranslit/exercise/v/{$main['id']}' class='ex_main_name''>{$main['ex_main_name']}</a>";
                    echo "</li>";
                    //echo "</ul>";
                }
            }

            echo "</li>";
            echo "</ul>";
        }
        echo "</div>";
    }



    //получить в массиве данные указанного числа ресторанов !!!!!!!!!!!!!!!!!ОБРАЗЕЦ!!!!!!
    public function getInfo($start = 0, $quantity = 1){
        
        $sql = "SELECT t1.id, t1.serv_obj_name, t1.id_serv_list, t1.id_city, t1.area_id, 
                  t1.serv_street, t1.serv_building, t1.serv_office, t1.serv_note, t1.tel_num, t1.email, t1.position, t2.service_name,
                  t3.city_name, t4.area_name, t5.average_check, t5.cuisine, t5.cuisine, t5.max_places, t5.own_alcohol,
                  t5.live_music, t5.karaoke, t5.specially_for, t5.serv_opt_note
                  FROM `service_obj` t1
                  LEFT JOIN `service_list` t2 ON t2.id = t1.id_serv_list 
                  LEFT JOIN `city_list` t3 ON t3.id = t1.id_city
                  LEFT JOIN `area_list` t4 ON t4.id = t1.area_id
                  LEFT JOIN `1_serv_opt` t5 ON t5.id_serv_obj = t1.id
                  WHERE t1.id_serv_list = '1' LIMIT $start, $quantity";
                  
        $query = $this->mysqli->query($sql);//true
        echo mysqli_error ($this->mysqli);
        
        while ($result[] = mysqli_fetch_assoc($query));
        array_pop($result);//удаляем последний (пустой)элемент массива"
        
        return $result;
    }
}
?>