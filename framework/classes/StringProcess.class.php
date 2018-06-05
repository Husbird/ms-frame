<?php
//набор функций обработки строк
class StringProcess
{
    public $mysqli;
 /**
 *    function __construct(){
 *         
 *     }
 */
    //вырезаем имя админа из строки типа: id: 72 | Имя: Сергей | IP:127.0.0.1
    //ВНИМАНИЕ: работает только если кодировка файла (содержащего данную функцию) UTF-8
    public function cutAdminName_mss($string) {
        //var_dump($string);
        $string = strstr($string, 'Имя'); //получаем: Имя: Сергей | IP:127.0.0.1
        //var_dump($string);
        $string = strstr($string, '| IP', true); //получаем: Имя: Сергей
        $string = substr($string, 7, 15);//получаем: Сергей    (+9 символов пустых после имени )
        
        return trim($string); //убираем лишние пробелы
    }
    
    public function cutText_mss($string, $number) {
        $cutText = mb_substr($string, 0, $number)."...";
        //$cutText[strlen($cutText)-3]="...";
        //$cutText = preg_replace("/(.)$/", "", $cutText);
        return nl2br($cutText); //nl2br - сохраняем переносы строк
    }
    
    //вывод меток (ключевых слов) принимает массив
    public function echoKeyWords($keyWords) {
	    if( is_array($keyWords) ){
            echo "<div style='max-width:90%; margin: 0 auto; margin:2%;'>";
            foreach ($keyWords as $key => $value) {
                $value = trim(mb_strtoupper($value, 'UTF-8'));
                    $backgroundColor = 'background-color:#000';
                echo "<div style='padding:3px; $backgroundColor; display:inline-block; margin:3px;'>
                    <span style='color:#fff;
                     text-decoration: none;'>$value</span></div>";
            }
                echo "</div>";
        } else {
            echo 'ключевых слов\меток не найдено...';
        }
    }
    
    //обработка строковых данных получяемых из форм ввода $mysqli  link
    public function checkStr($string) {
        $this->mysqli = Core::app()->DBase;
        $str = mysqli_real_escape_string( $this->mysqli, strip_tags(trim($string)) );
        Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] проверяю строку (".$str.")", "n");
        return ($str);
    }
    
    //делаем строку из массива
    public function stringFromArray($arr){
        
        if ( is_array($arr) ) {
            $this->mysqli = Core::app()->DBase;
            
            $str = '';
            
            foreach ($arr as $key => $value) {
                if ($value) {
                    $str = $str.', '.$value;
                }
            }
            
            $str = trim(substr($str, 1));//убираем запятую в начале
            //var_dump($str);die;
            $str = mysqli_real_escape_string($this->mysqli, strip_tags($str));
        }
        
        return $str;
    }

    //Преобразует специальные HTML-сущности обратно в соответствующие символы
    //ENT_COMPAT    Преобразует двойные кавычки и пропускает одинарные.
    //ENT_QUOTES    Преобразует и двойные, и одинарные кавычки.
    //ENT_NOQUOTES    Не преобразует ни двойные, ни одинарные кавычки.
    //ENT_HTML401   Обрабатывать код как HTML 4.01.
    //ENT_XML1  Обрабатывать код как XML 1.
    //ENT_XHTML Обрабатывать код как XHTML.
    //ENT_HTML5 Обрабатывать код как HTML 5.
    public function arrayDecode($arr, $flag) {
        $decode_data = array();
        foreach ($arr as $k => $value) {

            switch ($flag) {
                case 'ENT_QUOTES':
                    $decode_string = htmlspecialchars_decode($value, ENT_QUOTES);
                    break;

                case 'ENT_NOQUOTES':
                    $decode_string = htmlspecialchars_decode($value, ENT_NOQUOTES);
                    break;
                
                default:
                    $decode_string = htmlspecialchars_decode($value, ENT_HTML5);
                    break;
            }

            $decode_data[$k] = $decode_string;
        }

        return $decode_data;
    }

    //возвращает переработанный текст с картинками 
    public function textImgParser($text, $img_dir,$alt_tag) {
        //strpos - Возвращает позицию, в которой находится искомая строка или false если не найдена
        while ( (strpos($text, "[img]")) !== false ) {
            $pos1 = strpos($text, "[img]"); //первая позиция
            $pos2 = strpos($text, "[/img]") + 6;// последняя позиция
            //$pos2 = strrpos($data['article_text'], "[/img]");
            $length = $pos2 - $pos1; //длинна найденной подстроки
            $str = substr ( $text , $pos1, $length ); //извекаем подстроку [img][L]IMG_20170724_164444.jpg[/L][/img]
            $pagePosition = substr($str, 5, 3); // подстрока заданного расположения картинки ([L])
            $str_lengt = strlen($str); // длинна подстроки
            $max_width = substr($str, ($str_lengt - 9), 2); //максимальный размер
            $picture_name = substr($str, 8, ($str_lengt - 8 - 10)); //имя файла
            $img_path = $img_dir.$picture_name;//путь к изображению
            switch ($pagePosition) {
                case '[L]':
                    $new_str = "<figure><img src='$img_path' align='left' vspace='5' hspace='5' alt='$alt_tag' 
                        style='max-width: $max_width%; border-radius: 6px;'/></figure>";
                    break;
                case '[R]':
                    $new_str = "<figure><img src='$img_path' align='right' vspace='5' hspace='5' alt='$alt_tag'
                        style='max-width: $max_width%; border-radius: 6px;'/></figure>";
                    break;
                default:
                    $new_str = "<figure style='text-align: center;'><img src='$img_path' align='$class' vspace='5' hspace='5' 
                    alt='$alt_tag' style='max-width: $max_width%; border-radius: 6px;'/></figure>";
                    break;
            }
            $text = substr_replace ( $text , $new_str , $pos1, $str_lengt );
        }
         //var_dump($text);die;
        return $text;
    }

    // convert the tags of the form "[]" into tags "<>"
    public function textBracketsDecode($text) {
        preg_match_all('#.{1}#uis', $text, $arr); // split the string into an array of characters
        $resultArr = str_replace ( "[" , "<" , $arr[0]);
        $resultArr = str_replace ( "]" , ">" , $resultArr);
        return implode("", $resultArr); // get a string from the array and return
    }
}
?>