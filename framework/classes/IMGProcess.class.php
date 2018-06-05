<?php
//набор функций обработки изображений
class IMGProcess
{
 /**
 *    function __construct(){
 *         
 *     }
 */
    //подготовка изображения к выводу на экран по установленному значению максимально-выводимой стороны, с учётом пропорций
    //реального размера изображения
    public function img_out_size_mss($img_path, $max_scale) {
    
    // $img_path - путь к изображению $img_path = 'view/i/prod_img_mss/'.$id.'.jpg';
    //$max_scale - максимально допустимый размер бОльшей стороны изображения
    
    	list($w, $h) = getimagesize($img_path); // получаем размеры изображения
    
    	//если высота больше ширины
    	if ($h > $w) {
    		$one_procent = $h/100; //вычисляем 1% из бОльшей стороны (h)
    		$procent = $w/$one_procent; //вычисляем сколько процентов составляет меньшая сторона (w) от большей (h)
    		
    		//вычисляем размеры картинки для вывода на экран
    		$h_view = $max_scale;//т.к. высота - бОльшая величина - присваиваем ей максимально допустимый размер заданый в $max_scale
    		$w_view = $max_scale/100 * $procent;//т.к h = $max_scale, находим 1% от $max_scale и умножаем на составляющую ширины (w) $procent 
    											// и получаем новый ($w_view) размер ширины с учётом нового размера высоты $h_view
    		$w_view = round($w_view,0);//округляем результат
    	}
    	//если высота меньше ширины
    	if ($w > $h) {
    		$one_procent = $w/100; //вычисляем 1% из бОльшей стороны (h)
    		$procent = $h/$one_procent; //вычисляем сколько процентов составляет меньшая сторона (w) от большей (h)
    		
    		//вычисляем размеры картинки для вывода на экран
    		$w_view = $max_scale;//т.к. ширина - бОльшая величина - присваиваем ей максимально допустимый размер заданый в $max_scale
    		$h_view = $max_scale/100 * $procent;//находим высоту учитывая её процентное отношение к ширине и максимально допустимый размер заданый в $max_scale
    		$h_view = round($h_view,0);//округляем результат
    	}
    	if ($h == $w) {
    		$h_view = $max_scale;
    		$w_view = $max_scale;
    	}
    	
    	//добавляем полученные данные в массив $img_out_size_mss_rezult
    	$img_out_size_mss_rezult[0] = $h_view;//конечная высота
    	$img_out_size_mss_rezult[1] = $w_view;//конечная ширина
    	$img_out_size_mss_rezult[2] = $h;//изначальная высота h
    	$img_out_size_mss_rezult[3] = $w;//изначальная ширина w
    	$img_out_size_mss_rezult[4] = $procent;//сколько процентов составляет меньшая сторона от большей в % (из изначальных параметром h и w)
    	
    	return $img_out_size_mss_rezult;//возвращаем массив
    }

    //функция ресайза (с сохранением пропорций) и записи изображения полученного из формы
    //$max_scale - максимальный допустимый размер наибольшей стороны в пикселах
    //$maxSizeMB - максимальный размер изображения в мегабайтах 
    //$save_path - полный путь для сохранения (вместе с сохраняемым названием файла пример: $save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';) 
    public function cut_and_save_img_mss($max_scale,$maxSizeMB,$save_path) {
        if ($_FILES["image"]["size"] > 1024*$maxSizeMB*1024) {
    		 echo ("Размер файла ".$_FILES["image"]["name"]." превышает $maxSizeMB мегабайт");
    		 exit;
        }
        //var_dump($_FILES);die;
    	   // Проверяем загружен ли файл
        if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
            // Если файл загружен успешно...
            
            //подставляем максимальный размер бОльшей стороны используемый для ресайза $max_real_scale
            $max_real_scale = $max_scale;
            
            $input_img_file = $_FILES["image"]["tmp_name"];
            list($w, $h) = getimagesize($input_img_file); // получаем размеры изображения
            //var_dump($w);var_dump($h);die;
            
            //если ширина больше высоты - задаём ширину = $max_real_scale а высота расчитывается авто с учётом пропорций
            if ($w > $h) {
            	//$save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';//определяем путь для сохранения
            	$this->resize($input_img_file,$save_path,$max_real_scale,0); // задаём ширину 600 (сохраняя пропорции) и сохраняем в $save_path
            }
            
            //если высота больше ширины - задаём высоту = $max_real_scale а ширина расчитывается авто с учётом пропорций
            if ($h > $w) {
            	//$save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';//определяем путь для сохранения
                //var_dump($save_path);die;
            	$this->resize($input_img_file,$save_path,0,$max_real_scale); // задаём высоту 600 (сохраняя пропорции) и сохраняем в $save_path
            }
            
             //если стороны равны - задаём высоту = $max_real_scale а ширина расчитывается авто с учётом пропорций
            if ($w == $h) {
            	//$save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';//определяем путь для сохранения
            	$this->resize($input_img_file,$save_path,$max_real_scale,$max_real_scale); // задаём ширину 600 (сохраняя пропорции) и сохраняем в $save_path
            }
        }//else{
            //die('фаил не загружен!');
        //}
    }
    
    //Функция масштабирования
    public function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {
    	list($w_i, $h_i, $type) = getimagesize($file_input);
    	if (!$w_i || !$h_i) {
    		echo 'Невозможно получить длину и ширину изображения';
    		return;
            }
            $types = array('','gif','jpeg','png');
            $ext = $types[$type];
            if ($ext) {
        	        $func = 'imagecreatefrom'.$ext;
        	        $img = $func($file_input);
            } else {
        	        echo 'Некорректный формат файла';
    		return;
            }
    	if ($percent) {
    		$w_o *= $w_i / 100;
    		$h_o *= $h_i / 100;
    	}
    	if (!$h_o) $h_o = $w_o/($w_i/$h_i);
    	if (!$w_o) $w_o = $h_o/($h_i/$w_i);
    
    	$img_o = imagecreatetruecolor($w_o, $h_o);
    	imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
    	if ($type == 2) {
    		return imagejpeg($img_o,$file_output,100);
    	} else {
    		$func = 'image'.$ext;
    		return $func($img_o,$file_output);
    	}
    }

    public function uploadImg($files_array, $max_size_inMB, $uploaddir) {

        //если каталог не существует - создаём его
        if (!is_dir($uploaddir)) {
            if ( !mkdir($uploaddir, 0777, true) ) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>Не удалось создать директории ...</span>");
                die('Ошибка работы с файлами...');
            }
        }
        
         foreach ($_FILES as $key => $value) {
            //var_dump($_FILES);die;
            /*echo $value["size"]."<br>";
            echo $value['name']."<br>";*/
            if ( ($value['size'] >= 1) AND ($value['size'] <= 300000) ) {
                echo $value["tmp_name"]."<br>";
                if ( is_uploaded_file($value["tmp_name"]) ) {
                    //echo "upload complit:" .$value["tmp_name"];
                    $uploadfile = $uploaddir . basename($value['name']);
                    if (move_uploaded_file($value['tmp_name'], $uploadfile)) {
                        //echo $value['name'] . " ...Ok!";
                    } else {
                        die("Возможная атака с помощью файловой загрузки!\n");
                    }
                }
            }
        }
    }

    // Возвращает кол-во ошибок загрузки
    public function uploadMultiImg($max_size_inMB, $uploaddir) {
        //var_dump($_FILES);die;
        $max_size = $max_size_inMB * 1024 * 1024;
        $uploadErrors = 0;
        //если каталог не существует - создаём его
        if (!is_dir($uploaddir)) {
            if ( !mkdir($uploaddir, 0777, true) ) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>Не удалось создать директории ...</span>");
               $uploadErrors++;
            }
        }
        
         foreach ($_FILES["image"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["image"]["tmp_name"][$key];
                $size = $_FILES["image"]["size"][$key];
                echo "current file size: ".$size."<br>";
                echo "max-size: ".$max_size."<br><hr>";
                if ($size <= $max_size) {
                    $uploadfile = $uploaddir . basename($_FILES["image"]["name"][$key]);
                    // basename() может спасти от атак на файловую систему;
                    // может понадобиться дополнительная проверка/очистка имени файла
                    move_uploaded_file($tmp_name, $uploadfile);
                } else {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>Ошибка! Размер файла ".
                    $_FILES["image"]["name"][$key]." превышает ".$max_size_inMB."МБ </span>");
                    $uploadErrors++;
                }
            }
        }
        return $uploadErrors;
    }

    public function deleteDirectory($dir) {
        if ( !file_exists($dir) ) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
}
?>