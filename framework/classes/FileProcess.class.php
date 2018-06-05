<?php
//набор функций работы с файлами
class FileProcess
{
    // Универсальный загрузчик картинок
    //запись загружаемого из формы файла на диск сервера:
    public function save_uploaded_files($table_name, $id) {
        switch ($table_name) {
            case "article":
                $uploaddir = './assets/media/images/article/'.$id.'/'; // Желаемая структура папок
                    //$res = $IMGProcess->deleteDirectory('./assets/media/images/'.$this->params['table_name'].'/'.$add); die;
                $IMGProcess = new IMGProcess;
                $upload = $IMGProcess->uploadMultiImg(1, $uploaddir);
                if ($upload === 0) {
                    setcookie("upload_files", "<span id='debugSuccessMsg'>Все данные успешно загружены</span>", time()+3600*2);
                    return true;
                } else {
                    setcookie("upload_files", "<span id='debugErrMsg'>Ошибка загрузки файлов: ".$upload."</span>", time()+3600*2);
                    return false;
                }
            break;

            case "article_cat":
            //var_dump($_FILES);die;
                $uploaddir = './assets/media/images/article_cat/'.$id.'/'; // Желаемая структура папок
                    //$res = $IMGProcess->deleteDirectory('./assets/media/images/'.$this->params['table_name'].'/'.$add); die;
                $IMGProcess = new IMGProcess;
                $upload = $IMGProcess->uploadMultiImg(1, $uploaddir);
                if ($upload === 0) {
                    setcookie("upload_files", "<span id='debugSuccessMsg'>Все данные успешно загружены</span>", time()+3600*2);
                    return true;
                } else {
                    setcookie("upload_files", "<span id='debugErrMsg'>Ошибка загрузки файлов: ".$upload."</span>", time()+3600*2);
                    return false;
                }
            break;

            case "site":
            //var_dump($_FILES);die;
                $uploaddir = './assets/media/images/site/'.$id.'/'; // Желаемая структура папок
                    //$res = $IMGProcess->deleteDirectory('./assets/media/images/'.$this->params['table_name'].'/'.$add); die;
                $IMGProcess = new IMGProcess;
                $upload = $IMGProcess->uploadMultiImg(1, $uploaddir);
                if ($upload === 0) {
                    setcookie("upload_files", "<span id='debugSuccessMsg'>Все данные успешно загружены</span>", time()+3600*2);
                    return true;
                } else {
                    setcookie("upload_files", "<span id='debugErrMsg'>Ошибка загрузки файлов: ".$upload."</span>", time()+3600*2);
                    return false;
                }
            break;
        }
    }

    public function delete_file($path) {
        if (is_file($path)) {
            //return true;
            return unlink($path);
        }
        return false;
    }
    //удаление директории и вложенных директорий и файлов (рекурсия)
    public function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
           foreach($objs as $obj) {
             is_dir($obj) ? removeDirectory($obj) : unlink($obj);
           }
        }
        rmdir($dir);
        return true;
    }

    //запись изображения если передано пример: $save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';
    public function save_audio($save_path = "view/i/audio_rec/", $id) {
        //var_dump($_FILES);
        //проверяем загрузку файла на наличие ошибок
        if($_FILES['uploadfile']['error'] > 0){
            //в зависимости от номера ошибки выводим соответствующее сообщение
            //UPLOAD_MAX_FILE_SIZE - значение установленное в php.ini
            //MAX_FILE_SIZE значение указанное в html-форме загрузки файла
            switch ($_FILES['uploadfile']['error']){
                case 1: echo 'Размер файла превышает допустимое значение UPLOAD_MAX_FILE_SIZE'; break;
                case 2: echo 'Размер файла превышает допустимое значение MAX_FILE_SIZE'; break;
                case 3: echo 'Не удалось загрузить часть файла'; break;
                case 4: echo 'Файл не был загружен'; break;
                case 6: echo 'Отсутствует временная папка.'; break;
                case 7: echo 'Не удалось записать файл на диск.'; break;
                case 8: echo 'PHP-расширение остановило загрузку файла.'; break;
            }
            exit;
        }
        
        //проверяем MIME-тип файла
        if($_FILES['uploadfile']['type'] != 'audio/mp3'){
            echo 'Вы пытаетесь загрузить не audio файл.';
            exit;
        }
        
        //проверяем не является ли загружаемый файл php скриптом,
        //при необходимости можете дописать нужные типы файлов
        $blacklist = array(".php", ".phtml", ".php3", ".php4");
        foreach ($blacklist as $item){
            if(preg_match("/$item\$/i", $_FILES['uploadfile']['name'])){
                echo "Выбраный вами файл не являеться mp3 файлом.";
                exit;
            }
        }
        
        //папка для загрузки
        $uploaddir = $save_path; //'view/i/audio_rec/';
        //новое сгенерированное имя файла
        $newFileName=$id.'.mp3';
        //путь к файлу (папка.файл)
        $uploadfile = $uploaddir.$newFileName;

        //загружаем файл move_uploaded_file
        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
        //echo "Выбранный файл загружен.\n"; 
            return $newFileName;
        } else {
            return false;
         //echo "Ошибка загрузки файла.\n"; 
        }
    }

    // функция копирования файлов (включая вложеные) из папки $source в $res 
    public function copy_all_files($source, $res){ 
        $hendle = opendir($source); // открываем директорию 
        while ($file = readdir($hendle)) { 
            if (($file!=".")&&($file!="..")) { 
                if (is_dir($source."/".$file) == true) { 
                    if(is_dir($res."/".$file)!=true) // существует ли папка 
                        mkdir($res."/".$file, 0777); // создаю папку 
                        copy_files ($source."/".$file, $res."/".$file); 
                } 
                else{ 
                    if(!copy($source."/".$file, $res."/".$file)) {  
                        print ("при копировании файла $file произошла ошибка...<br>\n");  
                    }// end if copy 
                }  
            } // else $file == .. 
        } // end while 
        closedir($hendle); 
    }

    // функция переноса файла из папки $source в $res 
    public function rename_one_file($source, $dir, $res){ 
        $structure = './'.$dir.'';// Желаемая структура папок
        if (!mkdir($structure, 0777, true)) {
             $file_log = 'framework/log/error_log.mss';
             $log_title = 'id: '.$id_last.' | '.$date_reg. ' '.__METHOD__.' Не удалось создать папку'. "\n";
             $file_put = file_put_contents($file_log,$log_title,FILE_APPEND);
        }else{
            //если файл существует - переносим его из $source в $res
            if(file_exists($source)){
                $x = rename($source, $res);
                return $x;
            }else{
                //var_dump($x); die;
                return 'фаил отсутствует во временной папке';    
            }
        }
    }

    // функция копирования создания директории в случае её отсутствия
    //$path - полный путь к директории. В случае существования директории (или успешного её создания) возвращает true, в др случае false
    public function check_and_create_dir($path){ 
        
        if(is_dir($path) == true) {
            return true;
        }else{
            // создаю папку 
            if(mkdir($path, 0777)){
                return true;   
            }else{
                return false;
            }
        }
    }
}
?>