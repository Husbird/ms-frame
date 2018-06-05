<?php

//Восстановление пароля
class PassRestore extends Authoriz
{
    public $sitePath = false; //текущий адрес сайта
    private $id = false; //id с которым ассоциирован полученный из формы email
    private $name = false; //id с которым ассоциирован полученный из формы email
    private $email = false;
    private $patronymic = false;

    private $pdo = null;
    
    function __construct() {

        $this->pdo = Core::app()->DBase; //получаем метку соединения
        $this->sitePath = Core::app()->config->site_path;
    }
    
    //отсылаем письмо с инструкциями в случае нахождения введённого email в БД
    public function checkAndSend($params=false) {

        $email = strtolower(trim($params['email']));
        Core::app()->setLog(__METHOD__."[".__LINE__."] ищу в БД email идентичный переданному  ...");

        $sql = "SELECT id, name, email, patronymic FROM user WHERE email = :email";

        try {

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':email' => $email
            ));

            $data = $stmt->fetch(); //возвращает fale если ничего не "выбрал" из БД

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

            if ($data["id"] > 0) {
                //если введённый в форме email найден в БД
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                <span id='debugSuccessMsg'>данные пользователя успешно извлечены, искомый email - найден!</span>");

                //инициализация свойств
                $this->id = $data["id"];
                $this->name = $data["name"];
                $this->email = $data["email"];
                $this->patronymic = $data["patronymic"];

                //в случае если пользователь идентифицирован - высылаем ему
                // на указанный email ссылку на востановление пароля
                $hash = $this->generateCode(40);
                Core::app()->setLog(__METHOD__."[".__LINE__."] ставлю куку \"changePassHash\" на 2 часа...");
                setcookie("changePassHash", $hash, time()+3600*2);
                setcookie("changePassId", $this->id, time()+3600*2);
                Core::app()->setLog(__METHOD__."[".__LINE__."] отправляю письмо...");
                $sendMail = $this->sendCheckMail($hash);

                if ($sendMail) {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugSuccessMsg'>письмо отправлено!</span>");
                    header("location:/checkYourMail");//отправляем на главную страницу (встречаем)
                    exit();
                }

            } else {
                //пишем ошибку в сессию для вывода на странице востановления пароля (передаётся сначала в парсер GET)
                $_SESSION['email_not_find'] = '<div class="alert alert-danger" role="alert"><b>Ошибка!</b>
                                                     введённый Вами e-mail на сайте не зарегистрирован.<br>
                                                     Восстановление пароля с использованием электронного адреса: <b>'.$email.'</b> 
                                                     невозможно! :(                                                     
                                                   </div>';
                Core::app()->setLog(__METHOD__."[".__LINE__."] Введённый email - не найден!");
                header("location:/PassRestore");//отправляем на главную страницу (встречаем)
                exit();
            }

        } catch (PDOException $e) {

            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
            return false;
        }
    }
    //сверка хеша из письмя с хешем в куке. В случае совпадения - обновляем пароль пользователю
    public function chengePass($hash, $cookieHash, $cookieId) {
        //var_dump($cookieId);die;
        //$this->mysqli = MsDBConnect::getInstance()->getMysqli(); //получаем метку соединения
        $hash = Core::app()->checkStr($hash);
        $cookieHash = Core::app()->checkStr($cookieHash);
        $id = Core::app()->clearInt($cookieId);

        if ($hash == $cookieHash) {
            //var_dump($cookieId);die;
            $newPass = $this->generateCode(6);
            $passMd5 = md5(md5($newPass));//шифруем пароль для записи в БД
            
            $sql = "UPDATE user SET pass = :pass WHERE id = :id";//подготовка запроса

            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array(
                    ':pass' => $passMd5,
                    ':id' => $id
                ));

                $_SESSION['db_query_num']++;
                $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugSuccessMsg'>пароль пользователя успешно обновлен...</span>");
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugSuccessMsg'>отправляю письмо с новым паролем...</span>");

                $sql = "SELECT name, email, patronymic FROM user WHERE id = :id";

                try {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(array(
                        ':id' => $id
                    ));

                    $data = $stmt->fetch(); //возвращает fale если ничего не "выбрал" из БД

                    $_SESSION['db_query_num']++;
                    $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

                    if ($data) {
                            //var_dump($data); die;
                        $sendRegDataMail = $this->sendRegDataMail($data["email"],$newPass,$data['name'],$data['patronymic']);

                        //ставим куку чтобы передать email в сообщение об успешном восстановлении пароля (на 20 секунд!)
                        $_SESSION['userEmail'] = $data["email"];
                        //var_dump($_SESSION['userEmail']); die;
                        
                        if ($sendRegDataMail) {
                            Core::app()->setLog(__METHOD__."[".__LINE__."] чищу куки с временным хешем и id пользователя...");
                            //очистиь даннные пользователя из cookie
                            setcookie("changePassHash", "",time()-100,"/");
                            setcookie("changePassId", "",time()-100,"/");
                            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        ставлю cookie \"userEmail\" чтобы передать email в сообщение об успешном 
                        восстановлении пароля (на 20 секунд!)...");

                            //ставим куку чтобы передать email в сообщение об успешном восстановлении пароля (на 20 секунд!)
                            $_SESSION['userEmail'] = $data["email"];

                            //var_dump($_COOKIE['userEmail']); die;

                            header("location:/PassRestored");//отправляем на главную страницу (встречаем)
                            exit();
                        }
                    }

                } catch(PDOException $e) {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        <span id='debugErrMsg'>данные пользователя получены не корректно...</span>");
                }

            } catch (PDOException $e) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>пароль пользователя обновить не удалось...</span>");
            }

        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>Хеш не совпал!!!</span>");
            header("location:/PassRestoreError");//отправляем на главную страницу (выводим ошибку)
            exit();
        }
    }
    
    private function sendCheckMail($hash) {
        //Отправляем пользователю ссылку для регистрации
        if (!$this->sitePath) {die(__METHOD__." Ошибка: не указан адрес сайта!");}
        Core::app()->setLog(__METHOD__."[".__LINE__."] получил хеш:</span>");
        //$hash = $this->generateCode(40);
        $fakeHash = md5($hash);
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $this->name);
        /* получатели */
		$to  = "user <".$this->email.">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "Восстановление пароля $this->sitePath";
		
		/* сообщение */
		$message = "
		<html>
		<head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
		</head>
		<body>
		<table>
		<center>
		<h4>Здравствуйте ".$this->name." !</h4> <h5>Вы восстанавливаете доступ к сайту $this->sitePath !</h5>
		</center>
		<tr>
			<td><b>Для для завершения процедуры восстановления доступа, <br>перейдите пожалуйста по следующей ссылке:</b>
            <span style='color:#333'><a href='".$this->sitePath."/СhangePass/".$hash."'>".$this->sitePath."/СhangePass/".$fakeHash."</a></span>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+375 29 ХХХ-96-73 (МТС Беларусь)</i><br>
				<i>+375 25 ХХХ-66-61 (Life Беларусь)</i><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>С уважением, администрация $this->sitePath</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: MSFrame \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if (mail) {
            return true;
        } else {
            die(__METHOD__." Не удалось отправить письмо!");
        }
    }
    
    //отправка пользователю регистрационных данных
    public function sendRegDataMail($email, $pass, $name,$patronymic) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] Отправляю письмо с регистрационными данными...</span>");
        //$sitePath = $this->sitePath;
        /* получатели */
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $name);
        //$patronymic = iconv('UTF-8', 'windows-1251', $patronymic);
        
		$to  = "user <".trim($email).">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "$this->sitePath";
		
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
		<h4>Уважаемый ".$name." ".$patronymic." !</h4> <h5>Вы успешно восстановили доступ к сайту $this->sitePath</h5>
		</center>
		<tr>
			<td><b>Ваши НОВЫЕ регистрационные данные:</b><br>
            E-mail: <b>".$email."</b><br>
            Пароль: <b>".$pass."</b><br>
            <p>Рекомендуем сохранить эти данные в надёжном месте, и не передавать 3-м лицам.<br>
            <b><span style='color:red'>ПОМНИТЕ!</span></b> Администрация сайта никогда не будет спрашивать ваши регистрационные данные!</p>
            
            <center><span style='color:#333'><a href='".$this->sitePath."' title='Перейти на сайт'>Перейти на сайт!</a></span></center>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+375 29 ХХХ-96-73 (МТС Беларусь)</i><br>
				<i>+375 25 ХХХ-66-61 (Life Беларусь)</i><br><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>С уважением администрация $this->sitePath</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: MSFrame \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if (mail) {
            return true;
        } else {
            return false;
        }
    }
}
?>