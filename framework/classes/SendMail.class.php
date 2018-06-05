<?php
/**
 * отправка электронной почты
 */
class SendMail
{
    public $mysqli;
 /**
 *    function __construct(){
 *         
 *     }
 */
    //отправка письма на заданные emails
    //$emails - массив с эл.адресами; например: $array = array("client" => $params['email'], "admin" => "ms-projects@mail.ru");
    //$massageText - текст письма, 
    //$from - от кого письмо, 
    //$subject - тема письма
    public function sendToMail($emails = false, $from = false, $subject = false, $massageText = false){
        
        ($from == false) ? $from = Core::app()->config->site_path : $from = $from; //если не указано поле "от кого" - пишем адрес сайта
        
        //проверка входящих значений
        if (!is_array($emails)) {
            die(''.__METHOD__.': Ошибка! Не уазано ни одного E-mail ');
        }
        
        if ($from == false) {
            die(''.__METHOD__.': Ошибка! Попытка отправить письмо без указания отправителя');
        }
        
        if ($massageText == false) {
            die(''.__METHOD__.': Ошибка! Попытка отправить пустое письмо ');
        }
        
        foreach ($emails as $key => $value) {
            $to = $to.''.$key.' <"'.$value.'">,';
        }
        
        $to = substr($to, 0, -1);//удаляем лишнюю запятую вконце
        
        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		//устанавливаем кодировку
        $headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: info \r\n";
        
        //отправляем письмо
        mail ($to, $subject, $massageText, $headers);
        if (mail) {
            return true;
        } else {
            Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] 
                Не удалось отправить письмо пользователю! ... die ", "e");

            die("Ошибка при отправке сообщения...");
        }
        //var_dump($to);
    }
    
    //проверка E-mail
    //в случае успеха возвращает true, в противном случае возвращает массив с найденными ошибками
    public function emailCheck($email) {
        //создаём массив ошибок
    	$error = array(); 
		if (isset($email)) {
    		$email = strip_tags(trim($email));
    		if ($email != "") {
    			$regV = '/^[a-zA-Z0-9\-\_\.]{1,25}\@[a-zA-Z0-9\-\_]{2,15}\.[a-zA-Z0-9]{2,4}$/';
    			$rez = preg_match($regV, $email);
    			if (!$rez) {
    				$error[] = "некорректный E-mail";
    				if (strlen($email) > 46) $error[] = "Больше 46 символов";
    			}
   			} else
    			$error[] = "E-mail не передан!";
		}

		if (count($error) == 0) {
            return true;
		} else
			return $error;
    }
    
}
?>