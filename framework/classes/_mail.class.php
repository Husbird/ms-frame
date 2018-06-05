<?php

/**
 * @author Biblos
 * @copyright 2014
 */

class mail extends My_DBase{
    //public $id; //id авторизованного пользователя
	//public $pass;
	public $sitePath = 'no adress'; //текущий адрес сайта
    
    
    //отправка пользователю регистрационных данных
    public function sendRegDataMail($email, $pass, $name,$lastname){
        $this->sitePath = MSS::app()->config->site_path;
        /* получатели */
        //смена кодировки с utf8 на 1251
        $name = iconv('UTF-8', 'windows-1251', $name);
        $lastname = iconv('UTF-8', 'windows-1251', $lastname);
        
		$to  = "user <".trim($email).">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "info@your-registr";
		
		/* сообщение */
		$message = "
		<html>
		<head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; windows-1251' />
		</head>
		<body>
		<table>
		<center>
		<h4>Уважаемый ".$name." ".$lastname." !</h4> <h5>Вы успешно зарегистрировались на сайте ".$this->sitePath." <br> )</h5>
		</center>
		<tr>
			<td><b>Ваши регистрационные данные:</b><br>
            E-mail: <b>".$email."</b><br>
            Пароль: <b>".$pass."</b><br>
            <p>Рекомендуем сохранить эти данные в надёжном месте, и не передавать 3-м лицам.<br>
            ПОМНИТЕ! Администрация сайта никогда не будет спрашивать ваши регистрационные данные!</p>
            
            <center><span style='color:#333'><a href='".$this->sitePath."' title='Перейти на сайт'>Перейти на сайт!</a></span></center>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+375 29 ХХХ-96-73 (МТС Беларусь)</i><br>
				<i>+375 25 ХХХ-66-61 (Life Беларусь)</i><br><br>
                <i>или напишите на e-mail: ms-projects@mail.ru</i><br><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>С уважением администрация ".$this->sitePath."</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=windows-1251\r\n";
		
		/* дополнительные шапки */
		$headers .= "From: info@site\r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if(mail){
            return true;
        }
    }
    
}

?>