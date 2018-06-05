<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.04.2017
 * Time: 12:25
 */
class HtmlDetails {

    private $button_access = false; // статус кнопки показать\скрыть
    public $button_html_code = false; // html код кнопки
    public $rules ="Admin,Суперчеловек ;),Moderator"; //права доступа (по умолчанию)
    public $btn_title = "Title"; // надпись на кнопке (по умолчанию)
    public $btn_href = "#"; // надпись на кнопке (по умолчанию)

    function __construct() {
        
    }

    //далее метод сравнивает указанные категории с текущей категорией и запускает соответствующие сценарии
    public static function delButtonRun($id = false, $table_name = false, 
        $file_path = false, $dir_path = false, $back_url = false, $button_title = "Удалить") {

        if(!$id){
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки id!!!';
            return '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки id!!!';
        }
        if(!$table_name){
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки table_name!!!';
            return '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки table_name!!!';
        }
        //если массив не пуст - кодируем его (для отправки в форме)
        //если пуст - присваиваем нуль
        if(!empty($file_path)){
            $file_path = serialize($file_path);
            $file_path = base64_encode($file_path);
        }else{
            $file_path = 0;
        }
        //если массив не пуст - кодируем его (для отправки в форме)
        //если пуст - присваиваем нуль
        if(!empty($dir_path)){
            $dir_path = serialize($dir_path);
            $dir_path = base64_encode($dir_path);
        }else{
            $dir_path = 0;
        }
        //если указан $back_url - оставляем его, если нет - присваиваем $_SERVER['HTTP_REFERER']
        if(!$back_url){
            $back_url = $_SERVER['HTTP_REFERER'];
        }
        return '
        <form method="post" action="/" role="form">
            <input name="id" type="hidden" value="'.$id.'" />
            <input name="table_name" type="hidden" value="'.$table_name.'" />
            <input name="file_path" type="hidden" value="'.$file_path.'" />
            <input name="dir_path" type="hidden" value="'.$dir_path.'" />
            <input name="back_url" type="hidden" value="'.$back_url.'" />
            <button name="del" type="submit" class="btn btn-danger btn-sm">'.$button_title.'</button>
        </form>';
    }

    private function buttonCheckRules() {
            //в зависимости от прав включаем кнопки
            if ( Core::app()->accessCheck($this->rules) ) {
                $this->button_access = true;
            }
    }

    // $buttonName - название кнопки "edit","delete", "add"
    public function button_get($buttonName) {
        $this->buttonCheckRules();
        if ($this->button_access === true) {
            switch ($buttonName) {
                case 'edit':
                    $this->button_html_code = '<p><a href="'.$this->btn_href.'">
                        <button type="button" class="btn btn-primary btn-sm">'.$this->btn_title.'</button></a></p>';
                    break;

                case 'add':
                    # code...
                    break;

                case 'delete':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }

}

?>